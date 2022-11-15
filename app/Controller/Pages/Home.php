<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page
{
    /**
     * Retorna o conteúdo (view) da Home
     * @return string
     */
    public static function getHome()
    {
        $obOrganization = new Organization();

        // View da HOME
        $content = View::render('pages/home', [
            'name' => $obOrganization->name
        ]);

        // Retorna a view da página
        return parent::getPage('WDev - HOME', $content);
    }
}
