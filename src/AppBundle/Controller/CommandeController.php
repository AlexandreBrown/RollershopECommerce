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
            return $this->redirectToRoute('panier.index'); // Si le panier est vide ou n'existe pas on redirige l'utilisateur vers son panier
        }
            return $this->redirectToRoute('connexion'); // Si l'utilisateur tente de payer sans être connecté il est redirigé vers une page de connexion
    }
     /**
     * @Route("/revue", name="revue")
     * @Method({"POST"})
     */
    public function revueAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $post = $request->request->all(); // On récupère les éléments du post
            $session = $request->getSession();
            if ($session->has('panier')) { // On s'assure que le panier existe
                $panier = $session->get('panier');
                if($panier->compteAchats() > 0){ // On vérifie que le panier n'est pas vide
                    $clientConnecte = $this->getUser(); // On récupère le compte connecté
                    if(isset($post["stripeToken"])){ // Si nous avons un strike token qui n'est pas null (valide si nous venons d'être redirigé de la page de paiement)
                        $stripeToken = $post["stripeToken"]; // on stock le stripeToken
                        $this->get('session')->set('stripeToken',$stripeToken); // On stock le stripe token comme variable de session
                        return $this->render('./commande/revue.html.twig'); // On affiche la page de revue
                    }else{ // Si le stripe token n'est pas dans le post
                        if(isset($post["placeOrder"])){ // Si le client a placé une commande
                            $stripeToken = $session->get('stripeToken'); // On récupère le stripeToken (variable session)
                            if($stripeToken !== ""){ // On vérifie que le token existe
                              try{
                                // On charge la carte de crédit du client
                                $charge = $this->createCharge($panier->calculTotal(),$stripeToken); 

                                // On crée une commande
                                $commande = new Commande(new \DateTime('now'),
                                                         $charge['source']['id'],
                                                         $charge['source']['fingerprint'],
                                                         $panier->getTPS(),
                                                         $panier->getTVQ(),
                                                         Etat::PREPARING);

                                // On ajoute la commande en BD
                                $this->ajouterCommandeEnBD($commande,$clientConnecte);
                                // On met à jour le niveau de stock des produits affectés
                                $this->updateQuantiteStock($panier->getAchats());
                                // On ajoute les achats en BD
                                $this->ajouterAchatsEnBD($commande,$session);
                                $session->remove('stripeToken'); // On supprime le token (un token doit pouvoir être utilisé pour une transaction seulement)
                                $session->remove('panier'); // On vide le panier
                                $session->set('panier', new Panier()); // On créer un panier vide
                                return $this->redirectToRoute('commandes'); // On redirige l'utilisateur vers ses commandes
                              }catch(\Exception $e){
                                if($session->has('stripeToken')){  // Si l'utilisateur a tenté d'utiliser le même token pour une autre transaction 
                                  $session->remove('stripeToken'); // On supprime le token
                                }
                                return $this->redirectToRoute('paiement'); // On redirige l'utilisateur vers la page de paiement
                              }
                            }
                            else{
                                // Si l'utilisateur voulait effectuer une commande mais que le token stripe n'existe plus au moment de la transaction ,
                                //  on redirige l'utilisateur vers une page d'erreur
                                return $this->redirectToRoute('error');
                            }
                        }else{                  
                        return $this->redirectToRoute('paiement'); // Si l'utilisateur n'a pas identifié son moyen de paiement on le redirige vers la page de méthode de paiement
                        }
                    }
                    
                }
            }
            return $this->redirectToRoute('panier.index'); // Si le panier n'existe pas ou est vide
        }else{
            return $this->redirectToRoute('connexion'); // Si l'utilisateur n'est pas connecté on le redirige à l'écran de connexion
        }
    }

    private function createCharge($amount,$token)
    {
      // Pour plus d'informations : https://stripe.com/docs/charges
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
      // On met à jour la quantité en stock de tous les produits présents dans tous les achats de la commande
      foreach($achats as $achat)
      {
        $this->updateQuantiteProduit($achat->getProduit()->getIdProduit(),($achat->getQuantite()*1));
      }
    }

    private function updateQuantiteProduit($idProduit,$qteCommande)
    {
      $manager = $this->getDoctrine()->getManager();

      // On trouve le produit qui à un idProduit correspondant
      $produit = $manager->getRepository('AppBundle:Produit')->find($idProduit);

      if (!$produit) {
          throw $this->createException(
              'Aucun produit pour id '.$idProduit
          );
      }

      // On met à jour sa quantité en stock
      $produit->setQteStock($produit->getQteStock() - $qteCommande);
    }

    private function ajouterAchatsEnBD($commande,$session){
      // On récupère le panier
      $panier = $session->get('panier');
      // On récupère les achats du panier
      $achats = $panier->getAchats();

      $manager = $this->getDoctrine()->getManager();


      foreach($achats as $achat){
        // On assigne une commande à un achat
        $achat->setCommande($commande);
        // On assigne les produits de l'achat conçerné
        $this->ajouterProduitAchat($achat);
        // On sauvegarde l'achat dans la base de données
        $manager->persist($achat);

        $manager->flush();
      }
    }

    private function ajouterCategorieProduit($produit)
    {
      $manager = $this->getDoctrine()->getManager();
      // On trouve la catégorie correspondante
      $categorie = $manager->getRepository('AppBundle:Categorie')->find($produit->getCategorie()->getIdCategorie());

      if (!$categorie) {
          throw $this->createException(
              'Aucune catégorie pour id '.$idProduit
          );
      }
      // On assigne la catégorie trouvée avec le produit actuel
      $produit->setCategorie($categorie);
    }

    private function ajouterProduitAchat($achat)
    {
      $manager = $this->getDoctrine()->getManager();
      
      // On trouve le produit correspondant à l'achat actuel
      $produit = $manager->getRepository('AppBundle:Produit')->find($achat->getProduit()->getIdProduit());

      // On ajoute la catégorie au produit
      $this->ajouterCategorieProduit($produit);

      if (!$produit) {
          throw $this->createException(
              'Aucune produit pour id '.$idProduit
          );
      }

      // On ajoute le produit à l'achat
      $achat->setProduit($produit);
    }

}