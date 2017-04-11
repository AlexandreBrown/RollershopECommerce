<?php
namespace AppBundle\Entity;

class Produit
{
    // Attributs
    private $idProduit;
    private $categorie;
    private $nom;
    private $prix;
    private $qteStock;
    private $qteMinimale;
    private $descriptionCourte;
    private $description;

    // Constructeur
    public function __construct($tab,$categorie)
    {
        $this->idProduit = $tab['idProduit'];
        $this->categorie = $categorie;
        $this->nom = $tab['nom'];
        $this->prix = $tab['prix'];
        $this->qteStock = $tab['qteStock'];
        $this->qteMinimale = $tab['qteMinimale'];
        $this->descriptionCourte = $tab['descriptionCourte'];
        $this->description = $tab['description'];
    }

    // Getters
    public function getIdProduit() { return $this->idProduit; }
    public function getCategorie() { return $this->categorie; }
    public function getNom() { return $this->nom; }
    public function getPrix() { return $this->prix; }
    public function getQteStock() { return $this->qteStock; }
    public function getQteMinimale() { return $this->qteMinimale; }
    public function getDescriptionCourte() { return $this->descriptionCourte; }
    public function getDescription() { return $this->description; }
    
}
