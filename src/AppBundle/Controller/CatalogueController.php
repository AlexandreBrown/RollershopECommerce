<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Produit;
use AppBundle\Controller\PDOException;

class CatalogueController extends Controller
{
     /**
     * @Route("/", name="homepage")
     *
     */
    public function indexAction(Request $request)
    {
        try{ // On tente de se connecter à la BD
            // Se connecter à la base de données
            $connexion = $this->getDoctrine()->getManager()->getConnection();
            //Recherche
            $recherche = "";
            $post = $request->request->all();
            // On vérifie si le paramètre "recherche" a une valeur
            if(isset($post["recherche"])){
                $recherche = $post["recherche"]; // Si oui , on la stock dans une variable
            }
            $priceMin =0;
            // On vérifie si le paramètre "priceMin" a une valeur
            if(isset($post["priceMin"])){
                $priceMin = intval($post["priceMin"]); // Si oui , on la stock dans une variable
            }
            $priceMax =0;
            // On vérifie si le paramètre "priceMin" a une valeur
            if(isset($post["priceMax"])){
                $priceMax = intval($post["priceMax"]); // Si oui , on la stock dans une variable
            }

            //Catégorie
            $categorie = "";
            $post = $request->request->all();
            // On vérifie si le paramètre "categorie" a une valeur
            if(isset($post["categorie"])){
                $categorie = $post["categorie"]; // Si oui , on la stock dans une variable
            }

            $produits = $this->retrieveProduits($connexion,$recherche,$priceMin,$priceMax); // On va chercher tous les produits de la BD
            $categories = $this->retrieveCategories($connexion,$categorie); // On va chercher tous les catégories de la BD
            $minPriceAvailable = $this->retrieveMinPrice($connexion); // On va chercher le prix minimal des produits en BD
            $maxPriceAvailable = $this->retrieveMaxPrice($connexion); // On va chercher le prix maximal des produits en BD
            //On envoie le tout à la vue
            return $this->render('catalogue.html.twig',array('produits' => $produits,'categories' => $categories,'minPriceAvailable' => $this->floorDownToAny($minPriceAvailable["prix"]),'maxPriceAvailable' => $this->roundUpToAny($maxPriceAvailable["prix"])));
        }catch(\Exception $e){
            return $this->render('error500.html.twig'); // On affiche une page conçernant l'erreur.
        }
    }

    // Arrondit le nombre vers la valeur multiple de 5 la plus proche (arrondit vers le haut)
    private function roundUpToAny($n,$x=5) {
        return ROUND(($n+$x/2)/$x)*$x;
    }

    // Arrondit le nombre vers la valeur multiple de 5 la plus proche (arrondit vers le bas)
    private function floorDownToAny($n,$x=5) {
        return FLOOR($n/$x) * $x;;
    }

    // Trouve le prix minimal dans la BD
    public function retrieveMinPrice($connexion)
    {
        $requete = "SELECT (P.prix) ";
        $requete .= "FROM Produits P ";
        $requete .= "ORDER BY P.prix ";
        $requete .= "LIMIT 1";

        $sql = $connexion->prepare($requete);

        $sql->execute();
        $resultat = $sql->fetch();
        
        return $resultat;
    }

    // Trouve le prix maximal dans la BD
    public function retrieveMaxPrice($connexion)
    {
        $requete = "SELECT (P.prix) ";
        $requete .= "FROM Produits P ";
        $requete .= "ORDER BY P.prix DESC ";
        $requete .= "LIMIT 1";

        $sql = $connexion->prepare($requete);

        $sql->execute();
        $resultat = $sql->fetch();

        return $resultat; 
    }

    // Trouve les produits respectants les paramètres
    public function retrieveProduits($connexion,$recherche,$priceMin,$priceMax)
    {
        // Requête SQL
        $requete = "SELECT P.idProduit,P.idCategorie,P.nom,P.prix,P.qteStock,P.qteMinimale,P.descriptionCourte,P.description,P.image ";
        $requete .= "FROM Produits P INNER JOIN Categories C ON C.idCategorie = P.idCategorie "; // .= -> +=
        
        // Obtenir les produits
        $sql = $connexion->prepare($requete);

        if($recherche !== ""){
            $requete .= " WHERE P.nom LIKE :recherche OR P.description LIKE :recherche";
            $sql = $connexion->prepare($requete);
            $sql->bindValue("recherche",'%'.$recherche.'%');
        }
        if((is_int($priceMin) && is_int($priceMax)) && ($priceMin && $priceMax !== 0)){
            $requete .= " WHERE (P.prix BETWEEN :priceMin AND :priceMax)";
            $sql = $connexion->prepare($requete);
            $sql->bindValue('priceMin',$priceMin);
            $sql->bindValue('priceMax',$priceMax);
        }

        $sql->execute();
        $resultat = $sql->fetchAll();

        // Construire les objets Produit
        $produits = [];

        foreach ($resultat as $donneesProduits) {
           $categorie = $this->retrieveCategorie($connexion,$donneesProduits['idCategorie']);
           array_push($produits,new Produit($donneesProduits,$categorie)); // On ajoute tous les produits trouvés dans notre tableau de produits
        }
        return $produits;
    }

    // Trouve la catégorie d'un idCategorie
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
            return $this->redirectToRoute('error');
        }
        return $categorie;
    }

    // Trouve toutes les catégories ou celle d'un nom de catégorie précis
    public function retrieveCategories($connexion,$categorie)
    {
        // Requête SQL
        $requete = "SELECT C.idCategorie,C.nom ";
        $requete .= "FROM Categories C"; // .= -> +=
        
        // Obtenir les catégories
        $sql = $connexion->prepare($requete);

        if($categorie != ""){
            $requete .= " WHERE C.nom = ?";
            $sql = $connexion->prepare($requete);
            $sql->bindValue(1,'%'.$categorie.'%');
        }

        $sql->execute();
        $resultat = $sql->fetchAll();

        // Construire les objets Categorie
        $categories = [];

        foreach ($resultat as $donneesCategories) {
           array_push($categories,new Categorie($donneesCategories)); // On ajoute toutes les catégories trouvées dans notre tableau de catégories
        }
        return $categories;
    }
    
}