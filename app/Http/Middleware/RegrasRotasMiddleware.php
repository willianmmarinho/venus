<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;

class RegrasRotasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next 
     */

    public function caminhoUsuario(String $request, Bool $acesso)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
        $nome = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $metodo = $_SERVER['HTTP_USER_AGENT'];
        $rota = str_replace(url('/'), '', url()->full());
        $timestamp = Carbon::now();
        $usuario = session()->get('usuario.id_usuario');

        if (preg_match('/(android|iphone|ipad|ipod|blackberry|windows phone|opera mini|mobile)/i', $_SERVER['HTTP_USER_AGENT'])) {
            DB::table('log_caminho_usuario')->insert([
                'rota_acessada' => $rota,
                'request' => $request,
                'metodo_acesso' => $metodo,
                'data_hora' => $timestamp,
                'permitido_acesso' => $acesso,
                'id_usuario' => $usuario
            ]);
        } else {
            DB::table('log_caminho_usuario')->insert([
                'rota_acessada' => $rota,
                'request' => $request,
                'ip_acesso' => $ip,
                'nome_acesso' => $nome,
                'metodo_acesso' => $metodo,
                'data_hora' => $timestamp,
                'permitido_acesso' => $acesso,
                'id_usuario' => $usuario
            ]);
        }
    }

    
    public function handle(Request $request, Closure $next, Mixed $rota): Response
    {

        try {
            $rotasAutorizadas = session()->get('usuario.acesso');
            session()->put('acessoAtual', $rota);
            if (!$rotasAutorizadas) {
                app('flasher')->addError('É necessário fazer login para acessar!');
                $this->caminhoUsuario(json_encode($request->all()), false);
                return redirect('/');
            } else if (count(explode('-', $rota)) > 1) {

                foreach (explode('-', $rota) as $id) {
                    if (in_array($id, $rotasAutorizadas)) {
                        $this->caminhoUsuario(json_encode($request->all()), true);
                        return $next($request);
                    }
                }
                app('flasher')->addError('Você não tem autorização para acessar esta funcionalidade!');
                $this->caminhoUsuario(json_encode($request->all()), false);
                return redirect('/login/valida');
            } elseif (in_array(current(explode('-', $rota)), $rotasAutorizadas)) {
                $this->caminhoUsuario(json_encode($request->all()), true);
                return $next($request);
            } else {
                app('flasher')->addError('Você não tem autorização para acessar esta funcionalidade!');
                $this->caminhoUsuario(json_encode($request->all()), false);
                return redirect('/login/valida');
            }
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um Erro Inesperado!!');
            return redirect('/login/valida');
        }
    }
}
