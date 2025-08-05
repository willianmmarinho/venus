@extends('layouts.app')
@section('title', 'Gerenciar Assistidos Proamo')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ASSISTIDOS
            PROAMO
        </h4>

        <div class="col-12">
            <form action="/gerenciar-proamo" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        <label for="nome_pesquisa" class="form-label">Nome</label>
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                            value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-4">
                        <label for="grupo" class="form-label">Grupos</label>
                        <select class="form-select" id="grupo" name="grupo">
                            @foreach ($dirigentes as $dirigente)
                                <option value="{{ $dirigente->id }}"
                                    {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }} -
                                    {{ $dirigente->dia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button class="btn btn-light btn-sm" type="submit"
                                style="box-shadow: 1px 2px 5px #000000;">Pesquisar</button>
                            <a href="/gerenciar-proamo?grupo={{ $encaminhamentos ? current($encaminhamentos)->id_reuniao : '' }}"
                                class="btn btn-light btn-sm" style="box-shadow: 1px 2px 5px #000000;">Limpar</a>
                            <a href="/gerenciar-membro/{{ $selected_grupo }}" class="btn btn-primary btn-sm"
                                style="box-shadow: 1px 2px 5px #000000;">Gerenciar Grupo</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <hr>
        Total de assistidos: {{ $totalAssistidos }}
        <div class="col">
            <span class="text-danger" style="font-size: 20px;">&#9632;</span>
            <span style="font-size: 14px;">Assistidos sem PTD</span>
            <span class="text-warning" style="font-size: 20px;">&#9632;</span>
            <span style="font-size: 14px;">Assistidos em Avaliação</span>
        </div>
        <div class="col">
            <table class="table table-sm table-bordered border-secondary table-hover text-center align-middle">
                <thead style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <tr>
                        <th>NOME</th>
                        <th>GRUPO</th>
                        {{-- <th>SEMANAS</th> --}}
                        <th>FALTAS CONSECUTIVAS</th>
                        <th>STATUS</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>


                <tbody>
                    @foreach ($encaminhamentos as $encaminhamento)
                        {{-- (!$encaminhamento->ptd) --}}

                        <tr class="">
                            <td
                                style="{{ !$encaminhamento->ptd && !$encaminhamento->alta_ptd_proamo ? 'color:#dc3545; font-weight: bold;' : '' }}{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">
                                {{ $encaminhamento->nome_completo }}</td>
                            <td
                                style="{{ !$encaminhamento->ptd && !$encaminhamento->alta_ptd_proamo ? 'color:#dc3545; font-weight: bold;' : '' }}{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">
                                {{ $encaminhamento->nome }}</td>
                            {{-- <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold;' : '' }}{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">
                                @if ($encaminhamento->contagem == 0)
                                    - {{ $encaminhamento->contagem }}
                                @else
                                    {{ $encaminhamento->contagem }}
                                @endif
                            </td> --}}
                            <td
                                style="{{ !$encaminhamento->ptd && !$encaminhamento->alta_ptd_proamo ? 'color:#dc3545; font-weight: bold;' : '' }}{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">
                                {{ $encaminhamento->faltas }} </td>
                            <td
                                style="{{ !$encaminhamento->ptd && !$encaminhamento->alta_ptd_proamo ? 'color:#dc3545; font-weight: bold;' : '' }}{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">
                                {{ $encaminhamento->status }}</td>
                            <td
                                style="{{ ($encaminhamento->id_status < 3 and $encaminhamento->avaliacao < 91) ? 'background-color: #FFFF61' : '' }}">

                                @if ($encaminhamento->data == $now)
                                    <button type="button" class="btn btn-success tooltips btn-sm"><span
                                            class="tooltiptext">Presente</span><i class="bi bi-check2-circle"
                                            style="font-size: 1rem; color:#FFF;"></i></button>
                                @else
                                    <button type="button" class="btn btn-outline-danger tooltips btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#presenca{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Ausente</span><i class="bi bi-exclamation-triangle"
                                            style="font-size: 1rem; color:#000;"></i></button>
                                @endif

                                {{-- inicio da modal de presença --}}
                                <div class="modal fade closes" id="presenca{{ $encaminhamento->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <form method="post" action="/presenca-tratatamento/{{ $encaminhamento->id }}">
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
                                                                style="color:orange">{{ $encaminhamento->nome_completo }}</span>&#63;</label>
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

                                                    @if ($encaminhamento->dt_fim == $now or $encaminhamento->dt_fim == date('Y-m-d', strtotime($now . '-1 week')))
                                                        <button type="button" class="btn btn-primary openModal"
                                                            id="openModal" data-bs-toggle="modal" data-bs-dismiss="modal"
                                                            data-bs-target="#staticBackdrop{{ $listas->idtr }}">
                                                            Confirmar
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-primary">Confirmar
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="modal fade" id="staticBackdrop{{ $encaminhamento->id }}"
                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header"
                                                style="background-color:rgb(39, 91, 189);color:white">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">ATENÇÃO!</h1>
                                                <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label for="recipient-name" class="col-form-label"
                                                    style="font-size:17px">Este é
                                                    o {{ $encaminhamento->dt_fim == $now ? 'último' : null }}
                                                    {{ $encaminhamento->dt_fim == date('Y-m-d', strtotime($now . '-1 week')) ? 'penúltimo' : null }}
                                                    dia de tratamento
                                                    de:<br /><span
                                                        style="color: rgb(39, 91, 189)">{{ $encaminhamento->nome_completo }}</span></label>
                                                <br />

                                            </div>
                                            <div class="modal-footer">
                                                <button data-bs-dismiss="modal" type="button"
                                                    class="btn btn-danger">Cancelar</button>
                                                <button type="type" class="btn btn-primary">Confirmar Presença</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                {{-- fim da modal de presença --}}
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
                                @if ($encaminhamento->id_status != 1 and in_array(49, session()->get('usuario.acesso')))
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Declarar Alta</span>
                                        <i class="fa fa-person-walking" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @else
                                    <button type="button" disabled class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Declarar Alta</span>
                                        <i class="fa fa-person-walking fa-lg" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @endif
                                @if ($encaminhamento->id_status != 1 and in_array(49, session()->get('usuario.acesso')))
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modalB{{ $encaminhamento->id }}">
                                        <span class="tooltiptext" style="width: 130px">Declarar Alta PTD</span>
                                        <i class="fa-solid fa-award" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @else
                                    <button type="button" disabled class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modalB{{ $encaminhamento->id }}">
                                        <span class="tooltiptext" style="width: 150px">Declarar Alta PTD</span>
                                        <i class="fa-solid fa-award fa-lg" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @endif

                                <a href="/visualizar-proamo/{{ $encaminhamento->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Declarar Alta -->
                        <div class="modal fade" id="modalA{{ $encaminhamento->id }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form action="/alta-proamo/{{ $encaminhamento->ide }}" class="form-horizontal mt-4">
                                @csrf
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Declarar Alta</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <center>
                                            <div class="modal-body mb-2">
                                                Tem certeza que deseja declarar alta para <br /><span
                                                    style="color:rgb(196, 27, 27);">{{ $encaminhamento->nome_completo }}</span>&#63;
                                            </div>
                                            <div class="mb-2 col-10">
                                                <label class="col-form-label">Insira o motivo da
                                                    <span style="color:rgb(196, 27, 27);">Alta:</span></label>
                                                <select class="form-select motivo" id="motivo" name="motivo"
                                                    required>
                                                    @foreach ($motivosAlta as $motivoAlta)
                                                        <option value="{{ $motivoAlta->id }}">
                                                            {{ $motivoAlta->tipo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </center>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Confirmar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Modal Declarar Alta -->

                        <!-- Modal Declarar Alta PTD -->
                        <div class="modal fade" id="modalB{{ $encaminhamento->id }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                @csrf
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Declarar Alta PTD</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <center>
                                            <div class="modal-body">
                                                Tem certeza que deseja declarar a não necessidade do assistido participar de
                                                um PTD?
                                                <br />
                                                <br />
                                                <br />
                                                <span
                                                    style="color:rgb(196, 27, 27); font-size: 15px; font-style: italic">Essa
                                                    ação não inativa nenhum PTD ativo!</span>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <a href="/alta-ptd-proamo/{{ $encaminhamento->id }}" type="button"
                                                    class="btn btn-primary">Cofirmar</a>
                                            </div>
                                    </div>
                                </div>
                        </div>

                        <!-- Modal Declarar Alta PTD-->




        </div>
        @endforeach
        </tbody>
        </table>
    </div>
    </div>
    </div>

    <script>
        $('.motivo').prop('selectedIndex', -1)
    </script>
@endsection
