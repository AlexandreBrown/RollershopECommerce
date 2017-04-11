<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Client;
use AppBundle\Form\InscriptionType;
use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Doctrine\ORM\ORMException as ORMException;

class SecuriteController extends Controller 
{
    /**
    * @Route("/connexion", name="connexion")
    */
    public function connexionAction(Request $request)
    {
        // On ne doit pas pouvoir accéder à la page de connexion si nous sommes déjà connecté
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
        $session = $request->getSession();
        $authentificationUtils = $this->get('security.authentication_utils');

        $erreur = $authentificationUtils->getLastAuthenticationError();

        $messageErreur = null;
        $message = null;

        if($erreur !== null) {
            $messageErreur = "Erreur lors de l'authentification";
            $message = new Message(MessageType::DANGER, $messageErreur);
        }

        $dernierUtilisateur = $authentificationUtils->getLastUsername();

        return $this->render("connexion.html.twig", array('dernierUtilisateur' => $dernierUtilisateur, 'message' => $message));
        }else{
            return $this->redirectToRoute('dossier');
        }
    }

    /**
    * @Route("/deconnexion", name="deconnexion")
    */
    public function deconnexionRoute()
    {

    }
}