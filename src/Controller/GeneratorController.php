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
            if ($session->get('langFile') == '.json') {
                fwrite($file, "{\n");
                $cont = 0;
                foreach ($request->request as $ky => $value) {
                    $cont++;
                    $key = preg_replace('/_/', '.', $ky);
                    if ($key == 'modtranslator') {
                        if (!empty($value)) {
                            if ($cont == sizeof($request->request)) {
                                fwrite($file, '"Translated.by":"' . $value . '"');
                            } else {
                                fwrite($file, '"Translated.by":"' . $value . '",');
                            }
                        }
                    }
                    if ($key == 'modtranslationversion') {
                        if (!empty($value)) {
                            if ($cont == sizeof($request->request)) {
                                fwrite($file, '"Translation.Version":"' . $value . '"');
                            } else {
                                fwrite($file, '"Translation.Version":"' . $value . '",');
                            }
                        }
                    }
                    if (in_array($key, $session->get('codelist'))) {
                        $value = preg_replace('/"/', "'", $value);
                        if ($cont == sizeof($request->request)) {
                            fwrite($file, '"' . $key . '":"' . $value . '"');
                        } else {
                            fwrite($file, '"' . $key . '":"' . $value . '",');
                        }
                    }
                }
                fwrite($file, "}\n");
            } else if ($session->get('langFile') == '.lang') {
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
            }
            fclose($file);
            $zip = new ZipArchive();
            $dirRoot = '../public/var/downloads/' . $session->getId() . '/';
            if (!file_exists($dirRoot)) {
                mkdir($dirRoot, 0777, true);
            }
            $nombreArchivoZip = $dirRoot . substr($session->get('fileName'), 0, -4) . '_McMods-Translator.jar';
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
            $zip->close();
            //rmDir_rf($session->get('rutaExtraido'));
        }
        return $this->render('generator/generator.html.twig', [
            'session' => $session,
            'rutaDelDirectorio' =>  '/var/downloads/' . $session->getId() . '/' . substr($session->get('fileName'), 0, -4) . '_McMods-Translator.jar',
        ]);
    }
}
/**
 * Recorre una ruta de forma recursiva y elimina todo su contenido.
 *
 * @param $dir Ruta del directorio a eliminar
**/
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
