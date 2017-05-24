<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Categorie;
use AppBundle\Form\CategorieType;
use AppBundle\Entity\Produit;
use AppBundle\Form\ProduitType;
use AppBundle\Entity\Commande;
use AppBundle\Form\CommandeType;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

use Doctrine\ORM\ORMException as ORMException;
/**
* @Route("/admin")
* @Security("has_role('ROLE_ADMIN')")
*/
class AdminController extends Controller 
{
    /**
    * @Route("/produit", name="admin.produit.index")
    */
    public function produitAction(Request $request)
    {
        $message = $this->getVariableFromFlashBag('messages',$request);
        $produits = $this->retrieveProduits();
        return $this->render('./admin/adminProduits.html.twig',array('produits' => $produits,'message' => $message));
    }

    /**
    * @Route("/produit/ajout", name="admin.produit.ajout")
    */
    public function produitAjoutAction(Request $request)
    {
        $categorie = $this->trouverCategorieParID(1);
        $produit = new produit(array('idProduit' => null , 'nom' => null,'prix' => null,'qteStock' => null,'qteMinimale' => null,'descriptionCourte' => "" ,'description' => ""),$categorie);
        $formAjoutProduit = $this->createForm(ProduitType::class,$produit);
        $formAjoutProduit->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formAjoutProduit->isSubmitted() && $formAjoutProduit->isValid()) {
            try {
                    $produit = $formAjoutProduit->getData();
                    $this->ajouterProduit($produit);
                    $message = new Message(MessageType::SUCCESS,"Le produit a été ajouté avec succès!");
                    $this->addFlash('messages',$message);
                    return $this->redirectToRoute('admin.produit.index');
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
        }
        return $this->render('./admin/adminProduitAjoutModif.html.twig',array('formProduit' => $formAjoutProduit->createView()));
    }

    /**
    * @Route("/produit/{idProduit}", name="admin.produit.modifier")
    */
    public function produitModifierAction($idProduit,Request $request)
    {
        $produit = $this->trouverProduitParID($idProduit);
        $formModifProduit = $this->createForm(ProduitType::class,$produit);
        $formModifProduit->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formModifProduit->isSubmitted() && $formModifProduit->isValid()) {
            try {
                    $this->appliquerChangementBD();
                    $message = new Message(MessageType::SUCCESS,"Le produit a été modifié avec succès!");
                    $this->addFlash('messages',$message);
                    return $this->redirectToRoute('admin.produit.index');
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
        }
        return $this->render('./admin/adminProduitAjoutModif.html.twig',array('formProduit' => $formModifProduit->createView()));
    }
    

    /**
    * @Route("/categorie", name="admin.categorie.index")
    */
    public function categorieAction(Request $request)
    {
        $message = $this->getVariableFromFlashBag('messages',$request);
        $categorie = new Categorie(array('idCategorie' => null,'nom' => null));
        $formAjoutCategorie = $this->createForm(CategorieType::class,$categorie);
        $formAjoutCategorie->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formAjoutCategorie->isSubmitted() && $formAjoutCategorie->isValid()) {
            try {
                    $categorie = $formAjoutCategorie->getData();
                    $this->ajouterCategorie($categorie);
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été ajoutée avec succès!");
                    // Si la catégorie a été ajoutée avec succès , on réinitialise la form
                    $formAjoutCategorie = $this->createForm(CategorieType::class);
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
        }
        $categories = $this->retrieveCategories();

        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories,'message' => $message,'formAjoutCategorie' => $formAjoutCategorie->createView()));
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
                    $this->appliquerChangementBD();
                    $message = new Message(MessageType::SUCCESS,"La catégorie a été modifiée avec succès!");
                    $this->addFlash('messages',$message);
                }
                return $this->redirectToRoute('admin.categorie.index');
            } catch(ORMException $e) {
                return $this->redirectToRoute('error500');
            }
        }
        $categories = $this->retrieveCategories();
        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories,'formModifCategorie' => $formModifCategorie->createView(),'idCategorieSelectionnee' => $idCategorie));
    }

    private function getVariableFromFlashBag($name,Request $request)
    {
        $session = $request->getSession(); // On récupère la session
        
        $flashBag = $session->getFlashBag()->get($name); // On récupère la variable de session demandée
        $result = null;
        if(isset($flashBag[0])){ // Si notre variable est définie
            $result = $flashBag[0]; // On l'assigne à notre variable result
        }
        return $result; // le résultat est retourné
    }

    /**
    * @Route("/commande", name="admin.commande.index")
    */
    public function commandeAction(Request $request)
    {
        $commandes = $this->retrieveCommandes();
        return $this->render('./admin/adminCommandes.html.twig',array('commandes' => $commandes));
    }

    /**
    * @Route("/commande/{idCommande}", name="admin.commande.detail")
    */
    public function commanDetailAction($idCommande,Request $request)
    {
        $post = $request->request->all();
        $commande = $this->trouverCommandeParId($idCommande);
        if(isset($post['nouvelEtat']))
        {
            $commande->setEtat($post['nouvelEtat']);
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

        }
        return $this->render('./admin/adminCommandeDetail.html.twig',array('commande' => $commande));
    }

    private function trouverCategorieParID($idCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $categorie = $manager->getRepository('AppBundle:Categorie')->find($idCategorie);
        return $categorie;
    }

    private function trouverProduitParID($idProduit)
    {
        $manager = $this->getDoctrine()->getManager();
        $produit = $manager->getRepository('AppBundle:Produit')->find($idProduit);
        return $produit;
    }

    private function trouverCommandeParId($idCommande)
    {
        $manager = $this->getDoctrine()->getManager();
        $commande = $manager->getRepository('AppBundle:Commande')->find($idCommande);
        return $commande;
    }

    private function categorieEstNouvelle($nomCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $categorie = $manager->getRepository('AppBundle:Categorie')->findOneBy(
            array('nom' => $nomCategorie)
            );
        return $categorie === null;
    }

    private function ajouterCategorie($categorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($categorie);
        $manager->flush();
    }

    private function appliquerChangementBD()
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();
    }


    private function ajouterProduit($produit)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($produit);
        $manager->flush();
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