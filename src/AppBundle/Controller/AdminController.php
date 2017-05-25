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
        // Valeur fictive qui vont se faire override par le formulaire
        $categorie = $this->trouverCategorieParID(1);
        $produit = new produit(array('idProduit' => null , 'nom' => null,'prix' => null,'qteStock' => null,'qteMinimale' => null,'descriptionCourte' => "" ,'description' => "",'image' => null),$categorie);
        //--------------------------------------------------------------
        $formAjoutProduit = $this->createForm(ProduitType::class,$produit);
        $formAjoutProduit->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formAjoutProduit->isSubmitted() && $formAjoutProduit->isValid()) {
            try {
                    // Si l'administrateur n'a pas envoyé d'image
                    if($produit->getImage() === null)
                    {
                        // On met "NULL" pour l'image
                        $produit->setImage("NULL");
                    }else{
                        // Sinon on met le nom hashé de l'image
                        $produit->setImage($this->getHashFileName($produit));
                    }
                    // On persist en BD le produit
                    $this->persistEntity($produit);
                    $message = new Message(MessageType::SUCCESS,"Le produit a été ajouté avec succès!");
                    $this->addFlash('messages',$message);
                    return $this->redirectToRoute('admin.produit.index');
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
        }
        return $this->render('./admin/adminProduitAjoutModif.html.twig',array('formProduit' => $formAjoutProduit->createView(),'produit' => $produit));
    }

    /**
    * @Route("/produit/{idProduit}", name="admin.produit.modifier")
    */
    public function produitModifierAction($idProduit,Request $request)
    {
        // On stock le produit
        $produit = $this->trouverProduitParID($idProduit);
        // On stock l'image dans son état actuel
        $ancienneImage = $produit->getImage();
        $formModifProduit = $this->createForm(ProduitType::class,$produit);
        $formModifProduit->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formModifProduit->isSubmitted() && $formModifProduit->isValid()) {
            try {
                    $manager = $this->getDoctrine()->getManager();
                    // Si l'administrateur n'a pas envoyé d'image
                    if($produit->getImage() === null)
                    {
                        // On remet l'ancienne image
                        $produit->setImage($ancienneImage);
                    }else{
                        // Si l'administrateur a envoyé une image
                        // On met à jour l'image du produit pour le nom hashé de l'image envoyé
                        $produit->setImage($this->getHashFileName($produit));
                    }
                    // On met à jour la BD
                    $manager->flush();
                    $message = new Message(MessageType::SUCCESS,"Le produit a été mis à jour avec succès!");
                    $this->addFlash('messages',$message);
                    return $this->redirectToRoute('admin.produit.index');
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
        }
        return $this->render('./admin/adminProduitAjoutModif.html.twig',array('formProduit' => $formModifProduit->createView(),'produit' => $produit));
    }
    

    /**
    * @Route("/categorie", name="admin.categorie.index")
    */
    public function categorieAction(Request $request)
    {
        $message = $this->getVariableFromFlashBag('messages',$request);
        // Valeur fictive qui vont se faire override par le formulaire
        $categorie = new Categorie(array('idCategorie' => null,'nom' => null));
        // ---------------------------------------------------------------
        $formAjoutCategorie = $this->createForm(CategorieType::class,$categorie);
        $formAjoutCategorie->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formAjoutCategorie->isSubmitted() && $formAjoutCategorie->isValid()) {
            try {
                    $this->persistEntity($categorie);
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
        // On stock la catégorie
        $categorie = $this->trouverCategorieParID($idCategorie);
        $formModifCategorie = $this->createForm(CategorieType::class,$categorie);
        $formModifCategorie->handleRequest($request);
        // Si le formulaire est soumis et valide
        if($formModifCategorie->isSubmitted() && $formModifCategorie->isValid()) {
            try {
                if($this->categorieEstNouvelle($categorie->getNom()))
                {
                    $manager = $this->getDoctrine()->getManager();
                    $manager->flush();
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

    private function getHashFileName($produit)
    {
        if($produit->getImage() !== null)
        {
            $extension = $produit->getImage()->guessExtension();
            $fileDIR = $this->get('kernel')->getRootDir() . '/../web/img/produits/'.$produit->getCategorie()->getIdCategorie();
            $fileName = hash('md5',uniqid()).'.'.$extension;
            $produit->getImage()->move(
                $fileDIR,
                $fileName
            );
            return $fileName;
        }
    }

    private function trouverCategorieParID($idCategorie)
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            $categorie = $manager->getRepository('AppBundle:Categorie')->find($idCategorie);
            return $categorie;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }

    private function trouverProduitParID($idProduit)
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            $produit = $manager->getRepository('AppBundle:Produit')->find($idProduit);
            return $produit;
        }catch(\Exception $e)
        {
             return $this->redirectToRoute('error500');
        }
    }

    private function trouverCommandeParId($idCommande)
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            $commande = $manager->getRepository('AppBundle:Commande')->find($idCommande);
            return $commande;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }

    private function categorieEstNouvelle($nomCategorie)
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            $categorie = $manager->getRepository('AppBundle:Categorie')->findOneBy(
                array('nom' => $nomCategorie)
                );
            return $categorie === null;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }

    private function persistEntity($entity)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($entity);
        $manager->flush();
    }


    // Trouve toutes les catégories
    public function retrieveCategories()
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            // On trouve la catégorie correspondante
            $categories = $manager->getRepository('AppBundle:Categorie')->findAll();
            return $categories;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }

    // Trouve toutes les produits
    public function retrieveProduits()
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            // On trouve les catégories correspondantes
            $produits = $manager->getRepository('AppBundle:Produit')->findAll();
            return $produits;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }

    // Trouve toutes les commandes
    public function retrieveCommandes()
    {
        try{
            $manager = $this->getDoctrine()->getManager();
            // On trouve la catégorie correspondante
            $commandes = $manager->getRepository('AppBundle:Commande')->findAll();
            return $commandes;
        }catch(\Exception $e)
        {
            return $this->redirectToRoute('error500');
        }
    }
}