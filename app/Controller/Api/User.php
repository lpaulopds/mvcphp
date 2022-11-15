<?php

namespace App\Controller\Api;

use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Api
{
    /**
     * Obtém a renderização dos itens de usuários para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request, &$obPagination)
    {
        // Depoimentos
        $itens = [];

        // Quantidade de registros
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityUser::getUsers(null, 'id ASC', $obPagination->getLimit());

        // Renderiza o item
        while ($obUser = $results->fetchObject(EntityUser::class))
        {
            $itens[] = [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ];
        }

        // Retorna os usuários
        return $itens;
    }

    /**
     * Método responsável por retornar os usuários cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers($request)
    {
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Retorna os detalhes de um usuario
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getUser($request, $id)
    {
        // Valida o id do depoimento
        if (!is_numeric($id)) {
            throw new \Exception("O depoimento '" . $id . "' não é válido", 400);
        }

        // Busca usuário
        $obUser = EntityUser::getUserById($id);

        // Valida se o usuário existe
        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuário " . $id . " não foi encontrado", 404);
        }

        // Retorna os detalhes do depoimento
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    public static function getCurrentUser($request)
    {
        // Usuário atual
        $obUser = $request->user;
        
        // Retorna os detalhes do usuário
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Cadastra um novo usuário
     * @param Request $request
     */
    public static function setNewUser($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();

        // Valida campos obrigatórios
        if (!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new \Exception("Nome, E-mail e Senha são obrigatórios", 400);
        }

        // Valida a duplicação de e-mail
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if ($obUserEmail instanceof EntityUser) {
            throw new \Exception("O e-mail '".$postVars['email']."' já está em uso", 400);
        }

        // Novo usuário
        $obUser = new EntityUser;
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->cadastrar();

        // Retorna os detalhes de usuário cadastrado
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Atualiza um usuário
     * @param Request $request
     */
    public static function setEditUser($request, $id)
    {
        // POST VARS
        $postVars = $request->getPostVars();

        // Valida campos obrigatórios
        if (!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new \Exception("Nome, E-mail e Senha são obrigatórios", 400);
        }

        // Busca usuário
        $obUser = EntityUser::getUserById($id);

        // Valida se o usuário existe
        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuário " . $id . " não foi encontrado", 404);
        }

        // Valida a duplicação de e-mail
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id) {
            throw new \Exception("O e-mail '".$postVars['email']."' já está em uso", 400);
        }

        // Atualiza o usuário
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->atualizar();

        // Retorna os detalhes do usuário cadastrado
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Exclui um usuário
     * @param Request $request
     */
    public static function setDeleteUser($request, $id)
    {
        // Busca usuário
        $obUser = EntityUser::getUserById($id);

        // Valida se o usuário existe
        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuário " . $id . " não foi encontrado", 404);
        }

        if ($obUser->id == $request->user->id) {
            throw new \Exception("Não é possível excluir usuário conectado", 400);
        }

        // Exclui um usuário
        $obUser->excluir();

        // Retorna o sucesso da exclusão
        return [
            'sucesso' => true
        ];
    }
}
