<?php
namespace AppBundle\Entity;

class ListeSouhaits
{
    // Attributs
    private $souhaits;

    // Constructeur
    public function __construct()
    {
        $this->souhaits = array();
    }

    // Getters
    public function getSouhaits() { return $this->souhaits; }

    // Methodes
    public function compteSouhaits()
    {
        return count($this->souhaits); // On compte le nombre d'éléments que contient notre tableau souhaits
    }

    public function ajoutSouhait($produit)
    {
        try{
            if(!$this->contientSouhait($produit->getIdProduit())){ // Si notre liste de souhaits ne contient pas déjà l'article
                $this->souhaits[] = $produit; // On ajoute l'article dans le panier
                return 0; // Tout s'est bien déroulé
            }
            return 1; // Le souhait est déjà présent dans la liste
        }catch(\Exception $e){
            return $this->redirectToRoute('error'); // Erreur conçernant l'idProduit
        }
    }

    public function supprimerSouhait($idProduit)
    {
        for($i = 0; $i< $this->compteSouhaits();$i++){
            if($this->souhaits[$i]->getIdProduit() === $idProduit){
                unset($this->souhaits[$i]); // On retire l'achat du tableau d'achats et donc du panier
                $this->souhaits = array_values($this->souhaits);
                return 0; // Tout s'est bien déroulé
            }
        }
        return 1; // Aucun souhait avec l'id demandé n'a été trouvé
    }
    private function contientSouhait($idProduit)
    {
        foreach ($this->getSouhaits() as $p) {
            if($p->getIdProduit() == $idProduit){
                return true; // Un produit avec le même id était déjà présent dans la liste de souhaits
            }
        }
        return false; // Aucun produit a le même id dans la liste de souhaits
    }

}