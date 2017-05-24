<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Etat;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('etatFormatVerboseHTML', array($this, 'etatFormatVerboseHTML')),
        );
    }

    public function etatFormatVerboseHTML($etat)
    {
        switch($etat)
        {
            case Etat::PENDING :
                return "<span class='label label-warning'>En attente</span>";
            break;
            case Etat::PREPARING :
                return "<span class='label preparing'>En préparation</span>";
            break;
            case Etat::SENT :
                return "<span class='label label-primary'>Envoyée</span>";
            break;
            case Etat::CLOSED :
                return "<span class='label label-success'>Fermée</span>";
            break;
            default:
                return "Inconnu"; // Si l'utilisateur de la classe a mal défini l'état
            break;
        }
    }
}