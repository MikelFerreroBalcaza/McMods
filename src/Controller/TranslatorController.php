<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SplFileInfo;
use ZipArchive;

class TranslatorController extends AbstractController
{
    /**
     * @Route("/translator", name="translator")
     */
    public function index(): Response
    {
        $error = 0;
        if (isset($_FILES['userfile'])) {
            $_SESSION['files'] = $_FILES['userfile'];
        }
        if (isset($_SESSION['files'])) {
            $nombre_archivo = $_SESSION['files']['name'];
            $ruta_archivo = $_SESSION['files']['tmp_name'];
            $ruta_extraido = 'var/' . $nombre_archivo;
            $_SESSION['ruta_extraido'] = $ruta_extraido;
            $info = new SplFileInfo($_SESSION['files']['name']);
            $extension = $info->getExtension();
            if (!$extension === 'jar' || !$extension === 'zip' || !$extension === '7z') {
                $error = 1;
            } else {
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo) === TRUE) {
                    $zip->extractTo($ruta_extraido);
                    $zip->close();
                    dirLang($_SESSION['ruta_extraido'] . '/');
                }
            }
        } else $error = 2;
        return $this->render('translator/translator.html.twig', [
            'errorCode' => $error,
            'sesion' => $_SESSION,
        ]);
    }
}
function dirLang($ruta)
{
    if (is_dir($ruta)) {
        if ($dh = opendir($ruta)) {
            while (($file = readdir($dh)) !== false) {
                if (is_dir($ruta . $file) && $file != '.' && $file != '..') {
                    $dr = $ruta . $file . '/';
                    if (preg_match('/lang/', $dr)) {
                        $_SESSION['langdir'] = $dr;
                        $_SESSION['langfiles'] = scandir($dr);
                    }
                    dirLang($dr);
                } elseif ((!is_dir($ruta . $file)) && $file != '.' && $file != '..') {
                    if ($file == 'mcmod.info') {
                        $data = file_get_contents($ruta . $file);
                        $products = json_decode($data, true);
                        foreach ($products as $product) {
                            $_SESSION['mcmod_info'] = $product;
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}
