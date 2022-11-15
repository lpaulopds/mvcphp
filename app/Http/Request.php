<?php

namespace App\Http;

class Request
{
    /**
     * Instância do Router
     * @var Router
     */
    private $router;

    /**
     * Método HTTP da requisição
     * @var string
     */
    private $httpMethod;

    /**
     * URI da página
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas no POST da página ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição
     * @var array
     */
    private $headers = [];

    /**
     * Construtor da classe
     */
    public function __construct($router)
    {
        $this->router       = $router;
        $this->queryParams  = $_GET ?? '';
        $this->headers      = getallheaders();
        $this->httpMethod   = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }
    
    /**
     * Define as varáveis de POST
     */
    private function setPostVars()
    {
        // Verifica o método da requisição
        if ($this->httpMethod == 'GET') return false;

        // POST padrão
        $this->postVars = $_POST ?? [];

        // POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }

    private function setUri()
    {
        // URI completa (com GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        // Remove GETS da URI
        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    /**
     * Retorna a instância de Router
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Retorna o método HTTP da requisição
     * @return string
     */
    public function getHttpMethod() {
        return $this->httpMethod;
    }

    /**
     * Retorna a URI da requisição
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Retorna os HEADERS da requisição
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Retorna os parâmetros da URL da requisição
     * @return array
     */
    public function getQueryParams() {
        return $this->queryParams;
    }

    /**
     * Retorna as variáveis POST da requisição
     * @return array
     */
    public function getPostVars() {
        return $this->postVars;
    }
}
