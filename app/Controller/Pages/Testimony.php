<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{
    /**
     * Obtém a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItens($request, &$obPagination)
    {
        // Depoimentos
        $itens = '';

        // Quantidade de registros
        $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página tual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 3);

        // Resultados da página
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obTestimony = $results->fetchObject(EntityTestimony::class))
        {
            $itens .= View::render('pages/testimony/item', [
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
            ]);
        }

        // Retorna os depoimentos
        return $itens;
    }

    /**
     * Retorna o conteúdo (view) de depoimentos
     * @param Request
     * @return string
     */
    public static function getTestimonies($request)
    {
        // View de Testimonies
        $content = View::render('pages/testimonies', [
            'itens' => self::getTestimonyItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ]);

        // Retorna a view da página
        return parent::getPage('WDev - DEPOIMENTOS', $content);
    }

    /**
     * Cadastra um depoimento
     * @param Request
     * @return string
     */
    public static function insertTestimony($request)
    {
        // Dados do POST
        $postVars = $request->getPostVars();

        // Nova instância de depoimentos
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();

        // Retorna a página de listagem de depoimentos
        return self::getTestimonies($request);
    }
}
