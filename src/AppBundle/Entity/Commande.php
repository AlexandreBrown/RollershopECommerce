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
    * @Doctrine\Column(name="etat",type="string",length=20)
    */
    private $etat; // Length de 20 pour laisser la possibilitée de mettre autre chose qu'un seul caractère dans le futur. (Évite les problèmes de "truncate")

    
    // Constructeur
    public function __construct($idClient,$date,$stripeId,$stripeFingerprint,$TPS,$TVQ,$etat)
    {
        $this->idClient = $idClient;
        $this->dateCommande = $date;
        $this->stripeId = $stripeId;
        $this->stripeFingerprint = $stripeFingerprint;
        $this->tauxTPS = $TPS;
        $this->tauxTVQ = $TVQ;
        $this->etat = $etat;
    }

    // Getters
    public function getIdCommande() { return $this->idCommande; }
    public function getIdClient() { return $this->idClient; }
    public function getDateCommande() { return $this->dateCommande; }
    public function getStripeId() { return $this->stripeId; }
    public function getStripeFingerprint() { return $this->stripeFingerprint; }
    public function getTauxTPS() { return $this->tauxTPS; }
    public function getTauxTVQ() { return $this->tauxTVQ; }
    public function getEtat() { return $this->etat; }


    // Setters
    public function setIdCommande($newIdCommande) { $this->idCommande = $newIdCommande; return $this; }
    private function setIdClient($newIdClient) { $this->idClient = $newIdClient; return $this; }
    private function setDateCommande($newDateCommande) { $this->dateCommande = $newDateCommande; return $this; }
    private function setStripeId($newStripeId) {$this->stripeId = $newStripeId; return $this; }
    private function setStripeFingerprint($newStripeFingerprint) { $this->stripeFingerprint = $newStripeFingerprint; return $this; }
    private function setTauxTPS($newTauxTPS) {$this->tauxTPS = $newTauxTPS; return $this; }
    private function setTauxTVQ($newTauxTVQ) {$this->tauxTVQ = $newTauxTVQ; return $this; }
    private function setEtat($newEtat) {$this->etat = $newEtat; return $this; }

    // Méthodes
    public function EtatToVerbose()
    {
        switch($this->getEtat())
        {
            case Etat::PENDING :
                return "En attente";
            break;
            case Etat::PREPARING :
                return "En préparation";
            break;
            case Etat::DELIVERY :
                return "En livraison";
            break;
            case Etat::CANCEL :
                return "Annulée";
            break;
            case Etat::DELIVERED :
                return "Livrée avec succès";
            break;
            default:
                return "Inconnu"; // Si l'utilisateur de la classe a mal défini l'état
            break;
        }
    }

}
abstract class Etat
{
    const PENDING = "PEND";
    const PREPARING = "PREP";
    const DELIVERY = "DELI";
    const CANCEL = "CANC";
    const DELIVERED = "DONE";
}