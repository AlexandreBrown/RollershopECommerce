<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Message;
use AppBundle\Entity\MessageType;
class PageController extends Controller
{
     /**
     * @Route("/contacts", name="contacts")
     */
    public function indexAction(Request $request)
    {
        require_once '../vendor/swiftmailer/swiftmailer/lib/swift_required.php'; // On s'assure que swiftmailer est inclut (recommandé par la doc)
        $submit = $request->request->get('submit');
        if($submit != ""){ // Si l'utilisateur a cliquer sur le bouton d'envoie
        // On récupère les informatiosn du formulaire
            $name = $request->request->get('form_name');
            $email = $request->request->get('form_email');
            $msgBody = $request->request->get('form_msg');
            try{
                // On vérifie que les informations sont valide
                if($this->validateName($name) && $this->validateEmail($email) && $this->validateMsg($msgBody)){
                    $message = \Swift_Message::newInstance()
                            ->setSubject($email)
                            ->setFrom($this->getParameter('mailer_user'))// Car gmail ne nous laisse pas envoyer des courriels depuis un courriel autre que notre mailer_user
                            ->setTo($this->getParameter('mailer_user'))
                            ->setBody(
                                $this->renderView(
                                    'Email/contactEmailSent.html.twig',
                                    array('name' => $name, 'email' =>$email, 'msgBody' => $msgBody)
                                ),
                                'text/html'
                            )
                        ;
                        if($this->get('mailer')->send($message)){
                            $message = new Message(MessageType::SUCCESS,"Votre courriel à été envoyé avec succès! Merci!"); // On affiche un message si le message s'est bien envoyé
                            $this->addFlash('messages',$message);
                        }else{
                            $message = new Message(MessageType::DANGER,"Votre courriel n'a pas pu être envoyé!"); // On affiche un message si le message ne s'est pas bien envoyé
                            $this->addFlash('messages',$message);
                        }
            }
            }catch(\Exception $e){
                $message = new Message(MessageType::DANGER,$e->getMessage()); // On affiche un message d'erreur dans le cas où le message n'a pas pu être envoyé
                $this->addFlash('messages',$message);
            }
        }
        $session = $request->getSession();
        $messages = $session->getFlashBag()->get('messages');
        $message = null;
        if(isset($messages[0])){
            $message = $messages[0];
        }
        return $this->render('contacts.html.twig',array('message' => $message));
    }
    private function validateName($name){ // On valide le nom ( L'utilisation de doctrine était interdite pour le TP02 )
        if($name != "" && strlen($name) > 1 && $name != '  '){
            return true;
        }else{
            $message = new Message(MessageType::DANGER,"Le nom est invalide!");
            $this->addFlash('messages',$message);
            return false;
        }
    }

    private function validateMsg($msgBody){ // On valide le message ( L'utilisation de doctrine était interdite pour le TP02 )
        if($msgBody != "" && strlen($msgBody) > 1 && $msgBody != '  '){
            return true;
        }else{
            $message = new Message(MessageType::DANGER,"Le message est invalide!");
            $this->addFlash('messages',$message);
            return false;
        }
    }

    private function validateEmail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $message = new Message(MessageType::DANGER,"Le courriel est invalide!");
            $this->addFlash('messages',$message);
            return false;
        }else{
            return true;
        }
    }
    /**
     * @Route("/erreur", name="error")
     */
    public function errorAction(Request $request)
    {
        return $this->render('error.html.twig');
    }

    /**
     * @Route("/erreur404", name="error404")
     */
    public function pageNotFoundAction(Request $request)
    {
        return $this->render('error404.html.twig');
    }
    
    /**
     * @Route("/erreur500", name="error500")
     */
    public function serverErrorAction(Request $request)
    {
        return $this->render('error500.html.twig');
    }

}