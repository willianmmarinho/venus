@extends('layouts.app')

@section('title')
    Gerenciar Presença Entrevista
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR PRESENÇA ENTREVISTA</h4>
        <div class="col-12">
            <br />
            <div class="row justify-content-center">
                <form action="{{ route('listas') }}" class="form-horizontal" method="GET">
                    <div class="row align-items-center">
                        <div class="col-5">
                            Nome
                            <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                                placeholder="Pesquisar nome" value="{{ request('nome_pesquisa') }}">
                        </div>
                        <div class="col-2">
                            Data Marcada
                            <input class="form-control" type="date" id="data" name="data"
                                value="{{ request('nome_pesquisa') }}">
                        </div>
                        <div class="col-auto">
                            <input class="btn btn-light btn-sm me-md-2 mt-3"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="submit"
                                value="Pesquisar">
                            <a href="/gerenciar-presenca">
                                <input class="btn btn-light btn-sm me-md-2 mt-3"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="button"
                                    value="Limpar">
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <hr />
            {{-- <div class="table">Total assistidos: {{$contar}} --}}
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col">ASSISTIDO</th>
                        <th class="col">DIA</th>
                        <th class="col">HORÁRIO INICIO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    <tr>
                        @foreach ($lista as $listas)
                            <td>{{ $listas->id }}</td>
                            <td>{{ $listas->nome_completo }}</td>
                            <td>{{ date('d/m/Y', strtotime($listas->dh_marcada)) }}</td>
                            <td>{{ date('H:i', strtotime($listas->dh_marcada)) }}</td>
                            <td>


                                <button type="button" class="btn btn-outline-warning btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modal{{ $listas->id }}">
                                    <span class="tooltiptext">Presença</span>
                                    <i class="bi bi-exclamation-triangle" style="font-size: 1rem; color:#000;"></i>
                                </button>

                                {{--  Modal de Presença --}}
                                <div class="modal fade" id="modal{{ $listas->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: rgba(255, 165, 0, 1);">

                                                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Confirmar
                                                    Presença</h5>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja declarar a presença para <br /><span
                                                    style="color:rgba(255, 165, 0, 1);">{{ $listas->nome_completo }}</span>&#63;

                                            </div>
                                            <div class="modal-footer mt-2">
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <a type="button" class="btn btn-primary"
                                                    href="/criar-presenca/{{ $listas->id }}">Confirmar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Fim Modal de Presença --}}
                            </td>



                            </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div class="d-flex justify-content-center">
    </div>

    </div>
    </div>
@endsection

@section('footerScript')
@endsection
