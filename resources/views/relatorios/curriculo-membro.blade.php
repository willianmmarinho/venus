@extends('layouts.app')

@section('title')
    Currículo de Membro
@endsection

@section('content')
    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>
    <a href="/pdf-curriculo-medium/{{ $id }}" type="button" class="btn btn-info btn-floating btn-lg"
        id="btn-pdf-print">
        <i class="bi bi-cloud-download"></i>
    </a>

    <div class="container">
        <div class="justify-content-center">
            <br>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="mb-0">DADOS PESSOAIS</span>
                    <a  href="{{ session()->get('usuario.url') }}" class="btn btn-outline-danger btn-sm" title="Fechar">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="nome_completo" class="form-label">Nome do médium</label>
                                <input type="text" class="form-control" name="nome_completo"
                                    value="{{ $dadosP->nome_completo }}" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="dt_nascimento" class="form-label">Data de nascimento</label>
                            <input type="text" class="form-control" name="dt_nascimento"
                                value="{{ $dadosP->dt_nascimento ? date('d/m/Y', strtotime($dadosP->dt_nascimento)) : '' }}"
                                disabled>
                        </div>
                        <div class="col-2">
                            <label for="celular" class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="celular"
                                value="{{ $dadosP->descricao ? '(' . $dadosP->descricao . ')' : '' }} {{ $dadosP->celular }}"
                                disabled>
                        </div>
                        <div class="col-2">
                            <label for="nr_associado" class="form-label">Número de associado</label>
                            <input type="text" class="form-control" name="nr_associado"
                                value="{{ $dadosP->nr_associado }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($membros as $key => $membro)
                <br />
                <div class="card">
                    <div class="card-header">
                        {{ $key }}
                    </div>
                    <div class="card-body">
                        <table
                            class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                            <thead>
                                <tr style="background-color: #d6e3ff; font-size:12px; color:#000000; padding: 2px;">
                                    <th style="padding: 4px;">TRABALHO</th>
                                    <th style="padding: 4px;">FUNÇÃO</th>
                                    <th style="padding: 4px;">INICIO</th>
                                    <th style="padding: 4px;">FIM</th>
                                    <th style="padding: 4px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($membro as $dadoMembro)
                                    <tr>
                                        <td> {{ $dadoMembro->trabalho }} </td>
                                        <td> {{ $dadoMembro->nome_funcao }} </td>
                                        <td> {{ $dadoMembro->dt_inicio ? date('d/m/Y', strtotime($dadoMembro->dt_inicio)) : '-' }}
                                        </td>
                                        <td> {{ $dadoMembro->dt_fim ? date('d/m/Y', strtotime($dadoMembro->dt_fim)) : '-' }}
                                        </td>
                                        <td> {{ $dadoMembro->status_membro }} </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            <br>
            <div class="row">
                <div class="col">
                    <a class="btn btn-danger" href="{{ session()->get('usuario.url') }}" style="text-align:right;"
                        role="button">Fechar</a>
                </div>
            </div>

        </div>
    </div>
    </div>
    </div>
    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 120px;
            right: 20px;
            display: none;
        }
    </style>
    <style>
        #btn-pdf-print {
            position: fixed;
            bottom: 60px;
            right: 20px;
            display: block;
        }
    </style>
    <script>
        //Get the button
        let mybutton = document.getElementById("btn-back-to-top");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (
                document.body.scrollTop > 20 ||
                document.documentElement.scrollTop > 20
            ) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        // When the user clicks on the button, scroll to the top of the document
        mybutton.addEventListener("click", backToTop);

        function backToTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
@endsection
