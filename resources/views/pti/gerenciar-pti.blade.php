@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PTI
        </h4>
        <div class="col-12 mt-3 row">
            <div class="col d-flex justify-content-start mb-3">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                    style="box-shadow: 3px 5px 6px #000000; margin:5px;">
                    Selecionar Grupo <i class="bi bi-funnel"></i>
                </button>

                <a href="/gerenciar-membro/{{ $selected_grupo }}">
                    <input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 3px 5px 6px #000000; margin:5px;" type="button"
                        value="Gerenciar Grupo">
                </a>
            </div>
        </div>
        {{-- Modal Filtros --}}
        <form action="/gerenciar-pti" class="form-horizontal" method="GET">
            <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:grey;color:white">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <center>
                                <div class="row col-10">
                                    <div class="col-12">
                                        Nome
                                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                                            value="{{ request('nome_pesquisa') }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        Grupos
                                        <select class="form-select status" id="4" name="grupo" type="number">
                                            @foreach ($dirigentes as $dirigente)
                                                <option value="{{ $dirigente->id }}"
                                                    {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>
                                                    {{ $dirigente->nome }} - {{ $dirigente->dia }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </center>
                        </div>
                        <br />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <a href="/gerenciar-pti?grupo={{ count($encaminhamentos) > 0 ? current($encaminhamentos)->id_reuniao : '' }}"
                                type="button" class="btn btn-secondary pesq">Limpar</a>
                            <button class="btn btn-primary pesq" type="submit">Confirmar</button>
                        </div>

                    </div>
                </div>
        </form>
        {{-- Fim modal filtros --}}
    </div>
    <hr>
    Total de assistidos: {{ $totalAssistidos }}
    <br />
    <span class="text-success" style="font-size: 20px;">&#9632;</span>
    <span style="font-size: 14px;">Presença Declarada</span>
    <div class="table">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th>NOME</th>
                <th>GRUPO</th>
                <th>STATUS</th>
                <th>AÇÕES</th>
            </tr>
            <tbody>
                @foreach ($encaminhamentos as $encaminhamento)
                    <tr class="{{ in_array($encaminhamento->id, $presencaHoje) ? 'table-success' : '' }}">
                        <td>{{ $encaminhamento->nome_completo }}</td>
                        <td>{{ $encaminhamento->nome }}</td>
                        {{-- <td>{{ $encaminhamento->h_inicio }}</td>
                        <td>{{ $encaminhamento->h_fim }}</td> --}}
                        <td>{{ $encaminhamento->status }}</td>
                        <td>
                            @if (in_array(45, session()->get('usuario.acesso')))
                                @if ($encaminhamento->id_status < 3)
                                    <a href="/reverter-faltas-assistido/{{ $encaminhamento->id }}"
                                        class="btn btn-outline-warning btn-sm tooltips">
                                        <span class="tooltiptext">Reverter faltas</span>
                                        <i class="bi bi-file-diff" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                @else
                                    <button class="btn btn-outline-warning btn-sm tooltips" disabled>
                                        <span class="tooltiptext">Reverter faltas</span>
                                        <i class="bi bi-file-diff" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @endif
                            @endif
                            @if ($encaminhamento->id_status == 1)
                                <button disabled type="button" class="btn btn-outline-danger btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modal{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Declarar Alta</span>
                                    Alta
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#modal{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Declarar Alta</span>
                                    Alta
                                </button>
                            @endif


                            @if ($encaminhamento->id_status == 1)
                                <button disabled type="button" class="btn btn-outline-success btn-sm tooltips"
                                    data-bs-toggle="modal" data-bs-target="#modalNutres{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Nutres</span>
                                     Nutres
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-success btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#modalNutres{{ $encaminhamento->id }}">
                                    <span class="tooltiptext">Nutres</span>
                                    Nutres
                                </button>
                            @endif

                            <a href="/visualizar-pti/{{ $encaminhamento->id }}" type="button"
                                class="btn btn-outline-primary btn-sm tooltips">
                                <span class="tooltiptext">Visualizar</span>
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>

                            {{--  Modal de Exclusao --}}
                            <div class="modal fade" id="modal{{ $encaminhamento->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:rgb(196, 27, 27);">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                Confirmação de
                                                Alta</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja declarar alta para <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $encaminhamento->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary"""
                                                href="/alta-pti/{{ $encaminhamento->id }}">Confirmar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim Modal de Exclusao --}}

                            {{--  Modal de Nutres --}}
                            <div class="modal fade" id="modalNutres{{ $encaminhamento->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:rgb(196, 27, 27);">
                                            <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                Confirmação de
                                                Alta Nutres </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja enviar para o Nutres? <br /><span
                                                style="color:rgb(196, 27, 27);">{{ $encaminhamento->nome_completo }}</span>&#63;

                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary"""
                                                href="/alta-nutres/{{ $encaminhamento->id }}">Confirmar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim Modal de Nutres --}}

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
@endsection
