<?php

namespace AppBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Client;
use AppBundle\Entity\Commande;
use AppBundle\Form\InscriptionType;
use AppBundle\Form\ClientType;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Doctrine\ORM\ORMException as ORMException;
use Symfony\Component\Validator\Constraints\Length;

class ClientController extends Controller 
{
    protected $MIN_LENGTH_PWD = 5;
    protected $MAX_LENGTH_PWD = 33;
    /**
    * @Route("/dossier", name="dossier")
    */
    public function dossierAction(Request $request)
    {
        $message = null;
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            

            // Retrouver les informations du client authentifié
            $clientConnecte = $this->getUser();

            // Créer le formulaire avec les informations du client
            $formInfosCompte = $this->createForm(ClientType::class, $clientConnecte);
            $formInfosCompte->handleRequest($request);
            // Créer le formulaire avec les informations du client
            $formMotPasse = $this->createForm(ChangePasswordType::class);
            $formMotPasse->handleRequest($request);

            // <!-- Début modifs infos compte -->
            // Si le formulaire est soumis et valide
            if($formInfosCompte->isSubmitted() && $formInfosCompte->isValid()) {
                try {
                    $client = $formInfosCompte->getData();
                    $manager = $this->getDoctrine()->getManager();

                    // Sauvegarder les modifications dans la base de données
                    $manager->persist($client);

                    $manager->flush();

                    $this->authenticateClient($client);
                    $message = new Message(MessageType::SUCCESS,"Modifications des informations du compte effectuées avec succès!");

                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
                // Rediriger l’utilisateur vers la page de dossier
                return $this->render('./dossier/dossier.html.twig', array('formInfosCompte' => $formInfosCompte->createView(),'formMotPasse' => $formMotPasse->createView(),'message' => $message));
            }
            // <!-- Fin modifs infos compte -->

            // <!-- Début modif mot passe compte -->
            // Si le formulaire est soumis et valide
            if($formMotPasse->isSubmitted() && $formMotPasse->isValid()) {
                try {
                    $manager = $this->getDoctrine()->getManager();
                    $newPassword = $this->get('security.password_encoder')->encodePassword($clientConnecte, $formMotPasse["newPassword"]->getData()); // On encode le nouveau mot de passe
                    // On vérifie si la valeur pour le mot de passe actuel est valide
                    if($this->get('security.password_encoder')->IsPasswordValid($clientConnecte,$formMotPasse["oldPassword"]->getData(),$clientConnecte->getSalt())){
                        // On vérifie que le nouveau mot de passe n'est pas identique au mot de passe actuel
                        if ($newPassword !== $clientConnecte->getPassword()) {
                            // On vérifie que la longueur du mot de passe est bonne
                            if(strlen($formMotPasse["newPassword"]->getData()) > $this->MIN_LENGTH_PWD && strlen($formMotPasse["newPassword"]->getData()) < $this->MAX_LENGTH_PWD ){

                                //On change le mot de passe actuel du compte
                                $clientConnecte->setMotPasse($newPassword);

                                // On sauvegarde la modification dans la base de données
                                $manager->persist($clientConnecte);

                                $manager->flush();

                                $message = new Message(MessageType::SUCCESS,"Modification du mot de passe effectuée avec succès!");
                            }else{
                                $message = new Message(MessageType::WARNING,"Le nouveau mot de passe doit être entre ".($this->MIN_LENGTH_PWD +1)." et ".($this->MAX_LENGTH_PWD-1)." caractères");
                            }

                        } else {
                            $message = new Message(MessageType::WARNING,"Le nouveau mot de passe ne peut pas être identique au mot de passe actuel");
                        }
                    }else{
                        $message = new Message(MessageType::WARNING,"Mauvaise valeur pour le mot de passe actuel");
                    }
                }catch(ORMException $e) {
                    return $this->redirectToRoute('error500'); // Erreur avec la BD = redirection vers page d'erreur 500
                }
            }
            // <!-- Fin modif mot passe compte -->

            $session = $request->getSession(); // On récupère la session
            $messages = $session->getFlashBag()->get('messages'); // On récupère la variable de session messages
            if(isset($messages[0])){ // Si notre variable contient un message
                $message = $messages[0]; // On l'assigne à notre variable message
            }
            // Rediriger l’utilisateur vers la page de dossier
            return $this->render('./dossier/dossier.html.twig', array('formInfosCompte' => $formInfosCompte->createView(),'formMotPasse' => $formMotPasse->createView(),'message' => $message));
            } else {
                // Si l'utilisateur n'était pas connecté , on le redirige vers la page de connexion
                return $this->redirectToRoute ('connexion');
            }
    }

    /**
    * @Route("/inscription", name="inscription")
    */
    public function inscriptionAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dossier');
        } else {
            $message = null;
            $client = new Client();

            // On crée le formulaire d'inscription
            $formInfosCompte = $this->createForm(InscriptionType::class, $client);

            $formInfosCompte->handleRequest($request);
            // Si le formulaire est soumis et valide
            if($formInfosCompte->isSubmitted() && $formInfosCompte->isValid()) {
                try {
                    $client = $formInfosCompte->getData();
                    $manager = $this->getDoctrine()->getManager();
                    // On vérifie la longueur du mot de passe
                    if(strlen($formInfosCompte["motPasse"]->getData()) > $this->MIN_LENGTH_PWD && strlen($formInfosCompte["motPasse"]->getData()) < $this->MAX_LENGTH_PWD){

                        // On encode le mot de passe saisie par l'utilisateur
                        $password = $this->get('security.password_encoder')->encodePassword($client, $formInfosCompte["motPasse"]->getData());
                        
                        // On attribut ce mot de passe au compte
                        $client->setMotPasse($password);
                        
                        // On sauvegarde le compte dans la base de données
                        $manager->persist($client);

                        $manager->flush();

                        // On authentifie le compte que l'utilisateur vient de créer
                        $this->authenticateClient($client);

                        // On affiche un message à l'utilisateur sur la prochaine page
                        $message = new Message(MessageType::SUCCESS,"Votre compte a été crée avec succès");
                        $this->addFlash('messages',$message);
                        
                        // On redirige l'utilisateur à la page de dossier
                        return $this->redirectToRoute('dossier');
                    }else{
                        $message = new Message(MessageType::WARNING,"Le mot de passe doit être entre ".($this->MIN_LENGTH_PWD +1)." et ".($this->MAX_LENGTH_PWD-1)." caractères");
                    }
                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500'); // Si une erreur avec la BD est survenue on redirige l'utilisateur vers une page d'erreur 500.
                }
            }
            // On redirige l'utilisateur à la page d'inscription
            return $this->render('inscription.html.twig', array('formInfosCompte' => $formInfosCompte->createView(),'message' => $message));
        }
    }

    private function authenticateClient(Client $client)
    {
        $firewall = 'main';
        $token = new UsernamePasswordToken($client, null, $firewall, $client->getRoles());

        $this->get('security.token_storage')->setToken($token);
    }

     /**
     * @Route("/dossier/commandes", name="commandes")
     */
    public function commandesAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $clientConnecte = $this->getUser();
            $connexion = $this->getDoctrine()->getManager()->getConnection();
            $commandes = null;
            return $this->render('./dossier/commandes.html.twig',array('commandes' => $commandes));
        }
            return $this->redirectToRoute('connexion');
    }



}