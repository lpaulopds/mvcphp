<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{
    /**
     * Obtém a renderização dos itens de usuários para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUsersItens($request, &$obPagination)
    {
        // Usuários
        $itens = '';

        // Quantidade de registros
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página tual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obUser = $results->fetchObject(EntityUser::class))
        {
            $itens .= View::render('admin/modules/users/item', [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ]);
        }

        // Retorna os usuários
        return $itens;
    }

    /**
    * Renderiza a view de listagem de usuários admin
    * @param Request
    * @return string 
    */
    public static function getUsers($request)
    {
        // Conteúdo da home adm
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUsersItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Usuários ADM', $content, 'users');
    }

    /**
     * Retorna o formulário de cadastro de novo usuário adm
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        // Conteúdo do formulário
        $content = View::render('admin/modules/users/form', [
            'title' => 'Cadastrar usuário',
            'nome' => '',
            'email' => '',
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Novo Usuário', $content, 'users');
    }

    /**
     * Cadastra um novo usuário no banco de dados
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        // Post vars
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // Valida o e-mail do usuário
        $obUser = EntityUser::getUserByEmail($email);
        if ($obUser instanceof EntityUser) {
            // Redireciona o usuário
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        // Nova instância de usuário
        $obUser = new EntityUser;
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->cadastrar();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=created');
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
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluído com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('Digite outro e-mail');
                break;
        }
    }

    /**
     * Retorna o formulário de edição de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id)
    {
        // Obtém usuário no banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/users/form', [
            'title' => 'Editar usuário',
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Editar Usuário', $content, 'users');
    }

    /**
     * Salva a atualização de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request, $id)
    {
        // Obtém usuário no banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Post Vars
        $postVars = $request->getPostVars();

        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // Valida o e-mail do usuário
        $obUserEmail = EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            // Redireciona o usuário
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        // Atualiza a instância
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }

    /**
     * Retorna o formulário de exclusão de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id)
    {
        // Obtém o usuário do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/users/delete', [
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ]);

        // Retorna a página completa
        return parent::getPanel('WDev - Excluir Usuário', $content, 'users');
    }

    /**
     * Exclui um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request, $id)
    {
        // Obtém o usuário do banco de dados
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Exclui o usuário
        $obUser->excluir();

        // Redireciona o usuário
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}
