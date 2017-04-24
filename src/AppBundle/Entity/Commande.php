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
    * @Doctrine\Column(name="idClient", type="integer")
    * @Doctrine\Id
    */
    private $idClient;
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
    private $etat; // Length de 50 pour laisser la possibilité de mettre autre chose qu'un seul caractère dans le futur. (Évite les problèmes de truncate)

    private $achats;

    
    // Constructeur
    public function __construct($idClient,$TPS,$TVQ)
    {
        $this->idClient = $idClient;
        $this->dateCommande = date("Y-m-d H:i:s");
        $this->tauxTPS = $TPS;
        $this->tauxTVQ = $TVQ;
        $this->etat = "PREP"; // Par défaut une commande est "PREP" , donc Etat::PREPARING ("En préparation")
        $this->achats = array();
    }

    // Getters
    public function getIdClient() { return $this->idClient; }
    public function getDateCommande() { return $this->dateCommande; }
    public function getStripeId() { return $this->stripeId; }
    public function getStripeFingerprint() { return $this->stripeFingerprint; }
    public function getTauxTPS() { return $this->tauxTPS; }
    public function getTauxTVQ() { return $this->tauxTVQ; }
    public function getEtat() { return $this->etat; }
    public function getAchats() { return $this->achats; }


    // Setters
    private function setIdClient($newIdClient) { $this->idClient = $newIdClient; return $this; }
    private function setDateCommande($newDateCommande) { $this->dateCommande = $newDateCommande; return $this; }
    public function setStripeId($newStripeId) {$this->stripeId = $newStripeId; return $this; }
    public function setStripeFingerprint($newStripeFingerprint) { $this->stripeFingerprint = $newStripeFingerprint; return $this; }
    private function setTauxTPS($newTauxTPS) {$this->tauxTPS = $newTauxTPS; return $this; }
    private function setTauxTVQ($newTauxTVQ) {$this->tauxTVQ = $newTauxTVQ; return $this; }
    private function setEtat($newEtat) {$this->etat = $newEtat; return $this; }

    // Méthodes
    public function ajouterAchat($achat)
    {
        $this->achats[] = $achat;
    }

}
abstract class Etat
{
    const PREPARING = "En préparation"; // "PREP"
    const DELIVERY_PENDING = "En attente de livraison"; // "DELI"
    const CANCEL = "Annulée"; // "CANC"
    const DONE = "Terminée"; // "DONE"
}