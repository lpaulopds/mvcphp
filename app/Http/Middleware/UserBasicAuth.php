<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;

class UserBasicAuth
{
    /**
     * Retorna instância de usuário autenticado
     * Se comunica com os dados http para obter os dados usuário e senha
     * @return User
     */
    private function getBasicAuthUser()
    {
        // Verifica dados de acesso
        if (!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        // Busca usuário pelo e-mail
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        // Verfica instância
        if (!$obUser instanceof User) {
            return false;
        }

        // Valida a senha e retorna o usuário
        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }

    /**
     * Validar o acesso via HTTP BASIC AUTH
     * @param Request $request
     */
    private function basicAuth($request)
    {
        // Verifica usuário recebido na requisição
        if ($obUser = $this->getBasicAuthUser())
        {
            $request->user = $obUser;
            return true;
        }

        // Emite erro de senha inválida
        throw new \Exception("Requisição incorreta", 403);
    }

    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Realiza a validação do acesso a API via Basic Auth
        $this->basicAuth($request);

        // Executa o próximo nível do middleware
        return $next($request);
    }
}
