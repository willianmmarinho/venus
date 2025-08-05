@extends('layouts.app')
@section('title', 'Gerenciar Membros')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR GRUPO -
            {{ Str::upper($grupo->nome) }} ({{ $grupo->nsigla }}) - {{ Str::upper($grupo->dia) }}
            {{ date('H:i', strtotime($grupo->h_inicio)) }}/{{ date('H:i', strtotime($grupo->h_fim)) }}
        </h4>

        <div class="col-12">
            <form action="/gerenciar-membro/{{ $id }}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class=" col-xxl-4 col-lg-12">
                        <label for="nome_pesquisa">Nome</label>
                        <select class="form-select select2" id="nome_pesquisa" name="nome_pesquisa" data-width="100%">
                            <option value=""></option>
                            @foreach ($membro as $membros)
                                <option value="{{ $membros->nome_completo }}"
                                    {{ request('nome_pesquisa') == $membros->nome_completo ? 'selected' : '' }}>
                                    {{ $membros->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" col-xxl-4 col-lg-12">
                        <label for="status">Status</label>
                        <select class="form-select select2" id="status" name="status" data-width="100%">
                            <option value=""></option>
                            @foreach ($statu as $status)
                                <option value="{{ $status->nome }}"
                                    {{ request('status') == $status->nome ? 'selected' : '' }}>
                                    {{ $status->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xxl-1 col-lg-4 mt-3">
                        <input class="btn btn-light col-12 btn-sm mt-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="submit" value="Pesquisar">
                    </div>
                    <div class="col-xxl-1 col-lg-4">
                        <a href="/gerenciar-membro/{{ $id }}" class="btn btn-light col-12 btn-sm mt-4"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="submit"
                            type="button">Limpar</a>
                    </div>
                    <div class="col-xxl-1 col-lg-4">
                        <a href="/gerenciar-grupos-membro" class="btn btn-primary col-12 btn-sm mt-4"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;" type="button">Retornar</a>
                    </div>
                    <!-- Botão para abrir o modal -->
                    @if (in_array(13, session()->get('usuario.acesso')))
                        <div class="col-xxl-1 col-lg-4">
                            <button type="button" class="btn btn-success col-12 btn-sm mt-4"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;"data-bs-toggle="modal"
                                data-bs-target="#meuModal">
                                OPÇÕES
                            </button>
                    @endif
                </div>
        </div>
    </div>
    </form>
    <hr>
    Membros Ativos: {{ $contar }}
    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
        <thead>
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th>Nº</th>
                <th>NOME</th>
                <th>FUNÇÃO</th>
                <th>STATUS</th>
                <th>AÇÕES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($membro as $membros)
                <tr>
                    <td>{{ $membros->nr_associado }}</td>
                    <td>{{ $membros->nome_completo }}</td>
                    <td>{{ $membros->nome_funcao }}</td>
                    <td>{{ $membros->status }}</td>
                    <td>
                        <a href="/visualizar-membro/{{ $membros->idm }}" class="btn btn-outline-primary btn-sm tooltips">
                            <span class="tooltiptext">Visualizar</span>
                            <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                        </a>

                        @if (in_array(48, session()->get('usuario.acesso')))
                            <a href="/curriculo-medium/{{ $membros->idm }}"
                                class="btn btn-outline-primary btn-sm tooltips">
                                <span class="tooltiptext">Currículo</span>
                                <i class="bi bi-newspaper" style="font-size: 1rem; color:#000;"></i>
                            </a>
                        @endif
                        @if (in_array(42, session()->get('usuario.acesso')))
                            @if ($membros->status == 'Ativo')
                                <a href="/reverter-faltas-membro/{{ $membros->idm }}"
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
                        @if (in_array(13, session()->get('usuario.acesso')))
                            <!-- Botão para editar -->

                            @if ($membros->status == 'Ativo')
                                <a href="/editar-membro/{{ $id }}/{{ $membros->idm }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                            @else
                                <button class="btn btn-outline-warning btn-sm tooltips" disabled>
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif

                            <!-- Botão para email -->
                             @if ($membros->status == 'Ativo')
                             <a href="/editar-email/{{ $membros->idm }}/{{ $membros->id_cronograma }}" class="btn btn-outline-warning btn-sm tooltips">
                            <span class="tooltiptext">Email</span>
                            <i class="bi bi-envelope" style="font-size: 1rem; color:#000;"></i>
                            </a>
                             @else
                              <a href="/editar-email/{{ $membros->idm }}/{{ $membros->id_cronograma }}" class="btn btn-outline-warning btn-sm tooltips" disabled>
                            <span class="tooltiptext">Email</span>
                            <i class="bi bi-envelope" style="font-size: 1rem; color:#000;"></i>
                            </a>
                            @endif

                            <!-- Botão para inativar -->
                            @if ($membros->status == 'Ativo')
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmInactivate{{ $membros->idm }}" disabled>
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif
                           

                            @if ($membros->status == 'Ativo')
                                <!-- Botão para deletar -->
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmDelete{{ $membros->idm }}">
                                    <span class="tooltiptext">Deletar</span>
                                    <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#confirmDelete{{ $membros->idm }}" disabled>
                                    <span class="tooltiptext">Deletar</span>
                                    <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                </button>
                            @endif
                            
                    </td>
                </tr>
            @endif

            <form action="{{ route('membro.inactivate', ['idcro' => $id, 'id' => $membros->idm]) }}" method="POST"
                id="formulario{{ $membros->idm }}">
                @csrf
                <!-- Modal de confirmação para inativar -->
                <div class="modal fade" id="confirmInactivate{{ $membros->idm }}" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#DC4C64">
                                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Inativar membro</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body " style="text-align: center;">
                                Tem certeza que deseja inativar o membro<br /><span
                                    style="color:#DC4C64; font-weight: bold;">
                                    {{ $membros->nome_completo }}</span>?
                                <center>
                                    <div class="col-10">
                                        <label for="data_inativacao" class="form-label mt-3">Escolha a data de
                                            inativação:</label>
                                        <input type="date" name="data_inativacao" id="data_inativacao"
                                            class="form-control mb-3" required>
                                        <input type="text" name="escolha" id="escolha{{ $membros->idm }}"
                                            value="0" hidden>
                                        {{-- Usado para guardar se é uma inativcação em massa ou não --}}
                                    </div>
                                </center>
                                <div class="modal-footer mt-3"
                                    multiplo="{{ in_array($membros->id_associado, $id_membros) }}">
                                    <button type="button" class="btn btn-danger"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button id="{{ $membros->idm }}" class="btn btn-primary btnModal">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de confirmação para deletar -->


                {{-- Modal de confirmação de inativação em massa --}}
                <div class="modal fade" id="confirmMass{{ $membros->idm }}" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#DC4C64">
                                <h1 class="modal-title fs-5" id="exampleModalLabel" style="color:white">Inativação em
                                    Massa</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="modal-body " style="text-align: center;">
                                    O membro<span style="color:#DC4C64;">
                                        {{ $membros->nome_completo }}</span>
                                    Faz parte de outras reuniões desse grupo
                                    <br />
                                    <br />
                                    <br />
                                    <span style="color:#DC4C64; font-weight: bold;">
                                        Deseja inativar em todas as reuniões?</span>

                                </div>
                                <div class="modal-footer" id_membro="{{ $membros->idm }}">
                                    <button type="button" class="btn btn-danger"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" id="btnTodas" class="btn btn-primary confirm">Inativar
                                        Todas</button>
                                    <button type="button" id="btnUnica"class="btn btn-primary confirm">Inatinar
                                        Nesta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Fim do modal de confirmação de inativação em massa --}}

            </form>
            <div class="modal fade" id="confirmDelete{{ $membros->idm }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#DC4C64">
                            <h5 class="modal-title" id="exampleModalLabel" style="color:white">Deletar membro
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="text-align: center;">
                            Tem certeza que deseja deletar o membro<br /><span style="color:#DC4C64; font-weight: bold;">
                                {{ $membros->nome_completo }}</span>?
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('membro.destroy', ['idcro' => $id, 'id' => $membros->idm]) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
    <!-- Modal de opções do grupo -->
    <div class="modal fade" id="meuModal" tabindex="-1" aria-labelledby="meuModalLabel" aria-hidden="true">
        <data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:rgb(39, 91, 189);color:white">
                    <h5 class="modal-title" id="meuModalLabel">Opções do Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <center>
                        <div class="row col-10">
                            @if (in_array(13, session()->get('usuario.acesso')))
                                <a href="/criar-membro-grupo/{{ $id }}" class="btn btn-success w-100 mb-2">
                                    Novo membro +
                                </a>
                                <a href="/selecionar-membro/{{ $id }}" class="btn btn-warning w-100 mb-2">
                                    Transferir Membros
                                </a>
                            @endif
                            @if (in_array(13, session()->get('usuario.acesso')))
                                @if ($grupo->modificador == 4)
                                    <a href="/ferias-reuniao/{{ $id }}/2" class="btn btn-warning  w-100 mb-2">
                                        Retomar de Férias
                                    </a>
                                @else
                                    <a href="/ferias-reuniao/{{ $id }}/1" class="btn btn-danger w-100 mb-2">
                                        Declarar Férias
                                    </a>
                                @endif
                            @endif
                        </div>
                    </center>
                </div>

            </div>
        </div>
    </div>
    {{ $membro->links('pagination::bootstrap-5') }}
    </div>

    <script>
        $(document).ready(function() {

            // Ao apertar o botão do modal de inativação
            $(".btnModal").click(function(e) {
                e.preventDefault();

                // Caso o forumlário esteja valido (Todos os required preenchidos)
                if (document.getElementById('formulario' + this.id).reportValidity()) {

                    // Caso esse membro seja marcado como presente em mais de um cronograma nesse grupo
                    if (this.parentElement.getAttribute("multiplo") == 1) {
                        $('.modal').modal('hide');
                        $('#confirmMass' + this.id).modal('show');
                    } else {
                        $('#formulario' + this.id).submit();

                    }
                }
            })

            // Ao confirmar no modla de Inativação em massa
            $('.confirm').click(function(e) {
                e.preventDefault();

                // Caso o botão usado seja o de confirmar em massa
                if (this.id == 'btnTodas') {
                    $('#escolha' + this.parentElement.getAttribute("id_membro")).val(
                    '1'); // Coloca a varíavel como true
                    $('#formulario' + this.parentElement.getAttribute("id_membro")).submit();
                } else {
                    $('#formulario' + this.parentElement.getAttribute("id_membro")).submit();
                }
            })

        });
    </script>
@endsection
