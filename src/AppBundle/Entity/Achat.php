<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as Doctrine;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @Doctrine\Entity
* @Doctrine\Table(name="Achats")
*/
class Achat
{
    // Attributs
    /**
    * @Doctrine\Column(name="idAchat", type="integer")
    * @Doctrine\Id
    * @Doctrine\GeneratedValue(strategy="AUTO")
    */
    private $idAchat;
    /**
    * @Doctrine\Column(name="idProduit",type="integer")
    */
    private $idProduit;

    private $produit;
    /**
    * @Doctrine\Column(name="quantite",type="integer")
    * @Assert\NotBlank()
    */
    private $quantite;
    /**
    * @Doctrine\Column(name="prixAchat",type="decimal")
    * @Assert\NotBlank()
    */
    private $prixAchat;

    /**
     * Plusieurs achats on une commande
    * @Doctrine\ManyToOne(targetEntity="Commande", inversedBy="achats")
    * @Doctrine\JoinColumn(name="idCommande", referencedColumnName="idCommande", nullable=false)
    */
    private $commande;

    
    // Constructeur
    public function __construct($produit)
    {
        $this->idProduit = $produit->getIdProduit();
        $this->produit = $produit;
        $this->quantite = 1;
        $this->prixAchat = $this->getProduit()->getPrix() * $this->getQuantite();
    }

    // Getters
    public function getIdAchat() { return $this->idAchat; }
    public function getIdCommande() { return $this->idCommande; }
    public function getIdProduit() { return $this->idProduit; }
    public function getProduit() { return $this->produit; }
    public function getQuantite() { return $this->quantite; }
    public function getPrixAchat(){ return $this->prixAchat; }

    // Setters
    public function setCommande($commande) { $this->commande = $commande; return $this; }
    public function setQuantite($quantite) 
    { 
        $this->quantite = $quantite; 
        $this->setPrixAchat(); // Le prix est automatiquement mis à jour suite à un changement au niveau de la quantié ,car on veut éviter les erreurs de programmations (oublier de call la fonction setPrixAchat etc.)
        return $this; 
    }

    private function setPrixAchat() // Fonction qui met à jour le prix d'un achat et on ne fai pas de "return $this;" ,car cette fonction ne doit pas être chainable ou utilisé par l'utilisateur de la classe
    {
        $this->prixAchat = $this->getProduit()->getPrix() * $this->getQuantite();
    }

    private function setIdCommande($newIdCommande)
    {
        $this->idCommande = $newIdCommande;
    }

}