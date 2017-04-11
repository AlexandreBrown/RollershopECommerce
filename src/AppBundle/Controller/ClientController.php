<?php

namespace AppBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Client;
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
            $clientConnecte = $this->getUser();

            $formInfosCompte = $this->createForm(ClientType::class, $clientConnecte);
            $formInfosCompte->handleRequest($request);
            //Modifier infos d'un compte
            if($formInfosCompte->isSubmitted() && $formInfosCompte->isValid()) {

                $client = $formInfosCompte->getData();

                try {
                    $manager = $this->getDoctrine()->getManager();

                    $manager->persist($client);

                    $manager->flush();

                    $this->authenticateClient($client);
                    $message = new Message(MessageType::SUCCESS,"Modifications des informations du compte effectuées avec succès!");

                } catch(ORMException $e) {
                    return $this->redirectToRoute('error500');
                }
            }

            $formMotPasse = $this->createForm(ChangePasswordType::class);
            $formMotPasse->handleRequest($request);
            //Modifier mot de passe
            if($formMotPasse->isSubmitted() && $formMotPasse->isValid()) {
                try {
                    $manager = $this->getDoctrine()->getManager();
                    $newPassword = $this->get('security.password_encoder')->encodePassword($clientConnecte, $formMotPasse["newPassword"]->getData());
                    if($this->get('security.password_encoder')->IsPasswordValid($clientConnecte,$formMotPasse["oldPassword"]->getData(),$clientConnecte->getSalt())){
                        if ($newPassword !== $clientConnecte->getPassword()) {
                            if(strlen($formMotPasse["newPassword"]->getData()) > $this->MIN_LENGTH_PWD && strlen($formMotPasse["newPassword"]->getData()) < $this->MAX_LENGTH_PWD ){
                                $clientConnecte->setMotPasse($newPassword);

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
                    return $this->redirectToRoute('error500');
                }
            }
            $session = $request->getSession(); // On récupère la session
            $messages = $session->getFlashBag()->get('messages'); // On récupère la variable de session messages
            if(isset($messages[0])){ // Si notre variable contient un message
                $message = $messages[0]; // On l'assigne à notre variable message
            }
            return $this->render('./dossier/dossier.html.twig', array('formInfosCompte' => $formInfosCompte->createView(),'formMotPasse' => $formMotPasse->createView(),'message' => $message));
            } else {
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

            $formInfosCompte = $this->createForm(InscriptionType::class, $client);

            $formInfosCompte->handleRequest($request);
                if($formInfosCompte->isSubmitted() && $formInfosCompte->isValid()) {
                    
                    $client = $formInfosCompte->getData();
                    try {
                        $manager = $this->getDoctrine()->getManager();
                        if(strlen($formInfosCompte["motPasse"]->getData()) > $this->MIN_LENGTH_PWD && strlen($formInfosCompte["motPasse"]->getData()) < $this->MAX_LENGTH_PWD){

                            $password = $this->get('security.password_encoder')->encodePassword($client, $client->getMotPasse());
                            $client->setMotPasse($password);

                            $manager->persist($client);

                            $manager->flush();

                            $this->authenticateClient($client);
                            $message = new Message(MessageType::SUCCESS,"Votre compte a été crée avec succès");
                            $this->addFlash('messages',$message);
                            return $this->redirectToRoute('dossier');
                        }else{
                            $message = new Message(MessageType::WARNING,"Le mot de passe doit être entre ".($this->MIN_LENGTH_PWD +1)." et ".($this->MAX_LENGTH_PWD-1)." caractères");
                        }
                    } catch(ORMException $e) {
                        return $this->redirectToRoute('error500');
                    }
                }
            return $this->render('inscription.html.twig', array('formInfosCompte' => $formInfosCompte->createView(),'message' => $message));
        }
    }

    private function authenticateClient(Client $client)
    {
        $firewall = 'main';
        $token = new UsernamePasswordToken($client, null, $firewall, $client->getRoles());

        $this->get('security.token_storage')->setToken($token);
    }

}