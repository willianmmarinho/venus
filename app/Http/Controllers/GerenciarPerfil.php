<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarPerfil extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{

        $perfis = DB::table('perfil');

        if($request->nome_pesquisa){
            $perfis =$perfis->where('descricao', 'ilike', "%$request->nome_pesquisa%");
        }

        $perfis = $perfis->orderBy('descricao')->get();
        return view('perfis.gerenciar-perfil', compact('perfis'));
    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try{
        $rotas = DB::table('tipo_rotas')->orderBy('tipo_rotas.nome', 'ASC')->get();
        return view('perfis.criar-perfil', compact('rotas'));
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( ));
	    return redirect()->back();
            }
        }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

        $perfil = DB::table('perfil')->insertGetId([
            'descricao' => $request->nome
        ]);

        foreach($request->rotas as $rota){
            DB::table('rotas_perfil')->insert([
                'id_perfil' => $perfil,
                'id_rotas' => $rota
            ]);
        }


        return redirect('/gerenciar-perfis');
    }

    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
        DB::rollBack();
    return redirect()->back();
            }
        }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{

        $perfil = DB::table('perfil')->where('id',$id)->first();
        $rotas = DB::table('rotas_perfil')->leftJoin('tipo_rotas', 'rotas_perfil.id_rotas', 'tipo_rotas.id')->where('id_perfil',$id)->orderBy('tipo_rotas.nome', 'ASC')->get();

        return view('perfis.visualizar-perfil', compact('perfil', 'rotas'));
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( ));
	    return redirect()->back();
            }
        }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try{
        $perfil = DB::table('perfil')->where('id',$id)->first();

        $rotas = DB::table('tipo_rotas')->orderBy('nome', 'ASC')->get();
        $rotasSelecionadas = DB::table('rotas_perfil')->leftJoin('tipo_rotas', 'rotas_perfil.id_rotas', 'tipo_rotas.id')->where('id_perfil',$id)->orderBy('id_rotas', 'ASC')->pluck('id_rotas');
        return view('perfis.editar-perfil', compact('perfil', 'rotas', 'rotasSelecionadas'));
    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( ));
	    return redirect()->back();
            }
        }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
        DB::table('perfil')->where('id', $id)->update([
            'descricao' => $request->nome
        ]);

        DB::table('rotas_perfil')->where('id_perfil', $id)->delete();

        foreach($request->rotas as $rota){

            DB::table('rotas_perfil')->where('id_perfil', $id)->insert([
                'id_perfil' => $id,
                'id_rotas' => $rota
            ]);
        }


        return redirect('/gerenciar-perfis');

    }
    catch(\Exception $e){

        app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
	    return redirect()->back();
            }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{

        DB::table('rotas_perfil')->where('id_perfil', $id)->delete();
        DB::table('perfil')->where('id', $id)->delete();
        return redirect('/gerenciar-perfis');

    }
catch(\Exception $e){

    app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
    DB::rollBack();
return redirect()->back();
        }
    }
}
