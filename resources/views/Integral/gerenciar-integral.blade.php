@extends('layouts.app')
@section('title', 'Gerenciar Assistidos Integral')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ASSISTIDOS
            INTEGRAL
        </h4>

        <div class="col-12">
            <form action="/gerenciar-integral" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-4">
                        Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa"
                            value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-4">
                        Grupos

                        <select class="form-select status" id="4" name="grupo" type="number">
                            @foreach ($dirigentes as $dirigente)
                                <option value="{{ $dirigente->id }}"
                                    {{ $dirigente->id == $selected_grupo ? 'selected' : '' }}>{{ $dirigente->nome }} -
                                    {{ $dirigente->dia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        @if (request('grupo'))
                            <a href="/gerenciar-integral?grupo={{ request('grupo') }}"><input
                                    class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                        @else
                            <a href="/gerenciar-integral"><input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar"></a>
                        @endif
                        <a href="/gerenciar-membro/{{ $selected_grupo }}"><input class="btn btn-primary btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Gerenciar Grupo"></a>


                    </div>
                </div>

            </form>

        </div>

        <hr>
        Total de assistidos: {{ $totalAssistidos }}
        <br>
        <span class="text-danger" style="font-size: 20px;">&#9632;</span>
        <span style="font-size: 14px;">Assistidos sem PTD</span>
        <div class="table">
            <table class="table table-sm table-bordered border-secondary table-hover text-center align-middle">
                <thead style="background-color: #d6e3ff; font-size:14px; color:#000000">

                    @if (in_array(36, session()->get('usuario.acesso')))
                        <th>ID</th>
                    @endif
                    <th>NOME</th>
                    <th>SEMANAS REALIZADAS</th>
                    <th>PRESENÇAS</th>
                    <th>FALTAS CONSECUTIVAS</th>
                    <th>STATUS</th>
                    <th>MACA</th>
                    <th>AÇÕES</th>
                    </tr>

                <tbody>
                    @foreach ($encaminhamentos as $encaminhamento)
                        <tr class="{{ $encaminhamento->ptd }}">
                            @if (in_array(36, session()->get('usuario.acesso')))
                                <td>{{ $encaminhamento->id }}
                                </td>
                            @endif
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}">
                                {{ $encaminhamento->nome_completo }}</td>
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}">
                                @if ($encaminhamento->contagem == null and $encaminhamento->contagem !== 0)
                                    Permanente
                                @elseif ($encaminhamento->contagem == 0)
                                    -
                                @else
                                    {{ $encaminhamento->contagem }}
                                @endif
                            </td>
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}"> {{ $encaminhamento->presenca }} </td>
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}"> {{ $encaminhamento->faltas }} </td>
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}">
                                {{ $encaminhamento->status }}</td>
                            <td style="{{ !$encaminhamento->ptd ? 'color:#dc3545; font-weight: bold' : '' }}">
                                {{ $encaminhamento->maca }}</td>

                            <td>

                            @if ($encaminhamento->data == $now)
                            <button type="button" class="btn btn-success tooltips btn-sm"
                            ><span class="tooltiptext">Presente</span><i class="bi bi-check2-circle"
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
                                                    @if ($encaminhamento->dt_fim == $now or $encaminhamento->dt_fim == date('Y-m-d', strtotime($now . '+1 week')))
                                                        <button type="button" class="btn btn-primary openModal"
                                                            id="openModal" data-bs-toggle="modal" data-bs-dismiss="modal"
                                                            data-bs-target="#staticBackdrop{{ $encaminhamento->id }}">
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
                                                    {{ $encaminhamento->dt_fim == date('Y-m-d', strtotime($now . '+1 week')) ? 'penúltimo' : null }}
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
                                @if ($encaminhamento->dt_fim == null)
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modalA{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Declarar Alta</span>
                                        <i class="fa fa-person-walking" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @elseif($encaminhamento->id_status == 2)
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#modal{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Sem limite</span>
                                        <i class="bi bi-infinity" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"disabled
                                        data-bs-toggle="modal" data-bs-target="#modal{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Sem limite</span>
                                        <i class="bi bi-infinity" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @endif

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
                                    <!-- Button trigger modal (Desabilitado) -->
                                    <button type="button" style="font-size: 1rem; color:#000;"
                                        class="btn btn-outline-warning btn-sm tooltips" data-bs-toggle="modal"
                                        data-bs-target="#maca{{ $encaminhamento->id }}" disabled>
                                        <span class="tooltiptext">Maca</span>
                                        <i class="fa fa-bed" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @else
                                    <!-- Button trigger modal (Ativo) -->
                                    <button type="button" style="font-size: 1rem; color:#000;"
                                        class="btn btn-outline-warning btn-sm tooltips" data-bs-toggle="modal"
                                        data-bs-target="#maca{{ $encaminhamento->id }}">
                                        <span class="tooltiptext">Maca</span>
                                        <i class="fa fa-bed" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                @endif


                                <a href="/visualizar-integral/{{ $encaminhamento->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                                <form action="/maca-integral/{{ $encaminhamento->id }}">
                                    <!-- Modal -->
                                    <div class="modal fade" id="maca{{ $encaminhamento->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:orange">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel"
                                                        style=" color:white">
                                                        Indicar Maca</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <center>
                                                        <div class="row col-10">
                                                            <div class="mb-3">
                                                                <label for="recipient-name" class="col-form-label"
                                                                    style="font-size:17px">Designar maca
                                                                    para:<br /><span
                                                                        style="color:orange">{{ $encaminhamento->nome_completo }}</span>&#63;</label>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="col-form-label">Escolha a maca
                                                                    <span style="color:orange">desejada:</span></label>
                                                                <?php $i = 0; ?>
                                                                <select class="form-select" id="maca" name="maca"
                                                                    type="number">
                                                                    @foreach ($macasDisponiveis as $maca)
                                                                        @if ($encaminhamento->maca < $maca and $encaminhamento->maca > $i)
                                                                            <option value="{{ $encaminhamento->maca }}"
                                                                                selected>
                                                                                {{ $encaminhamento->maca }}</option>
                                                                        @endif
                                                                        <option value="{{ $maca }}">
                                                                            {{ $maca }}</option>
                                                                        {{ $i = $maca }};
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </center>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                {{--  Modal de Exclusao --}}
                                <form action="">
                                    <div class="modal fade" id="modal{{ $encaminhamento->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:#DC4C64">
                                                    <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                        Retirar
                                                        Tempo Limite</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja retirar o limite de semanas de <br /><span
                                                        style="color:#DC4C64; font-weight: bold;">{{ $encaminhamento->nome_completo }}</span>&#63;
                                                </div>
                                                <div class="modal-footer mt-2">
                                                    <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <a type="button" class="btn btn-primary"
                                                        href="/infinito-integral/{{ $encaminhamento->ide }}">Confirmar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                {{-- Fim Modal de Exclusao --}}

                                {{--  Modal de Exclusao --}}
                                <div class="modal fade" id="modalA{{ $encaminhamento->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:#DC4C64">
                                                <h5 class="modal-title" id="exampleModalLabel" style=" color:white">
                                                    Declarar Alta</h5>
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
                                                <a type="button" class="btn btn-primary"
                                                    href="/alta-integral/{{ $encaminhamento->ide }}">Confirmar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Fim Modal de Exclusao --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection
