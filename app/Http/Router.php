<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router
{
    /**
     * URL completa do projeto (raiz)
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';

    /**
     * Índice de rotas
     * @var array
     */
    private $routes = [];

    /**
     * Instância de Request
     * @var Request
     */
    private $request;

    /**
     * Content type padrão do response
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Inicia a classe
     * @param string
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Altera o valor de content type
     * @param string $contentType
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    /**
     * Define o prefixo das rotas
     */
    private function setPrefix()
    {
        // Informações da URL atual
        $parseUrl = parse_url($this->url);

        // Define o prefixo
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Adiciona uma rota na classe
     * @param string $method
     * @param string @route
     * @param array $params
     */
    private function addRoute($method, $route, $params = [])
    {
        // Validação dos parâmetros
        foreach ($params as $key => $value)
        {
            if ($value instanceof Closure)
            {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        // Middlewares da rota
        $params['middlewares'] = $params['middlewares'] ?? [];

        // Variáveis da rota
        $params['variables'] = [];

        // Padrão de validação das variáveis das rotas
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches))
        {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        // Remove a barra no final da rota
        $route = rtrim($route, '/');

        // Padrão de validação da URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        // Adiciona a rota dentro da classe
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Define uma rota de GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = []) {
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Define uma rota de POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = []) {
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Define uma rota de PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = []) {
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Define uma rota de DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = []) {
        return $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Retorna a URI desconsiderando o prefixo
     * @return string
     */
    public function getUri()
    {
        // URI da request
        $uri = $this->request->getUri();

        // Fatia a URI com o prefixo
        // *Retorna o que sobra da contagem de $prefix
        //  com o explode de $prefix e $uri se não, retorna $uri
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        // Retorna URI sem prefixo
        return rtrim(end($xUri), '/');
    }

    /**
     * Retorna os dados da rota atual
     * @return array
     */
    private function getRoute()
    {
        // URI
        $uri = $this->getUri();

        // Método HTTP
        $httpMethod = $this->request->getHttpMethod();

        // Valida as rotas
        foreach ($this->routes as $patternRoute => $methods)
        {
            // Verifica se a URI bate com o padrão
            if (preg_match($patternRoute, $uri, $matches)) {
                // Verifica o método
                if (isset($methods[$httpMethod])) {
                    // Remove a primeira posição do array
                    unset($matches[0]);

                    // Variáveis processadas
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    // Retorno dos parâmetros da rota
                    return $methods[$httpMethod];
                }
                // Método não permitido
                throw new Exception('Método não permitido', 405);
            }
        }
        // URL não encontrada
        throw new Exception('URL não encontrada', 404);
    }

    /**
     * Executa a rota atual
     * @return Response
     */
    public function run()
    {
        try {
            // Obtém a rota atual
            $route = $this->getRoute();

            // Verifica o controlador
            if (!isset($route['controller'])) {
                throw new Exception('URL não pode ser processada', 500);
            }

            // Argumentos da função
            $args = [];

            // Reflection
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            // Retorna a execução da fila middleware
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
        }
        catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    /**
     * Retorna mensagem de erro de acordo com o content type
     * @param string $message
     * @return mixed
     */
    private function getErrorMessage($message)
    {
        switch ($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];
                break;
            
            default:
                return $message;
                break;
        }   
    }

    /**
     * Retorna a URL atual
     * @return string
     */
    public function getCurrentUrl() {
        return $this->url.$this->getUri();
    }

    /**
     * Redireciona a URL
     * @param string
     */
    public function redirect($route)
    {
        // URL
        $url = $this->url . $route;

        // Executa o redirect
        header('location: ' . $url);
        exit;
    }
}
