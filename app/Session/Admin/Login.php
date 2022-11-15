<?php

namespace App\Session\Admin;

class Login
{
    /**
     * Inicia a sessão de usuário
     */
    private static function init()
    {
        // Verifica se a sessão não está ativa
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Cria o login do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser)
    {
        // Inicia a sessão
        self::init();

        // Define a sessão do usuário
        // Sempre defina sessões como arrays
        $_SESSION['admin']['usuario'] = [
            'id' => $obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];

        // Sucesso
        return true;
    }

    /**
     * Verifica se usuário está logado
     * @return boolean
     */
    public static function isLogged()
    {
        // Inicia a sessão
        self::init();

        // Retorna a verificação
        return isset($_SESSION['admin']['usuario']['id']);
    }

    /**
     * Executa o logout do usuário
     * @return boolean
     */
    public static function logout()
    {
        // Inicia a sessão
        self::init();

        // Desloga o usuário
        unset($_SESSION['admin']['usuario']);

        // Sucesso
        return true;
    }
}
