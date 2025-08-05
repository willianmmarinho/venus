<?php

namespace App\Http\Controllers;

use App\Mail\EnviarEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarEmailController extends Controller
{
    
    public function edit(Request $request, $idm, $id_cronograma)
    {
        $trabalhador = session()->get('usuario.nome');

        $destino = DB::table('membro AS m')
                    ->leftJoin('associado AS a', 'a.id', 'm.id_associado')
                    ->join('pessoas AS p', 'a.id_pessoa', 'p.id')
                    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', 'tf.id')
                    ->leftJoin('cronograma AS cro', 'm.id_cronograma','cro.id')
                    ->leftJoin('grupo AS g', 'cro.id_grupo', 'g.id')
                    // JOIN auto-relacionado para obter o dirigente, baseado em mesma cronograma
                    ->leftJoin('membro AS md', function ($join) use ($id_cronograma) {
                        $join->on('md.id_cronograma', 'cro.id')
                            ->where('md.id_funcao', 1);
                    })
                    ->leftJoin('associado AS asd', 'asd.id', 'md.id_associado')
                    ->leftJoin('pessoas AS pd', 'pd.id','asd.id_pessoa')
                    ->leftJoin('tp_ddd AS td', 'p.ddd', 'td.id')
                    ->leftJoin('tp_ddd AS ddt', 'pd.ddd', 'ddt.id')
                    ->leftJoin('tipo_dia as tds', 'cro.dia_semana', 'tds.id')
                    ->where('m.id', $idm)
                    ->where('cro.id', $id_cronograma)
                    ->select(
                        'p.nome_completo',
                        'm.id AS idm',
                        'm.id_associado',
                        'p.email AS emailv',
                        'p.celular',
                        'td.descricao AS dddvol',
                        'ddt.descricao',
                        'm.id_cronograma',
                        'tds.sigla',
                        'cro.h_inicio',
                        'cro.h_fim',
                        'tf.nome AS nome_funcao',
                        'g.nome AS nome_grupo',
                        DB::raw("(CASE WHEN m.dt_fim > '1969-06-12' THEN 'Inativo' ELSE 'Ativo' END) AS status"),
                        // email do dirigente se existir
                        DB::raw("CASE WHEN md.id IS NOT NULL THEN pd.email ELSE NULL END AS emaild"),
                        // nome do dirigente se existir
                        DB::raw("CASE WHEN md.id IS NOT NULL THEN pd.nome_completo ELSE NULL END AS nomedirigente"),
                        DB::raw("CASE WHEN md.id IS NOT NULL THEN ddt.descricao ELSE NULL END AS ddddirigente"),
                        DB::raw("CASE WHEN md.id IS NOT NULL THEN pd.celular ELSE NULL END AS celulardirigente")
                    )
                    ->get();
 

                    session()->put('destino', $destino[0], $id_cronograma);

    

            return view('email.informa-novo-voluntario', compact('destino', 'trabalhador'));
     
    }

    public function send(Request $request)
    {
    
      // dd($request->emaild);
        $destino = session()->get('destino');

        $id_cronograma = $destino->id_cronograma ?? null;

        $request->validate([
        'emailv' => 'required|email',
        'emailo' => 'required|email',
        'emaild' => 'nullable|email',
        'name' => 'required|string',
        'subject' => 'required|string',
        'message' => 'required|string',
        ]);

        // Inicia a construção do objeto de email
        $mailable = Mail::to($request->input('emailv'));

        // Adiciona o emaild como BCC, SOMENTE SE ele existir e for um email válido
        $emailDirigente = $request->input('emaild');
        if ($emailDirigente && filter_var($emailDirigente, FILTER_VALIDATE_EMAIL)) {
            $mailable->bcc($emailDirigente);
        }

        // Envia o e-mail com a instância configurada do Mailer
        $mailable->send(new EnviarEmail([
            'fromName' => $request->input('name'),
            'fromEmail' => $request->input('emailo'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'emaild' => $emailDirigente, 
            'nomedir'=> $request->input('nomedir')
        ]));

        return redirect("/gerenciar-membro/$id_cronograma")->with('success', 'E-mail enviado com sucesso!');


    }
        
}
