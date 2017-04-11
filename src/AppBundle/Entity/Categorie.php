<?php
namespace AppBundle\Entity;

class Categorie
{
    // Attributs
    private $idCategorie;
    private $nom;

    // Constructeur
    public function __construct($tab)
    {
        $this->idCategorie = $tab['idCategorie'];
        $this->nom = $tab['nom'];
    }

    // Getters
    public function getIdCategorie() { return $this->idCategorie; }
    public function getNom() { return $this->nom; }

}