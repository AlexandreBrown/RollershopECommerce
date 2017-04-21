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
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $session = $request->getSession();
            if ($session->has('panier')) { // On s'assure que le panier existe
                $panier = $session->get('panier');
                if($panier->compteAchats() > 0){
                    return $this->render('./commande/paiement.html.twig');
                }
            }
            return $this->redirectToRoute('homepage');
        }
            return $this->redirectToRoute('inscription');
    }
     /**
     * @Route("/revue", name="revue")
     */
    public function revueAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $session = $request->getSession();
            if ($session->has('panier')) { // On s'assure que le panier existe
                $panier = $session->get('panier');
                if($panier->compteAchats() > 0){
                    return $this->render('./commande/revue.html.twig');
                }
            }
            return $this->redirectToRoute('homepage');
        }else{
            return $this->redirectToRoute('inscription');
        }
    }
}