<?php

namespace App\Http;

class Response
{
    /**
     * Código de status HTTP
     * @var integer
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do Response
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo retornado
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do Response
     * @var mixed
     */
    private $content;

    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }

    /**
     * Altera o content type do Response
     * @param string
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeaders('Content-Type', $contentType);
    }

    /**
     * Adiciona um registro no cabeçalho de Response
     * @param string $key
     * @param string $value
     */
    public function addHeaders($key, $value) {
        $this->headers[$key] = $value;
    }

    /**
     * Envia HEADERS para o navegador
     */
    private function sendHeaders()
    {
        // Define STATUS para o navegador
        http_response_code($this->httpCode);

        // Envia HEADERS para o navegador
        foreach ($this->headers as $key => $value) {
            header($key.': '.$value);
        }
    }

    /**
     * Envia resposta para o usuário
     */
    public function sendResponse()
    {
        // Envia HEADERS para o navegador
        $this->sendHeaders();

        // Imprime conteúdo da página
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;
            case 'application/json':                
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
        }
    }
}
