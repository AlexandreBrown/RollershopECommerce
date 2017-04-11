<?php

namespace AppBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /**
     *
     *@var Router
     */
    protected $router;

    /**
     * @param Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $logFile = fopen(__DIR__ . "/Logs/KernelException.log","w");
        $exception = $event->getException();
        fwrite($logFile,$exception);
        fclose($logFile);
            $route = 'error500';
            $url = $this->router->generate($route);
            $response = new RedirectResponse($url);
            $event->setResponse($response);
    }
}