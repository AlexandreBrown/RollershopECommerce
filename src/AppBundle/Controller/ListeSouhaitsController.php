<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Produit;
use AppBundle\Entity\ListeSouhaits;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

/**
* @Route("/listeSouhaits")
*
*/
class ListeSouhaitsController extends Controller
{
     /**
     * @Route("/", name="listeSouhaits.index")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        
        $messages = $session->getFlashBag()->get('messages');
        $message = null;
        if(isset($messages[0])){
            $message = $messages[0];
        }
        if (!($session->has('listeSouhaits'))) {
            $session->set('listeSouhaits', new ListeSouhaits());
        }
        $listeSouhaits = $session->get('listeSouhaits');
        return $this->render('listeSouhaits.html.twig',array('listeSouhaits' => $listeSouhaits,'message' => $message));
    }

    /**
     * @Route("/", name="listeSouhaits.post")
     * @Method({"POST"})
     */
    public function postListeSouhaitsRoute(Request $request)
    {
        $action = $request->request->get('action'); // On récupère le nom 'action' passé dans le POST
        if($action === "vider"){
            $this->nouvelleListeSouhaits($request);
            $message = new Message(MessageType::SUCCESS,"Liste de souhaits vidée avec succès");
            $this->addFlash('messages',$message);
        }
        return $this->redirectToRoute('listeSouhaits.index');
    }

    private function nouvelleListeSouhaits( Request $requete )
    {
        $session = $requete->getSession();
        if ($session->has('listeSouhaits')) { // Vérifie si la variable de session existe
            $session->remove('listeSouhaits'); // retire la variable de session seulement si elle existe
            $session->set('listeSouhaits', new ListeSouhaits()); // Une nouvelle liste de souhaits est créée
        }
    }

    /**
     * @Route("/supprimer/{idProduit}", name="listeSouhaits.supprimer")
     */
    public function suppressionSouhaitRoute($idProduit,Request $request)
    {
        $session = $request->getSession();
        $listeSouhaits = $session->get('listeSouhaits'); // On récupère la liste de souhaits
        if($listeSouhaits->supprimerSouhait($idProduit) === 0){
            $message = new Message(MessageType::SUCCESS,"L'article a été supprimé de la liste de souhaits");
            $this->addFlash('messages',$message);
            return $this->redirectToRoute('listeSouhaits.index');
        }else{
            return $this->redirectToRoute('error');
        }
    }

    /**
     * @Route("/ajout/{idProduit}", name="listeSouhaits.ajout")
     *
     */
    public function ajoutSouhaitRoute($idProduit,Request $request)
    {
        try{
            $session = $request->getSession();

            if (!($session->has('listeSouhaits'))) {
                $session->set('listeSouhaits', new ListeSouhaits());
            }

            $listeSouhaits = $session->get('listeSouhaits'); //$_SESSION['listeSouhaits']

            $connexion = $this->getDoctrine()->getManager()->getConnection();

            // Créer l'objet
            $unProduit = $this->retrieveProduit($connexion,$idProduit); // Méthode qui retourne un produit corresspondant à un idProduit passé en paramètres
            if($unProduit != null){
                if($listeSouhaits->ajoutSouhait($unProduit) === 0){ // On ajoute le produit a la liste de souhaits
                    $message = new Message(MessageType::SUCCESS,"L'article a été ajouté à la liste de souhaits");
                    $this->addFlash('messages',$message);
                }
            }else{
                return $this->redirectToRoute('error'); // Erreur conçernant l'idProduit
            }
        }catch(\Exception $e){
            return $this->redirectToRoute('error500'); // Erreur de la BD (connexion à la BD)
        }
        return $this->redirectToRoute('listeSouhaits.index');
    }

    private function retrieveProduit($connexion,$idProduit)
    {
        //Requête SQL
        $requete = "SELECT P.idProduit,P.idCategorie,P.nom,P.prix,P.qteStock,P.qteMinimale,P.descriptionCourte,P.description ";
        $requete .= "FROM Produits P INNER JOIN Categories C ON C.idCategorie = P.idCategorie "; // .= -> +=
        $requete .= "WHERE P.idProduit = :idProduit";

        $sql = $connexion->prepare($requete);
        $sql->bindValue('idProduit',$idProduit);
        $sql->execute();
        
        $produitData = $sql->fetch();

        if($produitData != null){
            //Créer l'objet Produit
            $categorie = $this->retrieveCategorie($connexion,$produitData['idCategorie']);
            if($categorie != null){
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

}