<?php

namespace App\Http\Middleware;

class Api
{
    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Altera o content type
        $request->getRouter()->setContentType('application/json');

        // Executa o próximo nível do middleware
        return $next($request);
    }
}
