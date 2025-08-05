@extends('layouts.app')

@section('title')
    Criar Estudos Externos
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/salvar-estudos-externos" enctype="multipart/form-data">
                    @csrf
                    <!-- Card principal -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    CRIAR ESTUDOS EXTERNOS
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-5">Setor
                                        <select class="form-select select2" name="setor" required>
                                            <option value="">Selecione um setor</option>
                                            @foreach ($setores as $setor)
                                                <option value="{{ $setor->id }}">{{ $setor->sigla }} - {{ $setor->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">Pessoa
                                        <select class="form-select select2" name="pessoa" required>
                                            <option value="">Selecione uma pessoa</option>
                                            @foreach ($pessoas as $pessoa)
                                                <option value="{{ $pessoa->id }}">{{ $pessoa->nome_completo }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card para Pessoa / Instituição / Anexo -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>CURSO</span>
                            <!-- Botão para adicionar nova proposta comercial -->
                            <button type="button" id="add-curso-secundario" class="btn btn-success">Adicionar</button>
                        </div>
                        <div class="card-body" id="cursoContainer">
                            <div class="card curso-principal" style="border-color: #355089; margin-top: 20px;">
                                <div class="form-group row" style="margin: 5px; margin-top: 5px; margin-bottom: 15px;">
                                    <div class="col-md-5">Instituição
                                        <select class="form-select select2" name="instituicao[]" required>
                                            <option value="">Selecione uma instituição</option>
                                            @foreach ($instituicoes as $instituicao)
                                                <option value="{{ $instituicao->id }}">{{ $instituicao->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">Estudo
                                        <select class="form-select select2" name="estudo[]" required>
                                            <option value="">Selecione um estudo</option>
                                            @foreach ($estudos as $estudo)
                                                <option value="{{ $estudo->id }}">{{ $estudo->sigla }} -
                                                    {{ $estudo->id_semestre ?? 'N/P' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">Término
                                        <input type="date" class="form-control" name="dt_final[]" required>
                                    </div>
                                    <div class="col-md-5">Arquivo de Anexo
                                        <input type="file" class="form-control" name="arquivo[]" required
                                            accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                                    </div>
                                </div>
                            </div>
                            <!-- Container para os formulários de curso -->
                            <div id="form-curso-secundario">
                                <!-- Formulários de materiais serão adicionados aqui -->
                            </div>
                        </div>
                    </div>


                    <!-- Botões de ação -->
                    <br>
                    <div class="row mb-3">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-estudos-externos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Template de formulário de curso -->
    <div id="template-curso-secundario" style="display: none;">
        <div class="card curso-secundario" style="border-color: #355089; margin-top: 20px;">
            <button type="button"
                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-curso-secundario">
                <i class="bi bi-x"></i>
            </button>
            <div class="form-group row" style="margin: 5px; margin-top: 5px; margin-bottom: 15px;">
                <div class="col-md-5">Instituição
                    <select class="form-select js-categoria-curso" name="instituicao[]" required>
                        <option value="">Selecione uma instituição</option>
                        @foreach ($instituicoes as $instituicao)
                            <option value="{{ $instituicao->id }}">{{ $instituicao->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">Estudo
                    <select class="form-select js-categoria-curso" name="estudo[]" required>
                        <option value="">Selecione um estudo</option>
                        @foreach ($estudos as $estudo)
                            <option value="{{ $estudo->id }}">{{ $estudo->sigla }} -
                                {{ $estudo->id_semestre ?? 'N/P' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">Término
                    <input type="date" class="form-control" name="dt_final[]" required>
                </div>
                <div class="col-md-5">Arquivo de Anexo
                    <input type="file" class="form-control" name="arquivo[]" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                        required>
                </div>
            </div>
        </div>
    </div>
    <!-- FIM do Template de formulário de curso -->
    <script>
        $(document).ready(function() {
            // Adiciona novo curso
            $("#add-curso-secundario").click(function() {
                const newProposta = $("#template-curso-secundario").html();
                $("#form-curso-secundario").append(newProposta);
                $("#form-curso-secundario .js-categoria-curso").select2({
                    theme: "bootstrap-5"
                });
            });

            // Remove curso
            $(document).on("click", ".remove-curso-secundario", function() {
                $(this).closest(".curso-secundario").remove();
            });
        });
    </script>
@endsection
