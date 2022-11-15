<?php

use \App\Http\Response;
use \App\Controller\Api;

// Rota de listagem de usuarios API
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api',
        'jwt-auth',
        'cache'
    ],
    function($request) {
        return new Response(200, Api\User::getUsers($request), 'application/json');
    }
]);

// Rota de consulta do usuário atual
$obRouter->get('/api/v1/users/me', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request) {
        return new Response(200, Api\User::getCurrentUser($request), 'application/json');
    }
]);

// Rota de consulta individual de usuarios API
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'jwt-auth',
        'cache'
    ],
    function($request, $id) {
        return new Response(200, Api\User::getUser($request, $id), 'application/json');
    }
]);

// Rota de cadastro de usuarios API
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request) {
        return new Response(201, Api\User::setNewUser($request), 'application/json');
    }
]);

// Rota de atualização de usuarios API
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request, $id) {
        return new Response(200, Api\User::setEditUser($request, $id), 'application/json');
    }
]);

// Rota de exclusão de usuarios API
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function($request, $id) {
        return new Response(200, Api\User::setDeleteUser($request, $id), 'application/json');
    }
]);
