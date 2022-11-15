<?php

namespace App\Http\Middleware;

class Queue
{
    /**
     * Mapeamento de middleware
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento de middlewares que serão carregados em todas as rotas
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares que serão executados
     * @var array
     */
    private $middlewares = [];

    /**
     * Função de execução do controlador
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos da função de controller
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Constrói a classe de fila de middleware
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Método para definir o mapeamento de middlewares
     * @param array
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * Método para definir o mapeamento de middlewares padrões
     * @param array
     */
    public static function setDefault($default)
    {
        self::$default = $default;
    }

    /**
     * Executa o próximo nível da fila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request)
    {
        // Verifica se a fila de middleware está vazia
        if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        // Middlewares
        $middleware = array_shift($this->middlewares);

        // Verfica mapeamento
        if(!isset(self::$map[$middleware])) {
            throw new \Exception("Problemas ao processar o middleware da requisição", 500);
        }

        // Next
        $queue = $this;
        $next = function($request) use($queue) {
            return $queue->next($request);
        };

        // Executa o middleware
        return (new self::$map[$middleware])->handle($request, $next);
    }
}
