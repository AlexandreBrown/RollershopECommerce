<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Produit;
use AppBundle\Entity\Categorie;

class CatalogueDetailController extends Controller 
{
    /**
     * @Route("/produit/popup/{idProduit}", name="produit.popup")
     */
    public function popupAction($idProduit)
    {
        try{ // On tente de se connecter à la BD
            // Se connecter à la base de données
            $connexion = $this->getDoctrine()->getManager()->getConnection();
            $produit = $this->retrieveProduit($connexion,$idProduit); // On va chercher le produit qui correspond à l'id
            if($produit != null){
                //Nous fournissons le tout à la vue
                return $this->render('catalogue.popup.html.twig',array('produit' => $produit));
            }else{
                return $this->redirectToRoute('error');
            }
        }catch(\Exception $e){
            return $this->redirectToRoute('error500'); // On affiche une page conçernant l'erreur.
        }
    }

    public function retrieveProduit($connexion,$idProduit)
    {
        // Requête SQL
        $requete = "SELECT P.idProduit,P.idCategorie,P.nom,P.prix,P.qteStock,P.qteMinimale,P.descriptionCourte,P.description,P.image ";
        $requete .= "FROM Produits P INNER JOIN Categories C ON C.idCategorie = P.idCategorie "; // .= -> +=
        $requete .= "WHERE P.idProduit = :idProduit";

        $sql = $connexion->prepare($requete);
        $sql->bindValue('idProduit',$idProduit);
        $sql->execute();
        
        $produitData = $sql->fetch();
        if($produitData != null){
            $categorie = $this->retrieveCategorie($connexion,$produitData['idCategorie']); // On va chercher la catégorie qui correspond à l'id
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

    public function retrieveCategorie($connexion,$idCategorie)
    {
        // Requête SQL
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