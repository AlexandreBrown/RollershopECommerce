<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

use Doctrine\ORM\ORMException as ORMException;
/**
* @Route("/admin")
*/
class AdminController extends Controller 
{
    /**
    * @Route("/produit", name="admin.produit")
    */
    public function produitAction(Request $request)
    {
        $produits = $this->retrieveProduits();
        return $this->render('./admin/adminProduits.html.twig',array('produits' => $produits));
    }

    /**
    * @Route("/categorie", name="admin.categorie.get")
    * @Method({"GET"})
    */
    public function categorieAction(Request $request)
    {
        $categories = $this->retrieveCategories();
        $message = $this->getMessageFromSession($request);
        // À la base , aucune catégorie n'est sélectionnée
        $idCategorieSelectionnee = null;
        // On ne veut pas que la catégorie soit sélectionnée lorsqu'on rafraichit la page
        // Si nous n'étions pas déjà entrain de modifier une catégorie
        if($this->getFlashByName($request,'idCategorieEnModification') == null)
        {
            // On va chercher l'id de la catégorie que nous avons sélectioné (null est retourné si aucune catégorie n'a été sélectionnée)
            $idCategorieSelectionnee = $this->getFlashByName($request,'idCategorieSelectionnee'); 
        }
        
        // Dès que nous avons sélectionné une catégorie à modifier , nous tombons dans l'étape de modification
        if($idCategorieSelectionnee != null)
        {
            $this->setFlashByName($request,'idCategorieEnModification',$idCategorieSelectionnee);
        }

        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories,'message' => $message,'idCategorieSelectionnee' => $idCategorieSelectionnee ));
    }

    /**
    * @Route("/categorie", name="admin.categorie.post")
    * @Method({"POST"})
    */
    public function categorieAjoutAction(Request $request)
    {
        $post = $request->request->all();
        $nomCategorie = $post['categorie']['nom'];
        $action = $post['categorie']['action'];
        if($action === "ajout"){
            if($this->categorieEstVide($nomCategorie) == false)
            {
                if($this->categorieEstNouvelle($nomCategorie))
                {
                    $this->ajouterCategorie($nomCategorie);
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été ajoutée avec succès!");
                    $this->addFlash('messages',$message);
                }else{
                    $message = new Message(MessageType::WARNING,"La catégorie existe déjà!");
                    $this->addFlash('messages',$message);
                }
            }else{
                $message = new Message(MessageType::WARNING,"La catégorie ne doit pas être vide!");
                $this->addFlash('messages',$message);
            }

        }else if($action == "sauvegarder")
        {
            $idCategorie = $this->getFlashByName($request,'idCategorieSelectionnee');
            $this->get('session')->getFlashBag()->clear();
            if($this->categorieEstVide($nomCategorie) == false)
            {
                if($this->categorieEstNouvelle($nomCategorie))
                {
                    $this->modifierNomCategorie($idCategorie,$nomCategorie);
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été modifiée avec succès!");
                    $this->addFlash('messages',$message);
                }
            }else{
                $message = new Message(MessageType::WARNING,"La catégorie ne doit pas être vide!");
                $this->addFlash('messages',$message);
            }
        }
        return $this->redirectToRoute('admin.categorie.get');
    }

    /**
    * @Route("/categorie/{idCategorie}", name="admin.categorie.modifier")
    */
    public function categorieModifierAction($idCategorie,Request $request)
    {
        $this->addFlash('idCategorieSelectionnee',$idCategorie);
        return $this->redirectToRoute('admin.categorie.get');
    }

    private function categorieEstNouvelle($nomCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $categorie = $manager->getRepository('AppBundle:Categorie')->findOneBy(
            array('nom' => $nomCategorie)
            );
        return $categorie === null;
    }

    private function ajouterCategorie($nomCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $nouvelleCategorie = new Categorie(array('idCategorie' => null,'nom' => $nomCategorie));
        $manager->persist($nouvelleCategorie);
        $manager->flush();
    }

    private function categorieEstVide($nomCategorie)
    {
        return $nomCategorie === "";
    }

    private function getMessageFromSession(Request $request)
    {
        $session = $request->getSession(); // On récupère la session
        
        $messages = $session->getFlashBag()->get('messages'); // On récupère la variable de session messages
        $message = null;
        if(isset($messages[0])){ // Si notre variable contient un message
            $message = $messages[0]; // On l'assigne à notre variable message
        }
        return $message;
    }

    private function getFlashByName(Request $request,$flashName)
    {
        $session = $request->getSession();
        $flashResult = $session->getFlashBag()->get($flashName);
        $flash = null;
        if(isset($flashResult[0]))
        {
            $flash = $flashResult[0];
        }
        return $flash;
    }

    private function setFlashByName(Request $request,$flashName,$newValue)
    {
        $session = $request->getSession();
        $session->getFlashBag()->set($flashName,$newValue);
    }

    private function modifierNomCategorie($idCategorie,$nomCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        // On trouve la catégorie correspondante
        $categorie = $manager->getRepository('AppBundle:Categorie')->find($idCategorie);
        
        $categorie->setNom($nomCategorie);

        $manager->flush();
    }

    /**
    * @Route("/commande", name="admin.commande")
    */
    public function commandeAction(Request $request)
    {
        $commandes = $this->retrieveCommandes();
        return $this->render('./admin/adminCommandes.html.twig',array('commandes' => $commandes));
    }


    // Trouve toutes les catégories
    public function retrieveCategories()
    {
        $manager = $this->getDoctrine()->getManager();
        // On trouve la catégorie correspondante
        $categories = $manager->getRepository('AppBundle:Categorie')->findAll();
        return $categories;
    }

    // Trouve toutes les produits
    public function retrieveProduits()
    {
        $manager = $this->getDoctrine()->getManager();
        // On trouve les catégories correspondantes
        $produits = $manager->getRepository('AppBundle:Produit')->findAll();
        return $produits;
    }

    // Trouve toutes les commandes
    public function retrieveCommandes()
    {
        $manager = $this->getDoctrine()->getManager();
        // On trouve la catégorie correspondante
        $commandes = $manager->getRepository('AppBundle:Commande')->findAll();
        return $commandes;
    }
}