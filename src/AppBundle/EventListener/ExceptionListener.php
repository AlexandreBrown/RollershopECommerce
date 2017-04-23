<?php

namespace AppBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $sysDate = date("Y-m-j G:i:s");
        fwrite($logFile,$sysDate." Last error : ".$exception);
        fclose($logFile);
        $exception = $event->getException();
        $errorCode = 500;
        if ($exception instanceof HttpExceptionInterface) {
            $errorCode = ($exception->getStatusCode());
        }

            $route = ('error'.$errorCode);
            $url = $this->router->generate($route);
            $response = new RedirectResponse($url);
            $event->setResponse($response);
    }
}