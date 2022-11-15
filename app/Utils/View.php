<?php

namespace App\Utils;

class View
{
    /**
     * Variáveis padrões da View
     * @var array
     */
    private static $vars = [];

    /**
     * Define os dados iniciais da classe
     * @param array
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    /**
     * Valida se o arquivo de uma rota existe ou não 
     * e retorna o conteúdo de uma view
     * @param string $view
     * @return string 
     */
    private static function getContentView($view)
    {
        $file = __DIR__ . '/../../resources/view/' . $view . '.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Retorna conteúdo renderizado de uma view
     * A variável $vars é a que recebe as varáveis que são descritas nas views
     * @param string $view
     * @param array $vars (string/numeric)
     * @return string
     */
    public static function render($view, $vars = [])
    {
        // Conteúdo da view
        $contentView = self::getContentView($view);

        // Merge de variáveis da view
        $vars = array_merge(self::$vars, $vars);

        // Chaves do array de variáveis das views
        $keys = array_keys($vars);
        
        // Mapeia o array de variáveis para diferenciar as variáveis do conteúdo em si.
        $keys = array_map(function($item) {
            return '{{' . $item . '}}';
        }, $keys);

        // Retorna o conteúdo renderizado
        return str_replace($keys, array_values($vars), $contentView);
    }
}
