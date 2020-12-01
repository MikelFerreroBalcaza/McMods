<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
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
        $source = substr($session->get('lang1'), 0, 2);
        $target = substr($session->get('lang2'), 0, 2);
        $text = $_REQUEST['text'];
        $trans = new GoogleTranslate();
        $result = $trans->translate($source, $target, $text);
        $response = new Response();
        return $response->setContent(json_encode(array(
            'result' => $result,
        )));
    }
}

function checkLang($lang)
{
    $b = false;
    if (preg_match("/^[a-zA-Z]{2}\_[A-Za-z]{2}$/", $lang)) {
        $b = true;
    }
    return $b;
}
