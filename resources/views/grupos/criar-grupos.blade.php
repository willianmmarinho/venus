@extends('layouts.app')
@section('title', 'incluir Grupos')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                CADASTRAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                @csrf

                                <div class="row">
                                    <div class="col-6">
                                        Nome
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <input type="text" class="form-control" id="nome" name="nome" maxlength="50" required="required" oninput="validarCaracteresEspeciais(this)">
                                    </div>

                                    <script>
                                        function validarCaracteresEspeciais(input) {
                                            // Permite letras, espaços e caracteres especiais
                                            input.value = input.value.replace(/[^a-zA-Z\u00C0-\u00FF\s]/g, '');
                                        }
                                    </script>

                                    <div class="col">
                                        Status

                                        <select class="form-select" aria-label=".form-select-lg example" name="status_grupo" id="status_grupo" required="required">
                                            <option value="1">Ativo</option>
                                            <option value="2">Inativo</option>
                                            <option value="3">Experimental</option>
                                        </select>
                                    </div>

                                    <br>

                                    <div class="col">
                                        Motivo
                                        <select class="form-select" aria-label=".form-select-lg example" name="id_motivo_inativacao" id="tipo_mot_inat_gr_reu" disabled>
                                            <option value=""> </option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}">{{ $tipo_motivos->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <br>
                                            Tipo grupo
                                            <span class="tooltips">
                                                <span class="tooltiptext">Obrigatório</span>
                                                <span style="color:red">*</span>
                                            </span>
                                            <select class="form-select select2" aria-label=".form-select-lg example" name="id_tipo_grupo" required="required">
                                                @foreach ($tipo_grupo as $item)
                                                    <option value="{{ $item->idg }}">{{ $item->nm_tipo_grupo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <br>
                                            Setor
                                            <span class="tooltips">
                                                <span class="tooltiptext">Obrigatório</span>
                                                <span style="color:red">*</span>
                                            </span>
                                            <select class="form-select select2" aria-label=".form-select-lg example" name="id_setor" required="required">
                                                @foreach ($setor as $setors)
                                                    <option value="{{ $setors->id }}">{{ $setors->nm_setor }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="d-grid gap-1 col-4 mx-auto">
                                                <br>
                                                <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Cancelar</a>
                                            </div>
                                            <div class="d-grid gap-2 col-4 mx-auto">
                                                <br>
                                                <button class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Inicializa o Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Adiciona um ouvinte de evento para o campo "Status"
            $('#status_grupo').change(function() {
                var selectedStatus = $(this).val();
                if (selectedStatus === '2') {
                    $('#tipo_motivo').prop('disabled', false);
                } else {
                    $('#tipo_motivo').prop('disabled', true);
                    $('#tipo_motivo').val(''); // Limpa a seleção quando desabilitado
                }
            });
        });
    </script>
@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
