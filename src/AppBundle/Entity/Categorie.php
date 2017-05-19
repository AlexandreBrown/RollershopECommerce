<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as Doctrine;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @Doctrine\Entity
* @Doctrine\Table(name="Categories")
* @UniqueEntity("nom",message="Le nom saisie existe déjà!")
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
    * @Doctrine\Column(type="string", length=75,unique=true)
    * @Assert\NotBlank(message="Le nom est obligatoire")
    * @Assert\Length(min=2, minMessage="Le nom doit contenir un minimum de {{ limit }} caractères", max=75, maxMessage="Le nom doit contenir un maximum de {{ limit }} caractères")
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

    // Setters
    public function setNom($newNom) { $this->nom = $newNom;return $this; }

}