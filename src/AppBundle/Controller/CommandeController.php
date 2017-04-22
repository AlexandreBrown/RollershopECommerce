<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Commande;

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
                    $clientConnecte = $this->getUser();
                    $stripeId = "test"; // TODO : GET STRIPE ID
                    $stripeFingerprint = "test";  // TODO : GET FINGERPRINT
                    $commande = new Commande($clientConnecte->getIdClient(),$panier->getTPS(),$panier->getTVQ(),$stripeId,$stripeFingerprint);
                    $this->ajouterAchatsCommande($commande,$panier);
                    dump($commande);
                    return $this->render('./commande/revue.html.twig',array('commande' => $commande));
                }
            }
            return $this->redirectToRoute('homepage');
        }else{
            return $this->redirectToRoute('inscription');
        }
    }

    private function ajouterAchatsCommande($commande,$panier)
    {
        foreach ($panier->getAchats() as $achat) {
             $commande->ajouterAchat($achat);
        }
    }

}