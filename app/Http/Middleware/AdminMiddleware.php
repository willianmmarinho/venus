<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $perfil = session()->get('usuario.perfis');
        $perfil = explode(',', $perfil);
        if(in_array(1, $perfil)){
            return $next($request);
        }


            app('flasher')->addError("Você não tem autorização para acessar esta página");
            return redirect('/login/valida');

    }
}
