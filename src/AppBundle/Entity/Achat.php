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
    * @Doctrine\ManyToOne(targetEntity="Produit", inversedBy="achats")
    * @Doctrine\JoinColumn(name="idProduit", referencedColumnName="idProduit", nullable=false)
    */
    private $produit;
    /**
    * @Doctrine\Column(name="quantite",type="integer")
    * @Assert\NotBlank()
    */
    private $quantite;
    /**
    * @Doctrine\Column(name="prixAchat",type="decimal",precision=10, scale=2)
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
        //$this->idProduit = $produit->getIdProduit();
        $this->produit = $produit;
        $this->quantite = 1;
        $this->prixAchat = $this->getProduit()->getPrix();
    }

    // Getters
    public function getIdAchat() { return $this->idAchat; }
    //public function getIdProduit() { return $this->idProduit; }
    public function getProduit() { return $this->produit; }
    public function getQuantite() { return $this->quantite; }
    public function getPrixAchat(){ return $this->prixAchat; }

    // Setters
    public function setCommande($commande) { $this->commande = $commande; return $this; }
    public function setProduit($produit) { $this->produit = $produit; return $this; }
    public function setQuantite($quantite) { $this->quantite = $quantite; return $this; }
    private function setPrixAchat($prix) { $this->prixAchat = $prix; return $this; }

    // Méthodes
    public function getTotalPrixAchatQuantite() { return ($this->getQuantite() * $this->getPrixAchat()); }

}