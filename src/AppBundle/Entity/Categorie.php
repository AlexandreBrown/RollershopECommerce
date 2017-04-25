<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as Doctrine;


/**
* @Doctrine\Entity
* @Doctrine\Table(name="Categories")
*/
class Categorie
{
    // Attributs
    /**
    * @Doctrine\Column(name="idCategorie", type="integer")
    * @Doctrine\Id
    * @Doctrine\GeneratedValue(strategy="AUTO")
    */
    private $idCategorie;
    /**
    * @Doctrine\Column(type="string", length=30)
    */
    private $nom;
     /**
     * @Doctrine\OneToMany(targetEntity="Produit", mappedBy="Categorie")
     */
    private $produits;

    // Constructeur
    public function __construct($tab)
    {
        $this->idCategorie = $tab['idCategorie'];
        $this->nom = $tab['nom'];
        $this->produits = new ArrayCollection();
    }

    // Getters
    public function getIdCategorie() { return $this->idCategorie; }
    public function getNom() { return $this->nom; }

}