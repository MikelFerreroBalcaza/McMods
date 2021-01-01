<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use SplFileInfo;
use Symfony\Component\Validator\Constraints\Length;
use ZipArchive;

class TranslatorController extends AbstractController

{

    /**
     * @Route("/translator", name="translator")
     */
    public function index(Request $request): Response
    {
        $langsCodes = [
            'af_za',
            'ar_sa',
            'ast_es',
            'az_az',
            'ba_ru',
            'bar',
            'be_by',
            'bg_bg',
            'br_fr',
            'brb',
            'bs_ba',
            'ca_es',
            'cs_cz',
            'cy_gb',
            'da_dk',
            'de_at',
            'de_ch',
            'de_de',
            'el_gr',
            'en_au',
            'en_ca',
            'en_gb',
            'en_nz',
            'en_pt',
            'en_ud',
            'en_us',
            'enp',
            'enws',
            'eo_uy',
            'es_ar',
            'es_cl',
            'es_ec',
            'es_es',
            'es_mx',
            'es_uy',
            'es_ve',
            'esan',
            'et_ee',
            'eu_es',
            'fa_ir',
            'fi_fi',
            'fil_ph',
            'fo_fo',
            'fr_ca',
            'fr_fr',
            'fra_de',
            'fy_nl',
            'ga_ie',
            'gd_gb',
            'gl_es',
            'got_de',
            'gv_im',
            'haw_us',
            'he_il',
            'hi_in',
            'hr_hr',
            'hu_hu',
            'hy_am',
            'id_id',
            'ig_ng',
            'io_en',
            'is_is',
            'isv',
            'it_it',
            'ja_jp',
            'jbo_en',
            'ka_ge',
            'kab_kab',
            'kk_kz',
            'kn_in',
            'ko_kr',
            'ksh',
            'kw_gb',
            'la_la',
            'lb_lu',
            'li_li',
            'lol_us',
            'lt_lt',
            'lv_lv',
            'mi_nz',
            'mk_mk',
            'mn_mn',
            'moh_ca',
            'ms_my',
            'mt_mt',
            'nds_de',
            'nl_be',
            'nl_nl',
            'nn_no',
            'nb_no',
            'nuk',
            'oc_fr',
            'oj_ca',
            'ovd',
            'pl_pl',
            'pt_br',
            'pt_pt',
            'qya_aa',
            'ro_ro',
            'ru_ru',
            'scn',
            'sme',
            'sk_sk',
            'sl_si',
            'so_so',
            'sq_al',
            'sr_sp',
            'sv_se',
            'swg',
            'sxu',
            'szl',
            'ta_in',
            'th_th',
            'tlh_aa',
            'tr_tr',
            'tt_ru',
            'tzl_tzl',
            'uk_ua',
            'val_es',
            'vec_it',
            'vi_vn',
            'yi_de',
            'yo_ng',
            'zh_cn',
            'zh_tw',
        ];
        $session = $request->getSession();
        $error = 0;
        if (!$request->get('langform')) {
            if ($request->files->get('userfile')) {
                $session->set('fileName', $request->files->get('userfile')->getClientOriginalName());
                $session->set('fileRout', $request->files->get('userfile')->getPathName());
            }
            $nombre_archivo = $session->get('fileName');
            $ruta_archivo = $session->get('fileRout');
            $ruta_extraido = '../public/var/uploads/' . $session->getid() . '/' . $nombre_archivo;
            $session->set('rutaExtraido', $ruta_extraido);
            $extension = $request->files->get('userfile')->getClientOriginalExtension();
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
            iconLang($request, '../public/img/svg/');
            return $this->render('translator/selectorLanguage.html.twig', [
                'errorCode' => $error,
                'langFiles' => $session->get('langFiles'),
                'request' => $request->files->get('userfile')->getPathName(),
                'files' => $session->get('fileName'),
                'file' => $session->get('fileRout'),
                'langFilessadds' => $session->getid(),
                'iconlist' => $session->get('iconlist'),
                'langsCodes' => $langsCodes,
                'langsFile' => $session->get('langFile'),
            ]);
        } else {
            if ($session->get('langFile') == '.lang') {
                $lang1 = $request->get('lang1');
                $lang2 = $request->get('lang2');
                $session->set('lang1', $lang1);
                $session->set('lang2', $lang2);
                $arrLang1 = [];
                $arrLang2 = [];
                $arr = [];
                $lang1 = $session->get('langDir') . $lang1;
                $fp1 = fopen($lang1, 'r');
                while (!feof($fp1)) {
                    $line = fgets($fp1);
                    if (!empty($line)) {
                        if (!($line[0] == '#')) {
                            $item = explode('=', $line);
                            array_push($arr, $item[0]);
                            if (isset($item[1])) {
                                $arrLang1[$item[0]] = $item[1];
                            }
                        }
                    }
                }
                if (in_array($lang2, $session->get('langFiles'))) {
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
                $session->set('codelist', $arr);
            } elseif ($session->get('langFile') == '.json') {
                $lang1 = $request->get('lang1');
                $lang2 = $request->get('lang2');
                $session->set('lang1', $lang1);
                $session->set('lang2', $lang2);
                $arrLang1 = [];
                $arrLang2 = [];
                $arr = [];
                $lang1file = $session->get('langDir') . $lang1;
                $data = file_get_contents($lang1file);
                $products = json_decode($data, true);
                foreach ($products as $key => $product) {
                    $arrLang1[$key] = $product;
                    array_push($arr, $key);
                }
                if (in_array($lang2, $session->get('langFiles'))) {
                    $lang2file = $session->get('langDir') . $lang2;
                    $data = file_get_contents($lang2file);
                    $products = json_decode($data, true);
                    foreach ($products as $key => $product) {
                        $arrLang2[$key] = $product;
                    }
                }
                $session->set('codelist', $arr);
            } else {
            }
            return $this->render('translator/translatorLanguage.html.twig', [
                'mcmodInfo' => $session->get('mcmod_info'),
                'langFile' => $session->get('langFile'),
                'arrLang1' => $arrLang1,
                'arrLang2' => $arrLang2,
                'lang1' => $session->get('lang1'),
                'lang2' => $session->get('lang2'),
                'session' => $session->all(),
                'request' => $request,
            ]);
        }
    }
}
/**
 * Recorre una ruta de forma recursiva y encuentra el directorio 'lang', ademas si existe el
 * fichero 'mcmod.info' los recorre y genera un array con todos sus datos, también almacena la
 * extensión de los ficheros de idioma
 *
 * @param $request Objeto request proporcionado por Symfony
 * @param $ruta Ruta del archivo descomprimido
**/
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
                        $fileLang = scandir($dr);
                        for ($i = 0; $i < count($fileLang); $i++) {
                            if (substr($fileLang[$i], -5) == '.lang' || substr($fileLang[$i], -5) == '.json') {
                                $session->set('langFile', substr($fileLang[$i], -5));
                            } else {
                                $session->set('langFile', 'error');
                            }
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
/**
 * Recorre una ruta de forma recursiva y almacena en un arry los idiomas con los que cuenta el complemento
 *
 * @param $request Objeto request proporcionado por Symfony
 * @param $ruta Ruta de la carpeta 'lang'
**/
function iconLang($request, $ruta)
{
    $session = $request->getSession();
    $arr = [];
    if (is_dir($ruta)) {
        if ($dh = opendir($ruta)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    array_push($arr, strtolower($file));
                }
            }
        }
        $session->set('iconlist', $arr);
    }
}
