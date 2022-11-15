<?php

namespace App\Controller\Api; 

class Api
{
    /**
     * Método responsável por retornar os detalhes da API
     * @param Request $request
     * @return array
    */
    public static function getDetails($request)
    {
        return [
            'nome' => 'API - Wdev',
            'versao' => 'v1.0.0',
            'autor' => 'Canal Wdev',
            'email' => 'domemail@dominio.com'
        ];
    }

    /**
     * Retorna os detalhes da paginação
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    protected static function getPagination($request, $obPagination)
    {
        // Query params
        $queryParams = $request->getQueryParams();
        
        // Página
        $pages = $obPagination->getPages();

        // Retorno do detalhes
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadePaginas' => !empty($pages) ? count($pages) : 1
        ];
    }
}
