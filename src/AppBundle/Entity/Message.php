<?php
namespace AppBundle\Entity;

class Message
{
    // Attributs
    private $type;
    private $texte;

    // Constructeur
    public function __construct($type,$texte)
    {
        $this->type = $type;
        $this->texte = $texte;
    }

    // Getters
    public function getType() { return $this->type; }
    public function getTexte() { return $this->texte; }

}

abstract class MessageType
{
    const SUCCESS = "alert-success";
    const INFO = "alert-info";
    const WARNING = "alert-warning";
    const DANGER = "alert-danger";
}