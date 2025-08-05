@extends('layouts.app')

@section('title')
    Gerenciar Encaminhamentos
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR ENCAMINHAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="row">



                    <form action="{{ route('gecdex') }}" class="form-horizontal mt-4" method="GET">
                        <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color:grey;color:white">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <center>
                                            <div class="row col-10">
                                                <div class ="col-12 mb-3">Data início
                                                    <input class="form-control" type="date" id="" name="dt_enc"
                                                        value="{{ $data_enc }}">
                                                </div>
                                                <div class="col-12 mb-3">Assistido
                                                    <input class="form-control" type="text" id="3" name="assist"
                                                        value="{{ $assistido }}">
                                                </div>
                                                <div class="col-12 mb-3">CPF
                                                    <input class="form-control" type="text" maxlength="11"
                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                        id="2" name="cpf" value="{{ $cpf }}">
                                                </div>
                                                <div class="col-12 mb-3">Status
                                                    <select class="form-select" id="4" name="status"
                                                        type="number">
                                                        <option value="{{ $situacao }}"></option>
                                                        @foreach ($stat as $status)
                                                            <option value="{{ $status->id }}"
                                                                {{ $status->id == request('status') ? 'selected' : '' }}>
                                                                {{ $status->descricao }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 mb-3">Tratamento
                                                    <select class="form-select" id="4" name="tratamento"
                                                        type="number">
                                                        <option value=""></option>

                                                        <option value="1" {{ 1 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Desobsessivo</option>
                                                        <option value="2" {{ 2 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Intensivo</option>
                                                        <option value="4" {{ 4 == request('tratamento') ? 'selected' : '' }}>Programa de Apoio a Portadores de Mediunidade Ostensiva</option>
                                                        <option value="6" {{ 6 == request('tratamento') ? 'selected' : '' }}>Tratamento Fluidoterápico Integral</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </center>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <a class="btn btn-secondary" href="/gerenciar-encaminhamentos">Limpar</a>
                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex align-items-center">
                        <a href="/gerenciar-tratamentos" class="btn btn-warning btn-sm"
                            style="box-shadow: 1px 2px 5px #000000; margin:5px;">Tratamentos</a>

                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                            style="box-shadow: 3px 5px 6px #000000; margin:5px;">
                            Pesquisar <i class="bi bi-funnel"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    <br />
    <hr />
    Total assistidos: {{ $contar }}
    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
        <thead style="text-align: center;">
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th class="col">Nr</th>
                <th class="col">DATA</th>
                <th class="col">PRIORIDADE</th>
                <th class="col">ASSISTIDO</th>
                <th class="col">REPRESENTANTE</th>
                <th class="col">TIPO TRATAMENTO</th>
                <th class="col">STATUS</th>
                <th class="col">AÇÕES</th>
            </tr>
        </thead>
        <tbody style="font-size: 14px; color:#000000; text-align: center;">
            @foreach ($lista as $listas)
                <tr>
                    <td>{{ $listas->ide }}</td>
                    <td>{{ date('d/m/Y', strtotime($listas->dh_chegada)) }}</td>
                    <td>{{ $listas->prdesc }}</td>
                    <td>{{ $listas->nm_1 }}</td>
                    <td>{{ $listas->nm_2 }}</td>
                    <td>{{ $listas->desctrat }}</td>
                    <td>{{ $listas->tsenc }}</td>
                    <td>
                        @if ($listas->status_encaminhamento == 1)
                            <a href="/agendar/{{ $listas->ide }}/{{ $listas->idtt }}"><button type="button"
                                    class="btn btn-outline-success btn-sm tooltips"><span
                                        class="tooltiptext">Agendar</span><i class="bi bi-clipboard-check"
                                        style="font-size: 1rem; color:#000;"></i></button></a>
                        @elseif($listas->status_encaminhamento < 3)
                            {{-- botao de alterar grupo --}}
                            <a href="/alterar-grupo-tratamento/{{ $listas->ide }}"type="button"
                                class="btn btn-outline-success btn-sm tooltips"><span class="tooltiptext">Alterar
                                    Grupo</span><i class="bi bi-arrow-left-right"
                                    style="font-size: 1rem; color:#000;"></i></a>
                        @else
                            <button type="button" class="btn btn-outline-success btn-sm" data-tt="tooltip"
                                data-placement="top" title="Alterar Grupo" disabled><i class="bi bi-arrow-left-right"
                                    style="font-size: 1rem; color:#000;"></i></button>
                        @endif
                        <a href="/visualizar-enc/{{ $listas->ide }}"><button type="button"
                                class="btn btn-outline-primary btn-sm tooltips"><span
                                    class="tooltiptext">Histórico</span><i class="bi bi-search"
                                    style="font-size: 1rem; color:#000;"></i></button></a>


                                    @if (in_array(50, session()->get('usuario.acesso')))
                                    @if ($listas->status_encaminhamento < 3)
                                        <button class="btn btn-outline-danger btn-sm tooltips" type="button" id=""
                                            data-bs-toggle="modal" data-bs-target="#inativar{{ $listas->ide }}"><span
                                                class="tooltiptext">Inativar</span><i class="bi bi-x-circle"
                                                style="font-size: 1rem; color:#000;"></i></button>
                                    @else
                                        <button class="btn btn-outline-danger btn-sm" type="button" id=""
                                            data-bs-toggle="modal" data-bs-target="#inativar{{ $listas->ide }}"
                                            data-tt="tooltip" data-placement="top" title="Inativar" disabled><i
                                                class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></button>
                                    @endif
                                    @endif
                            </td>

                    </td>

                    <form action="/inativar/{{ $listas->ide }}">
                        <div class="modal fade" id="inativar{{ $listas->ide }}" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color:#DC4C64;color:white">
                                        <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                        <button data-bs-dismiss="modal" type="button" class="btn-close"
                                            aria-label="Close"></button>
                                    </div>
                                    <br />

                                    <div class="modal-body">
                                        <center>
                                            <label for="recipient-name" class="col-form-label" style="font-size:17px">Tem
                                                certeza que deseja inativar:<br /><span
                                                    style="color:#DC4C64; font-weight: bold;">{{ $listas->nm_1 }}</span>&#63;</label>
                                            <br />
                                        </center>



                                        <center>
                                            <div class="mb-2 col-10">
                                                <label class="col-form-label">Insira o motivo da
                                                    <span style="color:#DC4C64">inativação:</span></label>
                                                <select class="form-select teste1" name="motivo" required>

                                                    @foreach ($motivo as $motivos)
                                                        <option value="{{ $motivos->id }}">{{ $motivos->tipo }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </center>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-bs-dismiss="modal"
                                            class="btn btn-danger">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- fim modal de inativação --}}
                    </form>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $lista->links('pagination::bootstrap-5') }}
    </div>

    </div>
    </div>
@endsection

@section('footerScript')
@endsection
