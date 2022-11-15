<?php

namespace App\Http\Middleware;

use \App\Utils\Cache\File as CacheFile;

class Cache
{
    /**
     * Verifica se request atual pode ser cacheada
     * @param Request
     * @return boolean
     */
    private function isCacheable($request)
    {
        // Valida o tempo de cache
        if (getenv('CACHE_TIME') <= 0) {
            return false;
        }

        // Valida o método da requisição
        if ($request->getHttpMethod() != 'GET') {
            return false;
        }

        // Valida o header de cache
        // O cliente define se o retorno vai ser ou não cacheado
        $headers = $request->getHeaders();
        if (isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache') {
            return false;
        }
        
        // Cacheável
        return true;
    }

    /**
     * Retorna a hash do cache
     * @param Request $request
     * @return string
     */
    private function getHash($request)
    {
        // URI da rota
        $uri = $request->getRouter()->getUri();

        // Query params
        $queryParams = $request->getQueryParams();
        $uri .= !empty($queryParams) ? '?'.http_build_query($queryParams) : '';

        // Remove as barras e retorna a hash
        return rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');
    }

    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Verifica se request atual é cacheável
        if(!$this->isCacheable($request)) return $next($request);

        $hash = $this->getHash($request);

        // Retorna os dados do cache
        return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use ($request,$next) {
            return $next($request);
        });
    }
}
