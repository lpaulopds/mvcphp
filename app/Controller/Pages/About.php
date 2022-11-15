<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class About extends Page
{
    /**
     * Retorna o conteúdo (view) da página Sobre
     * @return string
     */
    public static function getAbout()
    {
        $obOrganization = new Organization();

        // View da HOME
        $content = View::render('pages/about', [
            'name' => $obOrganization->name,
            'description' => $obOrganization->description,
            'site' => $obOrganization->site
        ]);

        // Retorna a view da página
        return parent::getPage('WDev - SOBRE', $content);
    }
}
