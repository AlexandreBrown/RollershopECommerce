<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Etat;
use AppBundle\Entity\Panier;

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
            return $this->redirectToRoute('panier.index');
        }
            return $this->redirectToRoute('connexion');
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
                        $stripeToken = $post["stripeToken"]; // on stock le stripeToken
                        $this->get('session')->set('stripeToken',$stripeToken);
                        return $this->render('./commande/revue.html.twig');
                    }else{
                        if(isset($post["placeOrder"])){ // Si le client a placé une commande
                            $stripeToken = $session->get('stripeToken'); // On récupère le stripeToken
                            if($stripeToken !== ""){
                                $charge = $this->createCharge($panier->calculTotal(),$stripeToken);
                                $commande = new Commande(new \DateTime('now'),
                                                         $charge['source']['id'],
                                                         $charge['source']['fingerprint'],
                                                         $panier->getTPS(),
                                                         $panier->getTVQ(),
                                                         Etat::PREPARING);
                                $this->ajouterCommandeEnBD($commande,$clientConnecte);
                                $this->updateQuantiteStock($panier->getAchats());
                                $this->ajouterAchatsEnBD($commande,$session);
                                $session->remove('stripeToken'); // supprime le token
                                $session->remove('panier'); // vide le panier
                                $session->set('panier', new Panier()); // On créer un panier vide
                                return $this->redirectToRoute('commandes');
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
            return $this->redirectToRoute('panier.index');
        }else{
            return $this->redirectToRoute('connexion');
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
            return $charge;
    }

    private function ajouterCommandeEnBD($commande,$clientConnecte)
    {
      $manager = $this->getDoctrine()->getManager();

      $commande->setClient($clientConnecte);
      // On sauvegarde la commande dans la base de données
      $manager->persist($commande);

      $manager->flush();

    }

    private function updateQuantiteStock($achats)
    {
      foreach($achats as $achat)
      {
        $this->updateQuantiteProduit($achat->getIdProduit(),($achat->getQuantite()*1));
      }
    }

    private function updateQuantiteProduit($idProduit,$qteCommande)
    {
      $manager = $this->getDoctrine()->getManager();
      $produit = $manager->getRepository('AppBundle:Produit')->find($idProduit);

      if (!$produit) {
          throw $this->createException(
              'Aucun produit pour id '.$idProduit
          );
      }

      $produit->setQteStock($produit->getQteStock() - $qteCommande);
      $manager->persist($produit);
      $manager->flush();
    }

    private function ajouterAchatsEnBD($commande,$session){
      $panier = $session->get('panier');
      $achats = $panier->getAchats();
      $manager = $this->getDoctrine()->getManager();
      foreach($achats as $achat){
        $achat->setCommande($commande);
        // On sauvegarde l'achat dans la base de données
        $manager->persist($achat);

        $manager->flush();
      }
    }

}