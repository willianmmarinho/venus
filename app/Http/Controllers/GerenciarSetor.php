<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarSetor extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
        $setores = DB::table('rotas_setor as rs')->leftJoin('setor as s', 'rs.id_setor', 's.id')->distinct('s.nome')->orderBy('s.nome');

        $pesquisa = $request->nome_pesquisa;
        if($request->nome_pesquisa){
            $setores =$setores->where(function($query) use ($pesquisa) {
                $query->where('nome', 'ilike', "%$pesquisa%");
                $query->orWhere('sigla', 'ilike', "%$pesquisa%");

            });
        }

        $setores = $setores->orderBy('s.nome')->get();
        return view('setor.gerenciar-setor', compact('setores'));
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
        $setores = DB::table('setor')->whereNull('dt_fim')->orderBy('nome')->get();
        $rotas = DB::table('tipo_rotas')->orderBy('tipo_rotas.nome', 'ASC')->get();
        return view('setor.criar-setor', compact('rotas', 'setores'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

        foreach($request->rotas as $rota){
            DB::table('rotas_setor')->insert([
                'id_setor' => $request->setor,
                'id_rotas' => $rota
            ]);
        }

        return redirect('/gerenciar-setor');

    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
        $setor = DB::table('setor')->where('id',$id)->first();
        $rotas = DB::table('rotas_setor')->leftJoin('tipo_rotas', 'rotas_setor.id_rotas', 'tipo_rotas.id')->where('id_setor',$id)->orderBy('tipo_rotas.nome', 'ASC')->get();

        return view('setor.visualizar-setor', compact('setor', 'rotas'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try{
        $setor = DB::table('setor')->where('id',$id)->first();
        $setores = DB::table('setor')->whereNull('dt_fim')->orderBy('nome')->get();
        $rotas = DB::table('tipo_rotas')->orderBy('tipo_rotas.nome', 'ASC')->get();
        $rotasSelecionadas = DB::table('rotas_setor')->leftJoin('tipo_rotas', 'rotas_setor.id_rotas', 'tipo_rotas.id')->where('id_setor',$id)->orderBy('tipo_rotas.nome', 'ASC')->pluck('id_rotas');

        return view('setor.editar-setor', compact('setor','setores', 'rotas', 'rotasSelecionadas'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{


        DB::table('rotas_setor')->where('id_setor', $id)->delete();

        foreach($request->rotas as $rota){
            DB::table('rotas_setor')->where('id_setor', $id)->insert([
                'id_setor' => $request->setor,
                'id_rotas' => $rota
            ]);
        }


        return redirect('/gerenciar-setor');

    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('tratamento-erro.erro-inesperado', compact('code'));
            }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

            DB::table('rotas_setor')->where('id_setor', $id)->delete();
            return redirect('/gerenciar-setor');

}
}
