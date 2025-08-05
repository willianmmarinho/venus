@extends('layouts.app')

@section('title')
    Gerenciar Estudos Externos
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR ESTUDOS EXTERNOS</h4>
        <br>
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="row">
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                            style="box-shadow: 1px 2px 3px #000000; margin-right: 10px;">
                            Pesquisar <i class="bi bi-funnel"></i>
                        </button>
                        <a href="" class="btn btn-sm btn-warning"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-right: 10px"
                            data-bs-toggle="modal" data-bs-target="#modalAprovarLote">
                            Aprovar em Lote
                        </a>
                        <a href="/incluir-estudos-externos" class="btn btn-success btn-sm"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                            Novo+
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <table {{-- Inicio da tabela de informacoes --}}
            class= "table table-sm table-striped table-bordered border-secondary table-hover align-middle"
            id="tabela-materiais" style="width: 100%">
            <thead style="text-align: center;">{{-- inicio header tabela --}}
                <tr style="background-color: #d6e3ff; font-size:15px; color:#000;" class="align-middle">
                    <th>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)"
                                aria-label="Selecionar todos" style="border: 1px solid #000">
                        </div>
                    </th>
                    <th>ID</th>
                    <th>SETOR</th>
                    <th>PESSOA</th>
                    <th>ESTUDO</th>
                    <th>TÉRMINO</th>
                    <th>INSTITUIÇÃO</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>{{-- Fim do header da tabela --}}
            <tbody style="font-size: 15px; color:#000000; text-align: center;">
                {{-- Inicio body tabela --}}
                @foreach ($lista as $listas)
                    <tr>
                        <td>
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <input class="form-check-input item-checkbox" type="checkbox" id="checkboxNoLabel"
                                    value="" aria-label="..." style="border: 1px solid #000">
                            </div>
                        </td>
                        <td>{{ $listas->id }}</td>
                        <td>{{ $listas->setor }}</td>
                        <td>{{ $listas->id_pessoa }}</td>
                        <td>{{ $listas->id_tipo_atividade }}</td>
                        <td>{{ $listas->data_fim }}</td>
                        <td>{{ $listas->instituicao }}</td>
                        <td>{{ $listas->status }}</td>
                        <td>
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Visualizar">
                                <i class="bi bi-search"></i>
                            </a>
                            {{-- @if (in_array($aquisicaos->tipoStatus->id, ['3', '2'])) --}}
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Aprovar">
                                <i class="bi bi-check-lg"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if ($aquisicaos->tipoStatus->id == '1') --}}
                            <a href="" class="btn btn-sm btn-outline-warning" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Enviar">
                                <i class="bi bi-cart-check"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if (isset($aquisicaos->aut_usu_pres, $aquisicaos->aut_usu_adm, $aquisicaos->aut_usu_daf)) --}}
                            <a href="" class="btn btn-sm btn-outline-info" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Anexar">
                                <i class="bi bi-hand-thumbs-up"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if ($aquisicaos->tipoStatus->id == '1') --}}
                            <a href="#" class="btn btn-sm btn-outline-danger excluirSolicitacao" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Excluir"
                                data-bs-toggle="modal" data-bs-target="#modalExcluirSolicitacao" data-id="">
                                <i class="bi bi-trash"></i>
                            </a>
                            {{-- @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            {{-- Fim body da tabela --}}
        </table>
    </div>
    </div>


    <form action="{{ route('gecdex') }}" class="form-horizontal mt-4" method="GET">
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
                                <div class="col-12 mb-3">Setor
                                    <select class="form-select" id="4" name="status" type="number">

                                    </select>
                                </div>
                                <div class="col-12 mb-3">Estudo
                                    <select class="form-select" id="4" name="status" type="number">

                                    </select>
                                </div>
                                <div class="col-12 mb-3">Pessoa
                                    <select class="form-select" id="4" name="status" type="number">

                                    </select>
                                </div>
                                <div class="col-12 mb-3">CPF
                                    <input class="form-control" type="text" maxlength="11"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        id="2" name="cpf" value="">
                                </div>
                                <div class="col-12 mb-3">Status
                                    <select class="form-select" id="4" name="status" type="number">

                                    </select>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <a class="btn btn-secondary" href="/gerenciar-encaminhamentos">Limpar</a>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Modal Excluir Solicitação -->
    <div class="modal fade" id="modalExcluirSolicitacao" tabindex="-1" aria-labelledby="modalExcluirSolicitacaoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formExcluirSolicitacao" class="form-horizontal" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#DC4C64;">
                        <h5 class="modal-title" id="modalExcluirSolicitacaoLabel">Exclusão de Estudo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body-content-excluir-material">
                        Deseja realmente excluir o estudo: <span id="solicitacaoId" style="color: #DC4C64"></span>?
                    </div>
                    <div class="modal-footer mt-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- FIM da Modal Aprovar em Lote -->
    <!-- Modal Aprovar em Lote -->
    <div class="modal fade" id="modalAprovarLote" tabindex="-1" aria-labelledby="modalAprovarLoteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="form-horizontal" method="POST" action="{{ url('/aprovar-em-lote') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="background-color:lightblue;">
                        <h5 class="modal-title" id="modalAprovarLoteLabel">Confirmar Aprovação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body-content-aprovar">
                        <!-- O conteúdo dinâmico será inserido aqui -->
                    </div>
                    <div class="modal-footer mt-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- FIM da Modal Aprovar em Lote -->
@endsection

@section('footerScript')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".excluirSolicitacao").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let form = document.getElementById("formExcluirSolicitacao");

                    // Atualiza a ação do formulário com o ID correto
                    form.setAttribute("action", "/deletar-aquisicao-material/" + id);

                    // Atualiza o texto dentro da modal
                    document.getElementById("solicitacaoId").textContent = id;
                });
            });
        });
    </script>
    <script>
        // Função para selecionar ou desmarcar todos os checkboxes
        function toggleCheckboxes(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        // Função para gerar conteúdo dinâmico nos modais
        function generateModalContent(selectedCheckboxes, modalContentId) {
            selectedCheckboxes.each(function() {
                const id = $(this).val();
                const newContent = `
                    <div class="row mb-3" data-id="${id}">
                        <div class="d-flex col-md-12">
                            <div class="col-md-4" style="margin-right: 5px">
                                <label for="prioridade-${id}" class="form-label">Prioridade da solicitação ${id}:</label>
                                <select name="prioridade[${id}]" id="prioridade-${id}" class="form-select select2">
                                    @for ($i = 1; $i <= 100; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-8">

                            </div>
                        </div>
                    </div>
                    <br>
                `;
                $(modalContentId).append(newContent);
            });
        }

        $(document).ready(function() {
            // Inicializa o Select2 dentro dos modais
            $('#modalAprovarLote, #modalHomologarLote').on('shown.bs.modal', function() {
                $('.select2').select2({
                    dropdownParent: $(this)
                });
            });

            // Configuração do modal de Aprovar em Lote
            $('#modalAprovarLote').on('show.bs.modal', function() {
                $('#modal-body-content-aprovar').empty();
                const selectedCheckboxes = $('.item-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Por favor, selecione pelo menos uma solicitação.');
                    $('#modalAprovarLote').modal('hide');
                    return;
                }
                generateModalContent(selectedCheckboxes, '#modal-body-content-aprovar');
            });

            // Recarrega a página ao cancelar no modal
            $('.btn-danger[data-bs-dismiss="modal"]').on('click', function() {
                location.reload();
            });
        });
    </script>
@endsection
