<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as Doctrine;
/**
* @Doctrine\Entity
* @Doctrine\Table(name="Produits")
*/
class Produit
{
    // Attributs
    /**
    * @Doctrine\Column(name="idProduit", type="integer")
    * @Doctrine\Id
    * @Doctrine\GeneratedValue(strategy="AUTO")
    */
    private $idProduit;

    private $categorie;
    /**
    * @Doctrine\Column(type="string", length=50)
    */
    private $nom;
    /**
    * @Doctrine\Column(type="decimal",precision=10, scale=2)
    */
    private $prix;
    /**
    * @Doctrine\Column(name="qteStock",type="integer")
    */
    private $qteStock;
    /**
    * @Doctrine\Column(name="qteMinimale",type="integer")
    */
    private $qteMinimale;
    /**
    * @Doctrine\Column(name="descriptionCourte",type="string", length=255)
    */
    private $descriptionCourte;
    /**
    * @Doctrine\Column(type="string", length=1000)
    */
    private $description;

     /**
     * Un produit a plusieurs achats
     * @Doctrine\OneToMany(targetEntity="Achat", mappedBy="Produit")
     */
    private $achats;

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
        $this->achats = new ArrayCollection();
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

    // Setters
    public function setQteStock($newQte) { $this->qteStock = $newQte; return $this; }
    
}
