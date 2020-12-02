<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use SplFileInfo;
use ZipArchive;

class TranslatorController extends AbstractController

{

    /**
     * @Route("/translator", name="translator")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $error = 0;
        if (!isset($_REQUEST['langform'])) {
            if (isset($_FILES['userfile'])) {
                $session->set('fileName', $_FILES['userfile']['name']);
                $session->set('fileRout', $_FILES['userfile']['tmp_name']);
            }
            $nombre_archivo = $session->get('fileName');
            $ruta_archivo = $session->get('fileRout');
            $ruta_extraido = 'var/' . $nombre_archivo;
            $session->set('rutaExtraido', $ruta_extraido);
            $info = new SplFileInfo($session->get('fileName'));
            $extension = $info->getExtension();
            if (!$extension === 'jar' || !$extension === 'zip' || !$extension === '7z') {
                $error = 1;
            } else {
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo) === TRUE) {
                    $zip->extractTo($ruta_extraido);
                    $zip->close();
                    dirLang($request, $ruta_extraido . '/');
                }
            }
            return $this->render('translator/selectorLanguage.html.twig', [
                'errorCode' => $error,
                'langFiles' => $session->get('langFiles'),
                $session->get('langFile'),
            ]);
        } else {
            if ($session->get('langFile') == '.lang') {
                $lang1 = $_REQUEST['lang1'];
                $lang2 = $_REQUEST['lang2'];
                $session->set('lang1', $lang1);
                $session->set('lang2', $lang2);
                $arrLang2 = [];
                if (!(strtolower($lang2) == strtolower('new'))) {
                    $lang2 = $session->get('langDir') . $lang2;
                    $fp2 = fopen($lang2, 'r');
                    while (!feof($fp2)) {
                        $line = fgets($fp2);
                        if (!empty($line)) {
                            if (!($line[0] == '#')) {
                                $item = explode('=', $line);
                                if (isset($item[1])) {
                                    $arrLang2[$item[0]] = $item[1];
                                }
                            }
                        }
                    }
                }
                $arrLang1 = [];
                if (!(strtolower($lang2) == strtolower('new'))) {
                    $lang1 = $session->get('langDir') . $lang1;
                    $fp1 = fopen($lang1, 'r');
                    while (!feof($fp1)) {
                        $line = fgets($fp1);
                        if (!empty($line)) {
                            if (!($line[0] == '#')) {
                                $item = explode('=', $line);
                                if (isset($item[1])) {
                                    $arrLang1[$item[0]] = $item[1];
                                }
                            }
                        }
                    }
                }
                $lang1 = $_REQUEST['lang1'];
                $lang2 = $_REQUEST['lang2'];
                $session->set('lang1', $lang1);
                $session->set('lang2', $lang2);
            } elseif ($session->get('langFile') == '.json') {
            } else {
            }
            return $this->render('translator/translatorLanguage.html.twig', [
                'mcmodInfo' => $session->get('mcmod_info'),
                'request' => $_REQUEST,
                'langFile' => $session->get('langFile'),
                'arrLang1' => $arrLang1,
                'arrLang2' => $arrLang2,
                'lang1'=> $session->get('lang1'),
                'lang2'=> $session->get('lang2'),
                'session' => $session->all(),

            ]);
        }
    }
}
function dirLang($request, $ruta)
{
    $session = $request->getSession();
    if (is_dir($ruta)) {
        if ($dh = opendir($ruta)) {
            while (($file = readdir($dh)) !== false) {
                if (is_dir($ruta . $file) && $file != '.' && $file != '..') {
                    $dr = $ruta . $file . '/';
                    if (preg_match('/lang/', $dr)) {
                        $session->set('langDir', $dr);
                        $session->set('langFiles', scandir($dr));
                        if (substr(scandir($dr)[3], -5) == '.lang' || substr(scandir($dr)[3], -5) == '.json') {
                            $session->set('langFile', substr(scandir($dr)[3], -5));
                        } elseif (substr(scandir($dr)[4], -5) == '.lang' || substr(scandir($dr)[4], -5) == '.json') {
                            $session->set('langFile', substr(scandir($dr)[3], -5));
                        } else {
                            $session->set('langFile', 'error');
                        }
                    }
                    dirLang($request, $dr);
                } elseif ((!is_dir($ruta . $file)) && $file != '.' && $file != '..') {
                    if ($file == 'mcmod.info') {
                        $data = file_get_contents($ruta . $file);
                        $products = json_decode($data, true);
                        foreach ($products as $product) {
                            $session->set('mcmod_info',  $product);
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}