<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page
{
   /**
    * Renderiza a view de home do painel adm
    * @param Request
    * @return string 
    */
    public static function getHome($request)
    {
        // Conteúdo da home adm
        $content = View::render('admin/modules/home/index', []);

        // Retorna a página completa
        return parent::getPanel('WDev - Home ADM', $content, 'home');
    }
}
