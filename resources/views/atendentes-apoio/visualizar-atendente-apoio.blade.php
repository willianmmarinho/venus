@extends('layouts.app')
@section('title')
    Visualizar Atendentes Apoio
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Visualizar Atendentes Apoio
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-atendentes-apoio">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-md-6 col-12">
                                <div>Nome</div>

                                <input class="form-control" type="text" value="{{ $nomes[0]->nome_completo }}" Disabled>

                            </div>



                            <div class="table-responsive col-12">
                                <br />
                                <div class="table">
                                    <table
                                        class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col">Dia da Semana</th>
                                                <th scope="col">Data de Inicio</th>
                                                <th scope="col">Data Final</th>
                                                <th scope="col">Inicio</th>
                                                <th scope="col">Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($historico as $history)
                                                <tr>

                                                    <td>{{ $history->nome }}</td>
                                                    <td>

                                                        <div class="data_io">
                                                            {{ !is_null( $history->dt_inicio ) ? date('d-m-Y', strtotime( $history->dt_inicio ))  : '--' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="data_io">
                                                            {{ !is_null( $history->dt_fim ) ? date('d-m-Y', strtotime( $history->dt_fim ))  : '--' }}
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="data_io">
                                                            {{ date('G:i', strtotime($history->dh_inicio)) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="data_io">
                                                           {{ date('G:i', strtotime($history->dh_fim)) }}
                                                           </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <center>
                                <div class="col-12 mt-3">
                                    <a href="/gerenciar-atendentes-apoio" class="btn btn-danger col-3">
                                        Fechar
                                    </a>
                                </div>
                            </center>
                            </center>
                        @endsection
