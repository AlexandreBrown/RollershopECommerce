<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

/**
* @Route("/commande")
*
*/
class CommandeController extends Controller
{
     /**
     * @Route("/paiement", name="paiement")
     */
    public function paiementAction(Request $request)
    {

        return $this->render('./commande/paiement.html.twig');
    }
     /**
     * @Route("/revue", name="revue")
     */
    public function revueAction(Request $request)
    {

        return $this->render('./commande/revue.html.twig');
    }
}