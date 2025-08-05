@extends('layouts.app')

@section('title')
    Gerenciar Tratamentos
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR TRATAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('gtcdex') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">

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

                                                    <div class ="col-12">Data início
                                                        <input class="form-control pesquisa" type="date" id="dt_enc"
                                                            name="dt_enc" value="{{ $data_enc, old('dt_enc') }}">
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        Dia
                                                        <select class="form-select teste pesquisa" id=""
                                                            name="dia" type="number">
                                                            @foreach ($dia as $dias)
                                                                <option value="{{ $dias->id }}"
                                                                    {{ $diaP == $dias->id ? 'selected' : '' }}
                                                                    {{ $dias->id == old('dia') ? 'selected' : '' }}>
                                                                    {{ $dias->nome }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-12 mt-3">Assistido
                                                        <input class="form-control pesquisa" type="text" id="3"
                                                            name="assist" value="{{ old('assist') }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">CPF
                                                        <input class="form-control" type="text" maxlength="11"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            id="2" name="cpf" value="{{ $cpf }}">
                                                    </div>
                                                    <div class="col-12 mt-3">Grupo
                                                        <input class="form-control pesquisa" autocomplete="off"
                                                            id="grupo" name="grupo" type="text" list="grupos"
                                                            value="{{ $cron }}">
                                                        <datalist id="grupos">
                                                            @foreach ($cronogramas as $cronograma)
                                                                <option
                                                                    value="{{ $cronograma->id }} - {{ $cronograma->nome }} - {{ $cronograma->dia }} - {{ $cronograma->h_inicio }} - {{ $cronograma->setor }}">
                                                            @endforeach
                                                        </datalist>

                                                    </div>
                                                    <div class="col-12 mt-3">Tratamento
                                                        <select class="form-select pesquisa" id="4" name="tratamento"
                                                            type="number">

                                                            <option value=""></option>
                                                            <option value="1" {{ 1 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Desobsessivo</option>
                                                            <option value="2" {{ 2 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Intensivo</option>
                                                            <option value="4" {{ 4 == request('tratamento') ? 'selected' : '' }}>Programa de Apoio a Portadores de Mediunidade Ostensiva</option>
                                                            <option value="6" {{ 6 == request('tratamento') ? 'selected' : '' }}>Tratamento Fluidoterápico Integral</option>

                                                        </select>
                                                    </div>
                                                    <div class="col-12 mt-3">Status
                                                        <select class="form-select teste1" id="4" name="status"
                                                            type="number">
                                                            @foreach ($stat as $status)
                                                                <option value="{{ $status->id }}"
                                                                    {{ $situacao == $status->id ? 'selected' : '' }}
                                                                    {{ old('status') == $status->id ? 'selected' : '' }}>
                                                                    {{ $status->nome }}
                                                                </option>
                                                            @endforeach
                                                            <option value="all"
                                                                {{ $situacao == 'all' ? 'selected' : '' }}>
                                                                Todos os Status
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </center>




                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a class="btn btn-secondary" href="/gerenciar-tratamentos">Limpar</a>
                                            <button type="submit" class="btn btn-primary">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="col">
                                <br />
                                <div class="row">
                                    <div class="col d-flex align-items-center flex-wrap">
                                        @if (in_array(38, session()->get('usuario.acesso')))
                                            <a href="/incluir-avulso" class="btn btn-danger btn-sm"
                                                style="box-shadow: 1px 2px 5px #000000; margin:5px;">Atendimento de Emergência</a>
                                        @endif

                                        <a href="/gerenciar-encaminhamentos" class="btn btn-warning btn-sm"
                                            style="box-shadow: 1px 2px 5px #000000; margin:5px;">Encaminhamentos</a>

                                        @if (in_array(17, session()->get('usuario.acesso')))
                                            <a href="/job">
                                                <input class="btn btn-info-emphasis btn-sm me-md-2"
                                                    style="box-shadow: 1px 1px 3px #000000; margin:5px;" type="button"
                                                    value="Job">
                                            </a>
                                        @endif

                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#filtros" style="box-shadow: 3px 5px 6px #000000; margin:5px;">
                                            Pesquisar <i class="bi bi-funnel"></i>
                                        </button>
                                    </div>
                                </div>
                    </form>
                </div>
            </div>
        </div>
        <br />


    </div style="text-align:right;">
    <hr />
    Total assistidos: {{ $contar }}
    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
        <thead style="text-align: center;">
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th class="col">Nr</th>
                <th class="col">PRIORIDADE</th>
                <th class="col">ASSISTIDO</th>
                <th class="col">REPRESENTANTE</th>
                <th class="col">DIA</th>
                <th class="col">HORÁRIO</th>
                <th class="col">TRATAMENTO</th>
                <th class="col">GRUPO</th>
                <th class="col">STATUS</th>
                <th class="col">AÇÕES</th>
            </tr>
        </thead>
        <tbody style="font-size: 14px; color:#000000; text-align: center;">
            <tr>
                @foreach ($lista as $listas)
                    <td>{{ $listas->idtr }}</td>
                    <td>{{ $listas->prdesc }}</td>
                    <td>{{ $listas->nm_1 }}</td>
                    <td>{{ $listas->nm_2 }}</td>
                    <td>{{ $listas->nomed }}</td>
                    <td>{{ date('H:i', strtotime($listas->h_inicio)) }}</td>
                    <td>{{ $listas->sigla }}</td>
                    <td>{{ $listas->nomeg }}</td>
                    <td>{{ $listas->tst }}</td>
                    <td>




                        @if ($listas->status == 1 or $listas->status == 2)
                            {{-- Botão de presença --}}
                            <button type="button" class="btn btn-outline-success tooltips btn-sm" data-bs-toggle="modal"
                                data-bs-target="#presenca{{ $listas->idtr }}">
                                <span class="tooltiptext">Presença</span><i class="bi bi-exclamation-triangle"
                                    style="font-size: 1rem; color:#000;"></i></button>
                        @else
                            <button type="button" class="btn btn-outline-success tooltips btn-sm" data-bs-toggle="modal"
                                data-bs-target="#presenca{{ $listas->idtr }}" disabled>
                                <span class="tooltiptext">Presença</span><i class="bi bi-exclamation-triangle"
                                    style="font-size: 1rem; color:#000;"></i></button>
                        @endif

                        {{-- inicio da modal de presença --}}
                        <div class="modal fade closes" id="presenca{{ $listas->idtr }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form method="post" action="/presenca-tratatamento/{{ $listas->idtr }}">
                                @csrf
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:orange;color:white">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Presença
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label"
                                                    style="font-size:17px">Tem certeza que deseja registrar
                                                    presença para<br /><span
                                                        style="color:orange">{{ $listas->nm_1 }}</span>&#63;</label>
                                            </div>
                                            <center>
                                                <div class="mb-2 col-10">
                                                    <label class="col-form-label">Insira o número de acompanhantes,
                                                        <span style="color:orange">se necessário:</span></label>
                                                    <input type="number" class="form-control" name="acompanhantes"
                                                        placeholder="0" min="0">
                                                </div>
                                            </center>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>

                                            @if ($listas->dt_fim == $now or $listas->dt_fim == date('Y-m-d', strtotime($now . '-1 week')))
                                                <button type="button" class="btn btn-primary openModal" id="openModal"
                                                    data-bs-toggle="modal" data-bs-dismiss="modal"
                                                    data-bs-target="#staticBackdrop{{ $listas->idtr }}">
                                                    Registrar Presença
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-primary">Confirmar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal fade" id="staticBackdrop{{ $listas->idtr }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color:rgb(39, 91, 189);color:white">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">ATENÇÃO!</h1>
                                        <button data-bs-dismiss="modal" type="button" class="btn-close"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="recipient-name" class="col-form-label" style="font-size:17px">Este é
                                            o {{ $listas->dt_fim == $now ? 'último' : null }}
                                            {{ $listas->dt_fim == date('Y-m-d', strtotime($now . '-1 week')) ? 'penúltimo' : null }}
                                            dia de tratamento
                                            de:<br /><span
                                                style="color: rgb(39, 91, 189)">{{ $listas->nm_1 }}</span></label>
                                        <br />

                                    </div>
                                    <div class="modal-footer">
                                        <button data-bs-dismiss="modal" type="button"
                                            class="btn btn-danger">Cancelar</button>
                                        <button type="type" class="btn btn-primary">Confirmar Presença</button>
                                    </div>
                                </div>
                            </div>
                            {{-- fim da modal de presença --}}
                        </div>
                        </form>

                        @if (in_array(45, session()->get('usuario.acesso')))
                            @if ($listas->status < 3)
                                <a href="/reverter-faltas-assistido/{{ $listas->idtr }}"
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
                        <a href="/visualizar-tratamento/{{ $listas->idtr }}" type="button"{{-- botão de histórico --}}
                            class="btn btn-outline-primary btn-sm tooltips">
                            <span class="tooltiptext">Histórico</span>
                            <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></a>

                            @if (in_array(50, session()->get('usuario.acesso')))
                        @if ($listas->status == 1 or $listas->status == 2)
                            {{-- botao de inativar --}}
                            <a type="button" class="btn btn-outline-danger btn-sm tooltips"
                                data-bs-target="#inativa{{ $listas->idtr }}" data-bs-toggle="modal"><span
                                    class="tooltiptext">Inativar</span><i class="bi bi-x-circle"
                                    style="font-size: 1rem; color:#000;"></i></a>
                        @else
                            <button type="button" class="btn btn-outline-danger btn-sm" data-tt="tooltip"
                                data-placement="top" data-bs-target="#inativa{{ $listas->idtr }}" data-bs-toggle="modal"
                                title="Inativar" disabled><i class="bi bi-x-circle"
                                    style="font-size: 1rem; color:#000;"></i></button>
                        @endif
                        @endif
                        {{-- modal de inativação --}}
                        <form action="/inativar-tratamento/{{ $listas->ide }}">
                            <div class="modal fade" id="inativa{{ $listas->idtr }}" data-bs-keyboard="false"
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
                                            <label for="recipient-name" class="col-form-label" style="font-size:17px">Tem
                                                certeza que deseja inativar:<br /><span
                                                    style="color:#DC4C64; font-weight: bold;">{{ $listas->nm_1 }}</span>&#63;</label>
                                            <br />

                                            <center>
                                                <div class="mb-2 col-10">
                                                    <label class="col-form-label">Insira o motivo da
                                                        <span style="color:#DC4C64">inativação:</span></label>
                                                    <select class="form-select teste1" name="motivo" required>

                                                        @foreach ($motivo as $motivos)
                                                            <option value="{{ $motivos->id }}">
                                                                {{ $motivos->tipo }} </option>
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

                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $lista->links('pagination::bootstrap-5') }}
    </div>
    </div>
    </div>


    <script>
        $(document).ready(function() {


            if ({{ $diaP == null }}) { //Deixa o select status como padrao vazio
                $(".teste").prop("selectedIndex", -1);
            }

        });
    </script>
     <script>
        $(document).ready(function() {
            if ({{ $situacao == null }}) { //Deixa o select de status para Todos quando se pesquisa
                $(".teste1").prop("selectedIndex", 1);
            }
            $('.pesquisa').change(function() {
                $(".teste1").prop("selectedIndex", 6);
            })

        });
    </script>
@endsection
