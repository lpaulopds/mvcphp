<?php
namespace App\Http\Middleware;

class Maintenance
{
    /**
     * Executa o middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Verifica o estado de manutenção da página
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception("Página em manutenção. Tente mais tarde.", 200); 
        }

        // Executa o próximo nível do middleware
        return $next($request);
    }
}
