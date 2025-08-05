@extends('layouts.app')

@section('title')
   Cadastrar Reunião
@endsection

@section('content')
    
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                CADASTRAR REUNIÃO
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="/nova-reuniao">
                            @csrf
                            <div class="row">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="repete" name="repete">
                                <label class="form-check-label" for="repete">Manter campos</label>
                            </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="grupo" class="form-label">Grupo</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select select2" id="grupo" name="grupo" required>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->idg }}" {{old('grupo', request('grupo')) == $grupos->idg ? 'selected' : '' }}>
                                            {{ $grupos->nome}} - {{$grupos->nsigla}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label d-block">Dia da Semana <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span></label>
                                    <div class="d-flex gap-2">
                                        @php
                                            $diasSemana = ['D' => 0, 'S' => 1, 'T' => 2, 'Q' => 3, 'Q' => 4, 'S' => 5, 'S' => 6];
                                            $iniciais = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];
                                            $valores = [0, 1, 2, 3, 4, 5, 6];
                                        @endphp

                                        @foreach ($valores as $index => $val)
                                            @php $letra = $iniciais[$index]; @endphp
                                            <input type="checkbox" class="btn-check" id="dia-{{ $val }}" name="dia[]" value="{{ $val }}"
                                                {{ in_array($val, old('dia', [])) ? 'checked' : '' }} autocomplete="off">
                                            <label class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                style="width: 40px; height: 40px; padding: 0;" 
                                                for="dia-{{ $val }}">{{ $letra }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="h_inicio" class="form-label">Hora de início</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_inicio" name="h_inicio" value="{{old('h_inicio')}}" required>
                                </div>
                                <div class="col-2">
                                    <label for="h_fim" class="form-label">Hora de fim</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input class="form-control" type="time" id="h_fim" name="h_fim" value="{{old('h_fim')}}" required>
                                </div>
                                
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="tratamento" class="form-label">Tipo de Atividade</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select slct" id="tratamento" value="" name="tratamento" required>
                                        <option></option>
                                        @foreach ($tratamento as $tratamentos)
                                        <option value="{{ $tratamentos->idt}}" 
                                        {{old('tratamento', request('tratamento')) == $tratamentos->idt ? 'selected' : '' }}>
                                        {{ $tratamentos->descricao}} - {{ $tratamentos->sigla}} - {{ $tratamentos->siglasem}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="h_fim" class="form-label">Observação</label>
                                    <select class="form-select slct" id="observacao" name="observacao">
                                        <option></option>
                                        @foreach ($observacao as $obs)
                                        <option value="{{ $obs->id }}" {{old('observacao', request('observacao')) == $obs->id ? 'selected' : '' }}>
                                        {{ $obs->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
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

                            </div>
                            <div class="row mt-3">                               
                                <div class="col-2">
                                    <label class="form-label">Data Inicio</label>

                                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" 
           value="{{ old('dt_inicio', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-2">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="dt_fim" min="1" max="800"
                                        name="dt_fim" value="{{old('dt_fim')}}">
                                </div>
                                <div class="col-2">
                                    <label for="max_atend" class="form-label">Max atendimentos</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="max_atend" min="1" max="800"
                                        name="max_atend" value="{{old('max_atend')}}" required>
                                </div>
                                <div class="col-2">
                                    <label for="max_atend" class="form-label">Max trabalhadores</label>
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="max_trab" min="1" max="50"
                                        name="max_trab" value="{{old('max_trab')}}" required>
                                </div>
                                <div class="col">
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
                                        <div class="col">
                                        <input type="checkbox" class="btn-check option-check" id="btn-check-5" name="tipo_semana[]" value="5" {{ in_array("5", old('tipo_semana', [])) ? 'checked' : '' }} autocomplete="off">
                                        <label class="btn btn-outline-primary" for="btn-check-5">5ª</label>      
                                    </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="card mt-3">
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
                                    <option value="{{ $sala->id }}" 
                                        {{ old('id_sala', request('id_sala')) == $sala->id ? 'selected' : '' }}
                                        data-nome="{{ $sala->nome }}"
                                        data-numero="{{ $sala->numero }}"
                                        data-localizacao="{{ $sala->nome_localizacao }}">
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
        $(document).ready(function(){
            function disab(){
                let grupo = $('#grupo').prop('selectedIndex');

                let array = @json($grupo);
                array = array[grupo]

                if (array.id_tipo_grupo != 3) {
                    $('#max_atend').prop("disabled", false)
                    $('#tratamento').prop("disabled", false)
                    $('.sumir').prop('hidden', false)



                } else {
                    bufferM = $('#max_atend').val()
                    bufferT = $('#tratamento').prop("selectedIndex")
                    $('#max_atend').prop("disabled", true)
                    $('#max_atend').val('')
                    $('#tratamento').prop("disabled", true)
                    $('#tratamento').prop("selectedIndex", -1)
                    $('.sumir').prop('hidden', true)
                }
            };

            disab();
            $('#grupo').change(function() {
               disab()
            })
        })

    </script>
    <script>
        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });


 
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

                const checkedCount = [...optionCheckboxes].filter(cb => cb.checked).length;

                if (checkedCount > 4) {
                    alert("Pode selecionar no máximo 4 opções.");
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
