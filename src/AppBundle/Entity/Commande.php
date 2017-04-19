<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as Doctrine;
/**
* @Doctrine\Entity
* @Doctrine\Table(name="Commandes"
*/
class Commande
{
    // Attributs
    /**
    * @Doctrine\Column(name="idCommande", type="integer")
    * @Doctrine\Id
    * @Doctrine\GeneratedValue(strategy="AUTO")
    */
    private $idCommande;

    /**
    * @Doctrine\Column(name="dateCommande",type="datetime")
    */
    private $dateCommande;
    /**
    * @Doctrine\Column(name="stripeId",type="string",length=255)
    */
    private $stripeId;
    /**
    * @Doctrine\Column(name="stripeFingerprint",type="string",length=255)
    */
    private $stripeFingerprint;
    /**
    * @Doctrine\Column(name="tauxTPS",type="integer")
    */
    private $tauxTPS;
    /**
    * @Doctrine\Column(name="tauxTVQ",type="integer")
    */
    private $tauxTVQ;
    /**
    * @Doctrine\Column(name="etat",type="string",length=50)
    */
    private $etat;
    
    // Constructeur
    public function __construct()
    {

    }

    // Getters
    public function getDateCommande { return $this->dateCommande; }
    public function getStripeId { return $this->stripeId; }
    public function getStripeFingerprint { return $this->stripeFingerprint; }
    public function getTauxTPS { return $this->tauxTPS; }
    public function getTauxTVQ { return $this->tauxTVQ; }
    public function getEtat { return $this->etat; }


    // Setters
    private function setDateCommande($newDateCommande) { $this->dateCommande = $newDateCommande; return $this; }
    private function setStripeId($newStripeId) {$this->stripeId = $newStripeId; return $this; }
    private function setStripeFingerprint($newStripeFingerprint) { $this->stripeFingerprint = $newStripeFingerprint; return $this; }
    private function setTauxTPS($newTauxTPS) {$this->tauxTPS = $newTauxTPS; return $this; }
    private function setTauxTVQ($newTauxTVQ) {$this->tauxTVQ = $newTauxTVQ; return $this; }
    private function setEtat($newEtat) {$this->etat = $newEtat; return $this; }

}