<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

/**
* @Route("/listeSouhaits")
*
*/
class CommandeController extends Controller
{
     /**
     * @Route("/", name="")
     */
    public function indexAction(Request $request)
    {

        return $this->render('.html.twig',array());
    }
}