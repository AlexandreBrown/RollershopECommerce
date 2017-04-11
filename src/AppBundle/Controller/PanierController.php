<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Produit;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Panier;
use AppBundle\Entity\Achat;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

/**
* @Route("/panier")
*/
class PanierController extends Controller
{
     /**
     * @Route("/", name="panier.index")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession(); // On récupère la session
        
        $messages = $session->getFlashBag()->get('messages'); // On récupère la variable de session messages
        $message = null;
        if(isset($messages[0])){ // Si notre variable contient un message
            $message = $messages[0]; // On l'assigne à notre variable message
        }

        if (!($session->has('panier'))) { // Si le panier n'est pas créé
            $session->set('panier', new Panier()); // On le crée
        }
        $panier = $session->get('panier'); // On récupère le panier
        return $this->render('panier.html.twig', array('panier' => $panier,'message' => $message)); // On fait le rendu de la vue et on lui passe le panier avec le message à afficher
    }

     /**
     * @Route("/", name="panier.post")
     * @Method({"POST"})
     */
    public function postPanierRoute(Request $request)
    {
        $action = $request->request->get('action'); // On récupère le nom 'action' passé dans le POST
        if($action === "vider"){
            $this->nouveauPanier($request);
            $message = new Message(MessageType::SUCCESS,"Panier vidé avec succès");
            $this->addFlash('messages',$message);
        }else if($action === "rafraichir"){
            $this->rafraichirPanier($request);
            $message = new Message(MessageType::INFO,"Panier mis à jour");
            $this->addFlash('messages',$message);
        }else if($action === "payer"){
            return $this->redirectToRoute('error404');
        }
        return $this->redirectToRoute('panier.index');
    }

    /**
     * @Route("/supprimer/{idProduit}", name="panier.supprimer")
     */
    public function suppressionAchatRoute($idProduit,Request $request)
    {
        $session = $request->getSession();
        if (!($session->has('panier'))) { // On s'assure que le panier existe
            $session->set('panier', new Panier());
        }
        $panier = $session->get('panier'); // On récupère le panier
        if($panier->supprimerAchat($idProduit) === 0){ // Supprimer le produit, s’il est présent
            $message = new Message(MessageType::SUCCESS,"L'article a été supprimé du panier");
            $this->addFlash('messages',$message);
            return $this->redirectToRoute('panier.index');
        }else{
            return $this->redirectToRoute('error');// Si nous sommes ici c'est qu'aucun achat n'a été supprimé donc on se doit d'afficher une page d'erreur
        }
    }

    /**
     * @Route("/ajout/{idProduit}", name="panier.ajout")
     */
    public function ajoutAchatRoute($idProduit,Request $request)
    {
        try{
            $session = $request->getSession();
            // S’assurer que le Panier existe dans la session de l’utilisateur
            if (!($session->has('panier'))) {
                $session->set('panier', new Panier());
            }

            $panier = $session->get('panier'); //$_SESSION['panier']

            $connexion = $this->getDoctrine()->getManager()->getConnection();

            // Retrouver le produit dans la base de données
            $unProduit = $this->retrieveProduit($connexion,$idProduit); // Méthode qui retourne un produit corresspondant à un idProduit passé en paramètres
            if($unProduit != null){
                $achat = new Achat($unProduit); // On construit un Achat à partir du produit
                if($panier->ajoutAchat($achat) === 0){// On ajoute l'achat au panier
                    $message = new Message(MessageType::SUCCESS,"L'article a été ajouté au panier");
                    $this->addFlash('messages',$message);
                }else{
                    $message = new Message(MessageType::DANGER,"La quantité maximale pour cet article a été atteinte!");
                    $this->addFlash('messages',$message);
                }
            }else{
                return $this->redirectToRoute('error'); // Si unProduit est null on redirige vers la route conçernant l'erreur
            }
        }catch(\Exception $e){
            return $this->redirectToRoute('error500'); // On affiche une page conçernant l'erreur avec la BD
        }
        return $this->redirectToRoute('panier.index'); // Rediriger l’utilisateur sur la page du panier
    }

    private function nouveauPanier( Request $requete )
    {
        $session = $requete->getSession();
        if ($session->has('panier')) { // Vérifie si la variable de session existe
            $session->remove('panier'); // retire la variable de session seulement si elle existe
            $session->set('panier', new Panier()); // On créer un panier vide
        }
    }

    private function retrieveProduit($connexion,$idProduit)
    {
        // Requête SQL
        $requete = "SELECT P.idProduit,P.idCategorie,P.nom,P.prix,P.qteStock,P.qteMinimale,P.descriptionCourte,P.description ";
        $requete .= "FROM Produits P INNER JOIN Categories C ON C.idCategorie = P.idCategorie "; // .= -> +=
        $requete .= "WHERE P.idProduit = :idProduit";

        $sql = $connexion->prepare($requete);
        $sql->bindValue('idProduit',$idProduit);
        $sql->execute();
        
        $produitData = $sql->fetch();
        if($produitData != null){
            $categorie = $this->retrieveCategorie($connexion,$produitData['idCategorie']);
            if($categorie != null){
                //Créer l'objet Produit
                $produit = new Produit($produitData,$categorie);
            }else{
                return null;
            }
        }else{
            return null;
        }
        return $produit;
    }

    private function retrieveCategorie($connexion,$idCategorie)
    {
        //Requête SQL
        $requete = "SELECT C.idCategorie,C.nom ";
        $requete .= "FROM Categories C "; // .= -> +=
        $requete .= "WHERE C.idCategorie = :idCategorie";
        
        $sql = $connexion->prepare($requete);
        $sql->bindValue('idCategorie',$idCategorie);
        $sql->execute();
        
        $categorieData = $sql->fetch();

        if($categorieData != null){
            //Créer l'objet Categorie
            $categorie = new Categorie($categorieData);
        }else{
            return null;
        }
        return $categorie;
    }

    private function rafraichirPanier($request)
    {
        $session = $request->getSession();
        if (!($session->has('panier'))) { 
            $session->set('panier', new Panier()); // On créer un nouveau panier si aucun panier existait
        }
        $panier = $session->get('panier'); // On récupère le panier
        $post = $request->request->all(); // On récupère ce qui a été passé via le POST
        foreach ($panier->getAchats() as $achat) { // On parcours chaque achat du panier
            $idProduit = $achat->getProduit()->getIdProduit(); // On récupère l'id du produit actuel
            $nouvelleQuantite = $post['qteCommande'][$idProduit]; // On stock la quantité relié à l'id récupéré
            if($nouvelleQuantite < 0 || $nouvelleQuantite > 10){
                $nouvelleQuantite = $achat->getQuantite();
            }
            $achat->setQuantite($nouvelleQuantite); // On met à jour la quantité
            if($achat->getQuantite() === "0"){
                if(!($panier->supprimerAchat($achat->getProduit()->getIdProduit()) == 0) ){
                    return $this->redirectToRoute('error'); // Si le produit n'a pas pu être supprimé
                }
            }
        }
    }
}