<?php

namespace App\Controller\Admin;

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
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obTestimony = $results->fetchObject(EntityTestimony::class))
        {
            $itens .= View::render('admin/modules/testimonies/item', [
                'id' => $obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
            ]);
        }

        // Retorna os depoimentos
        return $itens;
    }

    /**
    * Renderiza a view de listagem de depoimentos admin
    * @param Request
    * @return string 
    */
    public static function getTestimonies($request)
    {
        // Conteúdo da home adm
        $content = View::render('admin/modules/testimonies/index', [
            'itens' => self::getTestimonyItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Depoimentos ADM', $content, 'testimonies');
    }

    /**
     * Retorna o formulário de cadastro de novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewTestimony($request)
    {
        // Conteúdo do formulário
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Cadastrar depoimento',
            'nome' => '',
            'mensagem' => '',
            'status' => ''
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Novo Depoimento', $content, 'testimonies');
    }

    /**
     * Cadastra um novo depoimento no banco de dados
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request)
    {
        // Post vars
        $postVars = $request->getPostVars();

        // Nova instância de depoimento
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'] ?? '';
        $obTestimony->mensagem = $postVars['mensagem'] ?? '';
        $obTestimony->cadastrar();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/testimonies/' . $obTestimony->id . '/edit?status=created');
    }

    /**
     * Retorna a mensagem de status
     * @param Request
     * @return string
     */
    private static function getStatus($request)
    {
        // Query params
        $queryParams = $request->getQueryParams();

        // Status
        if (!isset($queryParams['status'])) return '';

        // Mensagens de status
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento excluído com sucesso!');
                break;
        }
    }

    /**
     * Retorna o formulário de edição de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request, $id)
    {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Editar depoimento',
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Editar Depoimento', $content, 'testimonies');
    }

    /**
     * Salva a edição de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditTestimony($request, $id)
    {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Post Vars
        $postVars = $request->getPostVars();

        // Atualiza a instância
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
        $obTestimony->atualizar();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
    }

    /**
     * Retorna o formulário de exclusão de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request, $id)
    {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/testimonies/delete', [
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Excluir Depoimento', $content, 'testimonies');
    }

 /**
     * Exclui um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteTestimony($request, $id)
    {
        // Obtém o depoimento do banco de dados
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // Valida a instância
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // Exclui o depoimento
        $obTestimony->excluir();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }
}
