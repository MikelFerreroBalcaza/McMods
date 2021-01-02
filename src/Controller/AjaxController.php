<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Statickidz\GoogleTranslate;
use Symfony\Component\HttpFoundation\Request;


class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function ajax(Request $request): Response
    {
        $session = $request->getSession();
        $error = 0;
        $source = $request->get('source');
        $target = $request->get('target');
        if ($target == 'new') {
        }
        $text = $request->get('text');
        $trans = new GoogleTranslate();
        $result = $trans->translate($source, $target, $text);
        $response = new Response();
        return $response->setContent(json_encode(array(
            'result' => $result,
        )));
    }
}
