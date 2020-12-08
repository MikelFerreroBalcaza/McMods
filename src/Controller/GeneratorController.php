<?php

namespace App\Controller;

use RecursiveDirectoryIterator as GlobalRecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use ZipArchive;

class GeneratorController extends AbstractController
{
    /**
     * @Route("/Generator", name="Generator")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->get('langDir') == null) {
            $archivo = $session->get('langDir') . $session->get('lang2');
            if (file_exists($archivo)) {
                unlink($archivo);
            }
            $file = fopen($archivo, 'a');
            foreach ($request->request as $ky => $value) {
                $key = preg_replace('/_/', '.', $ky);
                if ($key == 'modtranslator') {
                    if (!empty($value)) {
                        fwrite($file, "# Translated by: {$value}\n");
                    }
                }
                if ($key == 'modtranslationversion') {
                    if (!empty($value)) {
                        fwrite($file, "# Translation Version: {$value}\n");
                    }
                }
                if (in_array($key, $session->get('codelist'))) {
                    fwrite($file, "{$key}={$value}\n");
                }
            }
            fclose($file);
            $zip = new ZipArchive();
            $nombreArchivoZip =  "../" . $session->get('fileName');
            $rutaDelDirectorio =  $session->get('rutaExtraido') . '/';
            if (!$zip->open($nombreArchivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                exit("Error abriendo ZIP en $nombreArchivoZip");
            }
            $archivos = new RecursiveIteratorIterator(
                new GlobalRecursiveDirectoryIterator($rutaDelDirectorio),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($archivos as $archivo) {

                if ($archivo->isDir()) {
                    continue;
                }
                $rutaAbsoluta = $archivo->getRealPath();

                $nombreArchivo = substr($rutaAbsoluta, strlen($rutaDelDirectorio) + 16);
                $zip->addFile($rutaAbsoluta, $nombreArchivo);
            }
            $resultado = $zip->close();
            if ($resultado) {
                //echo "Archivo creado";
            } else {
                //echo "Error creando archivo";
            }
            rmDir_rf($session->get('rutaExtraido'));
        }
        return $this->render('Generator/Generator.html.twig', [
            'session' => $session,
            'zip' => $zip,
            'nombreArchivo' =>  $nombreArchivo,
            'rutaAbsoluta' =>  $rutaAbsoluta,
            'rutaDelDirectorio' =>  $rutaDelDirectorio,
        ]);
    }
}

function dirZip($request, $ruta)
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
                    dirZip($request, $dr);
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

function rmDir_rf($dir)
{
    foreach (glob($dir . '/*') as $file_dir) {
        if (is_dir($file_dir)) {
            rmDir_rf($file_dir);
        } else {
            unlink($file_dir);
        }
    }
    rmdir($dir);
}
