<?php
namespace AppBundle\Entity;

class Panier
{
    // Attributs
    const TPS = 0.05; // 5% source : http://www.revenuquebec.ca/fr/entreprises/taxes/tpstvhtvq/perception/calcultaxes.aspx
    const TVQ = 0.09975; // 9.975% source : http://www.revenuquebec.ca/fr/entreprises/taxes/tpstvhtvq/perception/calcultaxes.aspx
    const FRAIS_LIVRAISON = 20;
    private $achats;
    
    // Constructeur
    public function __construct()
    {
        $this->achats = array();
    }

    // Getters
    public function getTPS() { return self::TPS; }
    public function getTVQ() { return self::TVQ; }
    private function getFRAIS_LIVRAISON() { return self::FRAIS_LIVRAISON; }
    public function getAchats() { return $this->achats; }

    // Methodes
    // Pour les cas d'utilisations où nous voulons le nombre d'achats et non le nombre de produits
    public function compteAchats() 
    {
        return count($this->achats); // On compte le nombre d'éléments que contient notre tableau achats
    }

    // Pour les cas d'utilisations où nous voulons le nombre de produits total se retrouvant dans le panier
    public function compteNbProduitsTotal()
    {
        $total = 0;
        foreach ($this->getAchats() as $achat) {
            $total += $achat->getQuantite(); // On fait le total du nombre de produits que contient notre panier
        }
        return $total; // On retourne ce total
    }

    public function calculFraisLivraison()
    {
        if($this->compteAchats() === 0){
            return 0; // On retourne 0 s'il n'y a pas d'achat dans notre panier
        }
        return $this->getFRAIS_LIVRAISON(); // Sinon les frais de livraison sont appliqués
    }

    public function calculSousTotal()
    {
        $sousTotal = 0;
        foreach ($this->getAchats() as $achat) {
            $sousTotal += $achat->getPrixAchat();
        }
        return $sousTotal;
    }

    public function ajoutAchat($achat)
    {
        if($this->contientAchat($achat->getProduit()->getIdProduit())){ // Si notre panier contient déjà l'achat
            if($this->updateQtePrixAchat($achat->getProduit()->getIdProduit(),$achat->getQuantite()) == 0){ // On met à jour sa quantité et son prix si le max n'est pas atteint
                // Si la fonction retourne 0 , tout s'est bien passé donc on retourne 0
                return 0;
            }else{
                return 1;// l'article a atteint la limite de quantité
            }
        }else{ // Si l'article n'est pas déjà dans le panier
            $this->achats[] = $achat; // On ajoute l'article dans le panier (à la fin)
        }
        return 0; // Tout s'est bien passé donc on retourne 0
    }

    private function updateQtePrixAchat($idProduit,$nouvelleQte)
    {
        foreach ($this->getAchats() as $a) {
            if($a->getProduit()->getIdProduit() === $idProduit){ // On trouve le produit avec le même id
                if($a->getQuantite() + $nouvelleQte <= 10){ // On vérifie si la quantité actuelle + la nouvelle quantité dépasse 10 si c'est le cas on rentre dans le if
                    $a->setQuantite($a->getQuantite() + $nouvelleQte); // On met à jour la quantité pour celle demandée et on met à jour le prix
                    return 0; // La quantité a été mise à jour sans problème
                }else{
                    return 1; // La quantité maximale a été atteinte
                }
            }
        }
    }

    public function supprimerAchat($idProduit)
    {
        for($i = 0; $i < $this->compteAchats();$i++){ // On parcourt le tableau d'achats
            if($this->achats[$i]->getProduit()->getIdProduit() === $idProduit){ // Si l'achat i à le même idProduit
                unset($this->achats[$i]); // On retire l'achat du tableau d'achats et donc du panier
                $this->achats = array_values($this->achats); // On remet à jour l'index de notre tableau d'achats (rend le tableau lisible)
                return 0;
            }
        }
        return 1; // Achat introuvable
    }

    private function contientAchat($idProduit)
    {
        foreach ($this->getAchats() as $a) {
            if($a->getProduit()->getIdProduit() === $idProduit){
                return true; // Un produit avec le même id était déjà présent dans le panier
            }
        }
        return false; // Aucun produit a le même id dans le panier
    }

    public function calculTPS()
    {
        return $this->calculTaxes($this->getTPS());
    }

    public function calculTVQ()
    {
        return $this->calculTaxes($this->getTVQ());
    }

    private function calculTaxes($taux)
    {
        return ($this->calculFraisLivraison() + $this->calculSousTotal()) * $taux;
    }

    public function calculTotal()
    {
        return $this->calculSousTotal() + $this->calculFraisLivraison() + $this->calculTPS() + $this->calculTVQ();
    }

}