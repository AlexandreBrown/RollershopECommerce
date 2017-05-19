<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Categorie;
use AppBundle\Form\CategorieType;
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
    * @Route("/categorie", name="admin.categorie.index")
    */
    public function categorieAction(Request $request)
    {
        $message = $this->getMessageFromSession($request);
        // À la base , aucune catégorie n'est sélectionnée
        // 
        $formAjoutCategorie = $this->createForm(CategorieType::class);
        $formAjoutCategorie->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formAjoutCategorie->isSubmitted() && $formAjoutCategorie->isValid()) {
            try {
                $categorie = $formAjoutCategorie->getData();

                if($this->categorieEstNouvelle($categorie['nom']))
                {
                    $this->ajouterCategorie($categorie['nom']);
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été ajoutée avec succès!");
                    $this->addFlash('messages',$message);
                }else{
                    $message = new Message(MessageType::WARNING,"La catégorie existe déjà!");
                    $this->addFlash('messages',$message);
                }

            } catch(ORMException $e) {
                return $this->redirectToRoute('error500');
            }
        }



        $categories = $this->retrieveCategories();

        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories,'message' => $message,'formAjoutCategorie' => $formAjoutCategorie->createView()));
    }

    private function trouverCategorieParID($idCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $categorie = $manager->getRepository('AppBundle:Categorie')->find($idCategorie);
        return $categorie;
    }


    /**
    * @Route("/categorie/{idCategorie}", name="admin.categorie.modifier")
    */
    public function categorieModifierAction($idCategorie,Request $request)
    {
        $categorie = $this->trouverCategorieParID($idCategorie);
        $formModifCategorie = $this->createForm(CategorieType::class,$categorie);
        $formModifCategorie->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formModifCategorie->isSubmitted() && $formModifCategorie->isValid()) {
            try {
                $categorie = $formModifCategorie->getData();
                if($this->categorieEstNouvelle($categorie->getNom()))
                {
                    $this->modifierNomCategorie($categorie->getIdCategorie(),$categorie->getNom());
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été modifiée avec succès!");
                    $this->addFlash('messages',$message);
                }
            } catch(ORMException $e) {
                return $this->redirectToRoute('error500');
            }
        }
        $categories = $this->retrieveCategories();
        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories,'formModifCategorie' => $formModifCategorie->createView(),'idCategorieSelectionnee' => $idCategorie));
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