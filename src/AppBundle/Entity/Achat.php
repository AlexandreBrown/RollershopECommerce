<?php
namespace AppBundle\Entity;

class Achat
{
    // Attributs
    private $produit;
    private $quantite;
    private $prixAchat;
    
    // Constructeur
    public function __construct($produit)
    {
        $this->produit = $produit;
        $this->quantite = 1;
        $this->prixAchat = $this->getProduit()->getPrix() * $this->getQuantite();
    }

    // Getters
    public function getProduit() { return $this->produit; }
    public function getQuantite() { return $this->quantite; }
    public function getPrixAchat(){ return $this->prixAchat; }

    // Setters
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

}