<?php

use \App\Http\Response;
use \App\Controller\Pages;

// Rota de HOME
$obRouter->get('/', [
    'middlewares' => [
        'cache'
    ],
    function() {
        return new Response(200, Pages\Home::getHome());
    }
]);

// Rota de About
$obRouter->get('/sobre', [
    'middlewares' => [
        'cache'
    ],
    function() {
        return new Response(200, Pages\About::getAbout());
    }
]);

// Rota de Testimony
$obRouter->get('/depoimentos', [
    'middlewares' => [
    'cache'
],
    function($request) {
        return new Response(200, Pages\Testimony::getTestimonies($request));
    }
]);

// Rota de Testimony (INSERT)
$obRouter->post('/depoimentos', [
    function($request) {
        return new Response(200, Pages\Testimony::insertTestimony($request));
    }
]);
