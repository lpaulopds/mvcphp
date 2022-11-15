<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page
{
    /**
     * Retorna a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        // Status
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';        

        // Conteúdo da página de login
        $content = View::render('admin/login', [
            'status' => $status
        ]);

        // Retorna a página completa
        return parent::getPage('WDev - Login', $content);
    }

    /**
     * Define o login do usuário
     * @param Request
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // Busca usuário por e-mail
        $obUser = User::getUserByEmail($email);
        if (!$obUser instanceof User) {
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }

        if (!password_verify($senha, $obUser->senha)) {
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }

        // Cria sessão de login
        SessionAdminLogin::login($obUser);

        // Redireciona o usuário para a home de admin
        $request->getRouter()->redirect('/admin');
    }

    /**
     * Responsável por deslogar o usuário
     * @param Request
     */
    public static function setLogout($request)
    {
        // Destroi sessão de login
        SessionAdminLogin::logout();

        // Redireciona o usuário para a tela de login
        $request->getRouter()->redirect('/admin/login');
    }
}
