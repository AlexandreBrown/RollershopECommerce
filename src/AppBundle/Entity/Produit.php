<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as Doctrine;
use Symfony\Component\Validator\Constraints as Assert;
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
    /**
    * @Doctrine\ManyToOne(targetEntity="Categorie", inversedBy="produits")
    * @Doctrine\JoinColumn(name="idCategorie", referencedColumnName="idCategorie", nullable=false)
    */
    private $categorie;
    /**
    * @Doctrine\Column(type="string", length=50)
    * @Assert\NotBlank(message="Le nom est obligatoire")
    * @Assert\Length(min=2, minMessage="Le nom doit contenir un minimum de {{ limit }} caractères", max=50, maxMessage="Le nom doit contenir un maximum de {{ limit }} caractères")
    */
    private $nom;
    /**
    * @Doctrine\Column(type="decimal",precision=10, scale=2)
    * @Assert\NotBlank(message="Le prix est obligatoire")
    */
    private $prix;
    /**
    * @Doctrine\Column(name="qteStock",type="integer")
    * @Assert\NotBlank(message="La quantité en stock est obligatoire")
    */
    private $qteStock;
    /**
    * @Doctrine\Column(name="qteMinimale",type="integer")
    * @Assert\NotBlank(message="La quantité minimale en stock est obligatoire")
    */
    private $qteMinimale;
    /**
    * @Doctrine\Column(name="descriptionCourte",type="string", length=255,nullable=true)
    * @Assert\Length(max=255, maxMessage="La description courte doit contenir un maximum de {{ limit }} caractères")
    */
    private $descriptionCourte;
    /**
    * @Doctrine\Column(type="string", length=1000,nullable=true)
    * @Assert\Length(max=1000, maxMessage="La description doit contenir un maximum de {{ limit }} caractères")
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
    public function setNom($newNom) { $this->nom = $newNom; return $this; }
    public function setPrix($newPrix) { $this->prix = $newPrix; return $this; }
    public function setQteMinimale($newQteMin) { $this->qteMinimale = $newQteMin; return $this; }
    public function setDescriptionCourte($newDescCourte) { $this->descriptionCourte = $newDescCourte; return $this; }
    public function setDescription($newDesc) { $this->description = $newDesc; return $this; }
    public function setQteStock($newQte) { $this->qteStock = $newQte; return $this; }
    public function setCategorie($categorie) { $this->categorie = $categorie; return $this; }
    
}
