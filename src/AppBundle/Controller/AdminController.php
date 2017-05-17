<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Client;

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
    * @Route("/categorie", name="admin.categorie")
    */
    public function categorieAction(Request $request)
    {
        $categories = $this->retrieveCategories();
        return $this->render('./admin/adminCategories.html.twig',array('categories' => $categories));
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
        // On trouve la catégorie correspondante
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