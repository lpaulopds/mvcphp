<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTAuth
{
    /**
     * Retorna instância de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request)
    {
        // Headers
        $headers = $request->getHeaders();

        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ','', $headers['Authorization']) : '';

        // Decode
        try {
            $decode = (array)JWT::decode($jwt, new Key(getenv('JWT_KEY'), 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Token inválido", 403);
        }

        $email = $decode['email'] ?? '';

        // Busca usuário pelo e-mail
        $obUser = User::getUserByEmail($email);

        // Retorna o usuário
        return $obUser instanceof User ? $obUser : false;
    }

    /**
     * Validar o acesso via JWT
     * @param Request $request
     */
    private function auth($request)
    {
        // Verifica usuário recebido na requisição
        if ($obUser = $this->getJWTAuthUser($request))
        {
            $request->user = $obUser;
            return true;
        }

        // Emite erro de senha inválida
        throw new \Exception("Acesso negado", 403);
    }

    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Realiza a validação do acesso a API via JWT
        $this->auth($request);

        // Executa o próximo nível do middleware
        return $next($request);
    }
}
