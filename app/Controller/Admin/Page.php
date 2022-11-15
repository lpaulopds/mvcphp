<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Page
{
    private static $modules = [
        'home' => [
            'label' => 'Home',
            'link' => URL.'/admin'
        ],
        'testimonies' => [
            'label' => 'Depoimentos',
            'link' => URL.'/admin/testimonies'
        ],
        'users' => [
            'label' => 'Usuários',
            'link' => URL.'/admin/users'
        ]
    ];

    /**
     * Retorna o conteúdo (view) da estrutura genérica da página do painel
     * @param string $title
     * @param string $content
     * @return string 
     */
    public static function getPage($title, $content)
    {
        return View::render('admin/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Renderiza a view do menu do painel
     * @param string
     * @return string
     */
    private static function getMenu($currentModule)
    {
        // Links do menu
        $links = '';

        // Itera os módulos
        foreach (self::$modules as $hash => $module) {
            $links .= View::render('admin/menu/link',[
                'label' => $module['label'],
                'link' => $module['link'],
                'current' => $hash == $currentModule ? 'text-danger' : ''
            ]);
        }

        // Retorna a renderização do menu
        return View::render('admin/menu/box', [
            'links' => $links
        ]);
    }

    /**
     * Renderiza a view do painel com conteúdos dinâmicos
     * @param string $title
     * @param string $content
     * @param string $currentModule
     * @return string 
     */
    public static function getPanel($title, $content, $currentModule)
    {
        // Renderiza a view do painel
        $contentPanel = View::render('admin/panel', [
            'menu' => self::getMenu($currentModule),
            'content' => $content
        ]);

        // Retorna a página renderizada
        return self::getPage($title, $contentPanel);
    }

    /**
     * Retorna um link da paginação
     * @param array $queryParams
     * @param array $page
     * @param string $url
     * @return
     */
    private static function getPaginationLink($queryParams, $page, $url, $label = null)
    {
        // Altera a página
        $queryParams['page'] = $page['page'];

        // Link
        $link = $url.'?'.http_build_query($queryParams);

        // View
        return View::render('admin/pagination/link', [
            'page' => $label ?? $page['page'],
            'link' => $link,
            'active' => $page['current'] ? 'active' : ''
        ]);
    }

    /**
     * Renderiza o layout de paginação
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        // Páginas
        $pages = $obPagination->getPages();

        // Verifica a quantidade de páginas
        if(count($pages) <= 1) return '';

        // Links paginação
        $links = '';

        // URL atual (sem GETS)
        $url = $request->getRouter()->getCurrentUrl();

        // GET
        $queryParams = $request->getQueryParams();

        // Página atual
        $currentPage = $queryParams['page'] ?? 1;

        // Limite de páginas
        $limit = getenv('PAGINATION_LIMIT');

        // Métade do limite de páginas
        $middle = ceil($limit/2);

        // Início da paginação
        $start = $middle > $currentPage ? 0 : $currentPage - $middle;

        // Ajusta o final da paginação
        $limit = $limit + $start;

        // Ajusta o início da paginação
        if ($limit > count($pages)) {
            $diff = $limit - count($pages);
            $start = $start - $diff;
        }

        // Link inicial
        if ($start > 0) {
            $links .= self::getPaginationLink($queryParams, reset($pages), $url, '<<');
        }

        // Renderiza os links
        foreach ($pages as $page)
        {
            // Verifica o start da paginação
            if($page['page'] <= $start) continue;

            // Verifica o limite de paginação
            if($page['page'] > $limit)
            {
                $links .= self::getPaginationLink($queryParams, end($pages), $url, '>>');
                break;
            }

            $links .= self::getPaginationLink($queryParams, $page, $url);
        }

        // Renderiza box de paginação
        return View::render('admin/pagination/box', [
            'links' => $links
        ]);
    }
}
