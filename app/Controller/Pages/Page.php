<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page
{
    /**
     * Renderiza o header da página
     * @return string
     */
    private static function getHeader() {
        return View::render('pages/header');
    }

    /**
     * Renderiza o footer da página
     * @return string
     */
    private static function getFooter() {
        return View::render('pages/footer');
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
        return View::render('pages/pagination/link', [
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
        foreach ($pages as $page) {
            // Verifica o start da paginação
            if ($page['page'] <= $start) continue;

            // Verifica o limite de paginação
            if ($page['page'] > $limit)
            {
                $links .= self::getPaginationLink($queryParams, end($pages), $url, '>>');
                break;
            }

            $links .= self::getPaginationLink($queryParams, $page, $url);
        }

        // Renderiza box de paginação
        return View::render('pages/pagination/box', [
            'links' => $links
        ]);
    }

    /**
     * Retorna o conteúdo (view) da página genérica
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('pages/page', [
            'title' => $title,
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter()
        ]);
    }
}
