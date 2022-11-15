<?php

require __DIR__ . '/includes/app.php';

use \App\Http\Router;

// Ínicia o Router
$obRouter = new Router(URL);

// Ínclui as rotas de páginas
include __DIR__ . '/routes/pages.php';

// Ínclui as rotas de admin
include __DIR__ . '/routes/admin.php';

// Ínclui as rotas da API
include __DIR__ . '/routes/api.php';

// Imprime o response da rota
$obRouter->run()
        ->sendResponse();
