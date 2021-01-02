<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class HelpController extends AbstractController
{
    /**
     * @Route("/help", name="index")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        return $this->render('help/help.html.twig', []);
    }
}