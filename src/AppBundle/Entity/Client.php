<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as Doctrine;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
* @Doctrine\Entity
* @Doctrine\Table(name="Clients", uniqueConstraints={@Doctrine\UniqueConstraint(name="UK_Courriel", columns={"courriel"})})
* @UniqueEntity(fields="courriel", message="Ce courriel est déjà utilisé")
*/
class Client implements UserInterface
{
    // Attributs

    /**
    * @Doctrine\Column(name="idClient", type="integer")
    * @Doctrine\Id
    * @Doctrine\GeneratedValue(strategy="AUTO")
    */
    private $idClient;

    /**
    * @Doctrine\Column(type="string", length=30)
    * @Assert\NotBlank(message="Le nom est obligatoire")
    * @Assert\Length(min=2, minMessage="Votre nom doit contenir un minimum de {{ limit }} caractères", max=30, maxMessage="Votre nom doit contenir un maximum de {{ limit }} caractères")
    */
    private $nom;

    /**
    * @Doctrine\Column(type="string", length=30)
    * @Assert\NotBlank(message="Le prénom est obligatoire")
    * @Assert\Length(min=2, minMessage="Votre prénom doit contenir un minimum de {{ limit }} caractères", max=30, maxMessage="Votre prénom doit contenir un maximum de {{ limit }} caractères")
    */
    private $prenom;

    /**
    * @Doctrine\Column(type="string", length=1)
    * @Assert\NotBlank(message="Le genre est obligatoire.")
    * @Assert\Choice(choices = {"F", "M"}, strict = true)
    */
    private $genre;

    /**
    * @Doctrine\Column(type="string", length=100, unique=true)
    * @Assert\NotBlank(message="Le courriel est obligatoire")
    * @Assert\Email(message="Le courriel {{ value }} n'est pas valide.", checkMX = true, checkHost = true)
    * @Assert\Length(max=100, maxMessage="Le courriel doit contenir un maximum de {{ limit }} caractères.")
    */
    private $courriel;

    /**
    * @Doctrine\Column(name="motPasse", type="string", length=128)
    * @Assert\NotBlank(message="Le mot de passe est obligatoire")
    */
    private $motPasse;

    /**
    * @Doctrine\Column(type="string", length=64)
    */
    private $salt;

    /**
    * @Doctrine\Column(type="string", length=100)
    * @Assert\NotBlank(message="L'adresse est obligatoire")
    * @Assert\Length(min=5, minMessage="L'adresse doit contenir un minimum de {{ limit }} caractères", max=100, maxMessage="L'adresse doit contenir un maximum de {{ limit }} caractères")
    */
    private $adresse;

    /**
    * @Doctrine\Column(type="string", length=30)
    * @Assert\NotBlank(message="La ville est obligatoire")
    * @Assert\Length(min=3, minMessage="La ville doit contenir un minimum de {{ limit }} caractères", max=30, maxMessage="La ville doit contenir un maximum de {{ limit }} caractères")
    */
    private $ville;

    /**
    * @Doctrine\Column(name="codePostal", type="string", length=6)
    * @Assert\Regex("/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[ABCEGHJKLMNPRSTVWXYZ]{1}\d{1}[ABCEGHJKLMNPRSTVWXYZ]{1}\d{1}$/", message="Le code postal est invalide")
    * @Assert\NotBlank(message="Le code postal est obligatoire.")
    */
    private $codePostal; // Source de la REGEX : http://stackoverflow.com/questions/1146202/canadian-postal-code-validation

    /**
    * @Doctrine\Column(type="string", length=2)
    * @Assert\NotBlank(message="La province est obligatoire.")
    * @Assert\Choice(choices = {"NL", "PE","NS","NB","QC","ON","MB","SK","AB","BC","YT","NT","NU"}, strict = true, message="Province :")
    */
    private $province;

    /**
    * @Doctrine\Column(name="telephone", type="string", length=10)
    * @Assert\Regex("/^\d{10}$/", message="Le téléphone est invalide")
    * @Assert\NotBlank(message="Le téléphone est obligatoire.")
    */
    private $telephone;

    /**
     * Un client a plusieurs commandes
     * @Doctrine\OneToMany(targetEntity="Commande", mappedBy="Client")
     */
    private $commandes;

    // Constructeur
    public function __construct()
    {
        $this->setSalt(md5(uniqid(null, true)));
        $this->commandes = new ArrayCollection();
    }

    // Getters
    public function getIdClient() { return $this->idClient; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getCourriel() { return $this->courriel; }
    public function getMotPasse() { return $this->motPasse; }
    public function getGenre() { return $this->genre; }
    public function getAdresse() { return $this->adresse; }
    public function getVille() { return $this->ville; }
    public function getCodePostal() { return $this->codePostal; }
    public function getProvince() { return $this->province; }
    public function getTelephone() { return $this->telephone; }

    //Méthodes membres de l'interface UserInterface
    public function getRoles() { return array('ROLE_USER'); } //TODO: Sera à modifier lors de l'ES
    public function getPassword() { return $this->getMotPasse(); }
    public function getSalt() { return $this->salt; }
    public function getUsername() { return $this->getCourriel(); }
    public function eraseCredentials() {  /*$this->setMotPasse("");*/ }

    // Setters
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setPrenom($prenom) { $this->prenom = $prenom; return $this; }
    public function setCourriel($courriel) { $this->courriel = $courriel; return $this; }
    public function setGenre($genre) { $this->genre = $genre; return $this; }
    public function setAdresse($adresse) { $this->adresse = $adresse; return $this; }
    public function setVille($ville) { $this->ville = $ville; return $this; }
    public function setCodePostal($codePostal) { $this->codePostal = $codePostal; return $this; }
    public function setProvince($province) { $this->province = $province; return $this; }
    public function setTelephone($telephone) { $this->telephone = $telephone; return $this; }
    public function setMotPasse($motPasse) { $this->motPasse = $motPasse; return $this; }
    public function setSalt($salt) { $this->salt = $salt; return $this; }

}