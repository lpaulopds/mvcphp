<?php

use \App\Http\Response;
use \App\Controller\Admin;

// Rota de listagem de usuários
$obRouter->get('/admin/users', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\User::getUsers($request));
    }
]);

// Rota de novo usuário
$obRouter->get('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\User::getNewUser($request));
    }
]);

// Rota de novo usuário (POST)
$obRouter->post('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request) {
        return new Response(200, Admin\User::setNewUser($request));
    }
]);

// Rota de edição de um usuário
$obRouter->get('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\User::getEditUser($request, $id));
    }
]);

// Rota de edição de um usuário (POST)
$obRouter->post('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\User::setEditUser($request, $id));
    }
]);

// Rota de exclusão de um usuário
$obRouter->get('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\User::getDeleteUser($request, $id));
    }
]);

// Rota de exclusão de um usuário (POST)
$obRouter->post('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request, $id) {
        return new Response(200, Admin\User::setDeleteUser($request, $id));
    }
]);
