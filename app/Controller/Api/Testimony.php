<?php

namespace App\Controller\Api;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api
{
    /**
     * Obtém a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItems($request, &$obPagination)
    {
        // Depoimentos
        $itens = [];

        // Quantidade de registros
        $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página tual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obTestimony = $results->fetchObject(EntityTestimony::class))
        {
            $itens[] = [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data
            ];
        }

        // Retorna os depoimentos
        return $itens;
    }

    /**
     * Método responsável por retornar os depoimnetos
     * @param Request $request
     * @return array
    */
    public static function getTestimonies($request)
    {
        return [
            'depoimentos' => self::getTestimonyItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Retorna os detalhes de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getTestimony($request, $id)
    {
        // Valida o id do depoimento
        if (!is_numeric($id)) {
            throw new \Exception("O depoimento '" . $id . "' não é válido", 400);
        }
        // Busca depoimento
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida se o depoimento existe
        if (!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento " . $id . " não foi encontrado", 404);
        }

        // Retorna os detalhes do depoimento
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * Cadastra um novo depoimento
     * @param Request $request
     */
    public static function setNewTestimony($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();

        // Valida campos obrigatórios
        if (!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Nome e Mensagem são obrigatórios", 400);
        }

        // Novo depoimento
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();
        

        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * Atualiza um depoimento
     * @param Request $request
     */
    public static function setEditTestimony($request, $id)
    {
        // POST VARS
        $postVars = $request->getPostVars();

        // Valida campos obrigatórios
        if (!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Nome e Mensagem são obrigatórios", 400);
        }

        // Busca depoimento no banco
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento " . $id . " não foi encontrado", 404);
        }

        // Atualiza o depoimento
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->atualizar();

        // Retorna os detalhes do depoimento atualizado
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data
        ];
    }

    /**
     * Exclui um depoimento
     * @param Request $request
     */
    public static function setDeleteTestimony($request, $id)
    {
        // Busca depoimento no banco
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento " . $id . " não foi encontrado", 404);
        }

        // Exclui o depoimento
        $obTestimony->excluir();

        // Retorna o sucesso da exclusão
        return [
            'sucesso' => true
        ];
    }
}
