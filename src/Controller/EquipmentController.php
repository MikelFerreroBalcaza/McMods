<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class EquipmentController extends AbstractController
{
    /**
     * @Route("/equipment", name="index")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        return $this->render('equipment/equipment.html.twig', []);
    }
}