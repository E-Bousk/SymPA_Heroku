<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{

    private $maintenance;
    private $twig;

    public function __construct($maintenance, Environment $twig)
    {
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }
    
    public function onKernelRequest(RequestEvent $event)
    {
        // Vérifie si le fichier « .maintenance » existe
        if (!file_exists($this->maintenance)) {
            return;
        }

        // Définit la réponse
        $event->setResponse(
            new Response(
                $this->twig->render('maintenance/maintenance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            )
        );

        // Stoppe le traitement des évènements
        $event->stopPropagation();
    }
}
