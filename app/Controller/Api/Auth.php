<?php

namespace App\Controller\Api;

use \App\Model\Entity\User;
use \Firebase\JWT\JWT;

class Auth extends Api
{
    /**
     * Gera token JWT
     * @param Request $request
     * @return array
     */
    public static function generateToken($request)
    {
        // Post Vars (variáveis da requisição de POST)
        $postVars = $request->getPostVars();

        // Valida os campos obrigatórios de e-mail e senha
        if (!isset($postVars['email']) or !isset($postVars['email'])) {
            throw new \Exception("Insira os dados obrigatórios", 400);
        }

        // Busca usuário pelo email
        $obUser = User::getUserByEmail($postVars['email']);
        if (!$obUser instanceof User) {
            throw new \Exception("Requisição incorreta", 400);
        }

        // Valida a senha do usuário
        if (!password_verify($postVars['senha'], $obUser->senha)) {
            throw new \Exception("Requisição incorreta", 400);
        }

        // Payload JWT
        $payload = [
            'email' => $obUser->email
        ];

        // Retorna o token gerado
        return [
            'token' => JWT::encode($payload,getenv('JWT_KEY'), 'HS256')
        ];
    }
}
