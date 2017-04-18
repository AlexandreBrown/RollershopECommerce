<?php
namespace AppBundle\Entity;

class Commande
{
    // Attributs
    private $dateCommande;
    private $stripeId;
    private $stripeFingerprint;
    private $tauxTPS;
    private $tauxTVQ;
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