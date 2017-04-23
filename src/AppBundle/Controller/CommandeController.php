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
     * @Method({"POST"})
     */
    public function revueAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $post = $request->request->all();
            $session = $request->getSession();
            if ($session->has('panier')) { // On s'assure que le panier existe
                $panier = $session->get('panier');
                if($panier->compteAchats() > 0){
                    $clientConnecte = $this->getUser();
                    if(isset($post["stripeToken"])){ // Si nous venons d'être redirigé à partir de la page de paiement
                        $stripeId = $post["stripeToken"]; // on stock le stripeToken
                        $this->get('session')->set('stripeId',$stripeId);
                        return $this->render('./commande/revue.html.twig');
                    }else{
                        if(isset($post["placeOrder"])){ // Si le client a placé une commande
                            $stripeId = $this->get('session')->get('stripeId'); // On récupère le stripeId
                            if($stripeId !== ""){
                                $commande = new Commande($clientConnecte->getIdClient(),$panier->getTPS(),$panier->getTVQ(),$stripeId);
                                $this->ajouterAchatsCommande($commande,$panier);
                                $this->createCharge($panier->calculTotal(),$stripeId);
                                $this->get('session')->remove('stripeId');
                            }
                            else{
                                return $this->redirectToRoute('error');
                            }
                        }else{                  
                        return $this->redirectToRoute('paiement'); // Si l'utilisateur n'a pas identifié son moyen de paiement on le redirige vers la page de méthode de paiement
                        }
                    }
                    
                }
            }
            return $this->redirectToRoute('homepage');
        }else{
            return $this->redirectToRoute('inscription');
        }
    }

    private function createCharge($amount,$token)
    {
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey("sk_test_sGvHbTfAPF6Cvgp685LuCqrW");
            $amount = $amount * 100;
            $amount = round($amount);
            // Charge the user's card:
            $charge = \Stripe\Charge::create(array(
              "amount" => $amount,
              "currency" => "cad",
              "description" => "Rollershop",
              "source" => $token,
            ));
    }

    private function ajouterAchatsCommande($commande,$panier)
    {
        foreach ($panier->getAchats() as $achat) {
             $commande->ajouterAchat($achat);
        }
    }

}