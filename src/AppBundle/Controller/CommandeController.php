<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        $message = null;
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
           $cardName = $request->request->get('cardholder-name'); // On récupère le nom 'action' passé dans le POST
            if(!(empty($cardName))){
                return $this->redirectToRoute('revue');
            }else{
                $message = new Message(MessageType::WARNING,"Le nom ne peut pas être vide");
                return $this->render('./commande/paiement.html.twig',array('message' => $message));
            }
        }else{
            return $this->redirectToRoute('inscription');
        }
    }
     /**
     * @Route("/revue", name="revue")
     */
    public function revueAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('./commande/revue.html.twig');
        }else{
            return $this->redirectToRoute('inscription');
        }
    }
}