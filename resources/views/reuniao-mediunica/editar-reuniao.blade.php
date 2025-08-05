@extends('layouts.app')

@section('title')
    Editar Reunião
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                Editar Reunião
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/atualizar-reuniao/{{ $info->id }}">
                            @csrf
                            <div class="row mt-3">
                                
                                <div class="col-auto">
                                    <label for="crono" class="form-label">ID</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Desabilitado</span>
                                        <span style="color:red"></span>                                    
                                    </span>
                                    <input class="form-control" type="text" value="{{$cronograma}}" disabled>
                                </div> 
                                <div class="col">
                                    <label for="grupo" class="form-label">Grupo</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select select2" id="grupo" name="grupo" required>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->idg }}"
                                                {{ $grupos->idg == $info->id_grupo ? 'selected' : '' }}>{{ $grupos->nome }}
                                                - {{ $grupos->nsigla }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tratamento" class="form-label">Tipo de Trabalho</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select slct" id="tratamento" name="tratamento" required>
                                        @foreach ($tratamento as $tratamentos)
                                            <option value="{{ $tratamentos->idt }}"
                                                {{ $tratamentos->descricao == $info->descricao ? 'selected' : '' }}>
                                                {{ $tratamentos->descricao }}-{{ $tratamentos->sigla }}-{{ $tratamentos->siglasem }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-2">
                                    <label for="h_fim" class="form-label">Modalidade</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select slct" id="modalidade" name="modalidade">
                                        @foreach ($modalidade as $modal)
                                        <option value="{{ $modal->id }}" {{ request('modalidade') == $modal->id ? 'selected' : '' }}>
                                        {{ $modal->nome}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="dia" class="form-label">Dia da semana</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select slct" id="dia" name="dia" required>
                                        @foreach ($dia as $dias)
                                            <option value="{{ $dias->idd }}"
                                                {{ $dias->nome == $info->dia ? 'selected' : '' }}>{{ $dias->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="h_inicio" class="form-label">Hora de início</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_inicio" name="h_inicio" required
                                        value="{{ $info->h_inicio }}">
                                </div>
                                <div class="col-2">
                                    <label for="h_fim" class="form-label">Hora de fim</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_fim" name="h_fim" required
                                        value="{{ $info->h_fim }}">
                                </div>
                                <div class="col-4">
                                    <label for="h_fim" class="form-label">Observação</label>
                                    <select class="form-select slct" id="observacao" name="observacao">
                                        <option></option>
                                        @foreach ($observacao as $obs)
                                            <option value="{{ $obs->id }}"
                                                {{ $obs->id == $info->obs ? 'selected' : '' }}>{{ $obs->descricao }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="max_atend" class="form-label">Max atendimentos</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="max_atend" min="1" max="800" name="max_atend" value="{{ $info->max_atend }}" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Inicio</label>
                                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $info->data_inicio }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="dt_fim" min="1" max="800" name="dt_fim" value="{{ $info->data_fim }}">
                                </div>
                                <div class="col-auto">
                                    <label for="tp_semana" class="form-label">Semana de trabalho</label>
                                    <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                    </span>
                                    <div class="row">
                                        <div class="col">
                                                <input type="checkbox" class="btn-check" id="btn-check-td" name="tipo_semana[]" value="0" {{ in_array("0", old('tipo_semana', [])) ? 'checked' : '' }} checked autocomplete="off">
                                                <label class="btn btn-outline-primary" for="btn-check-td">Td</label>
                                            </div>
                                            <div class="col">
                                                <input type="checkbox" class="btn-check option-check" id="btn-check-1" name="tipo_semana[]" value="1" {{ in_array("1", old('tipo_semana', [])) ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-primary" for="btn-check-1">1ª</label>
                                            </div>
                                            <div class="col">
                                                <input type="checkbox" class="btn-check option-check" id="btn-check-2" name="tipo_semana[]" value="2" {{ in_array("2", old('tipo_semana', [])) ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-primary" for="btn-check-2">2ª</label>      
                                            </div>
                                            <div class="col">
                                                <input type="checkbox" class="btn-check option-check" id="btn-check-3" name="tipo_semana[]" value="3" {{ in_array("3", old('tipo_semana', [])) ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-primary" for="btn-check-3">3ª</label>
                                            </div>
                                            <div class="col">
                                                <input type="checkbox" class="btn-check option-check" id="btn-check-4" name="tipo_semana[]" value="4" {{ in_array("4", old('tipo_semana', [])) ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-primary" for="btn-check-4">4ª</label>      
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                <br />
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sala</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="id_sala" class="form-label">Número</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" id="id_sala" name="id_sala">
                                    <option value=""></option>
                                    @foreach ($salas as $sala)
                                        <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                            data-numero="{{ $sala->numero }}"
                                            data-localizacao="{{ $sala->nome_localizacao }}"
                                            {{ $sala->id == $info->id_sala ? 'selected' : '' }}>
                                            {{ $sala->numero }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" readonly>


                            </div>
                            <div class="row mt-5">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger" href="/gerenciar-reunioes" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-2 col-4 mx-auto">
                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#max_atend').prop("disabled", false)
            $('#tratamento').prop("disabled", false)

            function salas() {
                let grupo = $('#grupo').prop('selectedIndex');

                let array = @json($grupo);
                array = array[grupo]

                if (array.id_tipo_grupo !== 3) {
                    $('#max_atend').prop("disabled", false)
                    $('#tratamento').prop("disabled", false)
                    $('.sumir').prop('hidden', false)



                } else {
                    bufferM = $('#max_atend').val()
                    bufferT = $('#tratamento').prop("selectedIndex")
                    console.log(bufferM, bufferT)
                    $('#max_atend').prop("disabled", true)
                    $('#max_atend').val('')
                    $('#tratamento').prop("disabled", true)
                    $('#tratamento').prop("selectedIndex", -1)
                    $('.sumir').prop('hidden', true)
                }
            }

            salas()
            $('#grupo').change(function() {
                salas();
            })
        })
    </script>

    <script>
        $(document).ready(function() {

            var selectedOption = document.getElementById('id_sala')
            selectedOption = selectedOption.options[selectedOption.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');


            document.getElementById('id_sala').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
                document.getElementById('localizacao').value = selectedOption.getAttribute(
                    'data-localizacao');
            });


            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        })
    </script>

<script>
        document.addEventListener("DOMContentLoaded", function() {
            const tdCheckbox = document.getElementById("btn-check-td");
            const optionCheckboxes = document.querySelectorAll(".option-check");

            tdCheckbox.addEventListener("change", function() {
                if (tdCheckbox.checked) {
                    optionCheckboxes.forEach(cb => cb.checked = false);
                }
            });

            optionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    if (this.checked) {
                        tdCheckbox.checked = false;

                        // Conta quantos checkboxes estão marcados
                        const checkedCount = [...optionCheckboxes].filter(cb => cb.checked).length;
                        
                        // Se mais de 3 checkboxes forem marcados, desmarca o atual
                        if (checkedCount > 3) {
                            this.checked = false;
                        }
                    }
                });
            });
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalidadeSelect = document.getElementById("modalidade");
        const salaSelect = document.getElementById("id_sala");

        function toggleSala() {
            // Verifica se a opção selecionada é "1"
            if (modalidadeSelect.value === "1") {
                salaSelect.removeAttribute("disabled");
            } else {
                salaSelect.setAttribute("disabled", "disabled");
            }
        }

        // Executa a função ao carregar a página
        toggleSala();

        // Adiciona um evento para alterar o estado do select de id_sala
        modalidadeSelect.addEventListener("change", toggleSala);
    });
</script>

@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
