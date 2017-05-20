<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Commande;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('etatToVerbose', array($this, 'etatToVerbose')),
        );
    }

    public function etatToVerbose($etat)
    {
        $verbose = Commande::EtatToVerbose($etat);

        return $verbose;
    }
}