@extends('layouts.app')

@section('title')
    Visualizar Reunião
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
                                Visualizar Reunião
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/atualizar-reuniao/{{ $info->id }}">
                            @csrf
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="grupo" class="form-label">Grupo</label>
                                    <select class="form-control slct" id="grupo" name="grupo" required disabled>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->idg }}" {{$grupos->idg == $info->id_grupo ? 'selected' : ''}}>{{ $grupos->nome }}-{{ $grupos->nsigla }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="tratamento" class="form-label">Tipo de Trabalho</label>
                                    <select class="form-control slct" id="tratamento" name="tratamento" required disabled>
                                        @foreach ($tratamento as $tratamentos)
                                            <option value="{{ $tratamentos->idt }}" {{$tratamentos->descricao == $info->descricao ? 'selected' : ''}}>{{ $tratamentos->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="row mt-3">
                                <div class="col">
                                    <label for="dia" class="form-label">Dia da semana</label>
                                    <select class="form-control slct" id="dia" name="dia" required disabled>
                                        @foreach ($dia as $dias)
                                            <option value="{{ $dias->idd }}" {{$dias->nome == $info->dia ? 'selected' : ''}} >{{ $dias->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="h_inicio" class="form-label">Hora de início</label>
                                    <input class="form-control" type="time" id="h_inicio" name="h_inicio" required value="{{ $info->h_inicio }}" disabled>
                                </div>
                                <div class="col">
                                    <label for="h_fim" class="form-label">Hora de fim</label>
                                    <input class="form-control" type="time" id="h_fim" name="h_fim" required value="{{ $info->h_fim }}" disabled>
                                </div>

                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="max_atend" class="form-label">Max atendimentos</label>
                                    <input  type="number" class="form-control" id="max_atend" min="1" max="800"
                                        name="max_atend" value="{{ $info->max_atend }}" disabled>
                                </div>
                                <div class="col-2">
                                    <label class="form-label">Data Inicio</label>
                                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $info->data_inicio }}" disabled>
                                </div>
                                <div class="col-2">
                                    <label class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="dt_fim" min="1" max="800"
                                        name="dt_fim" value="{{ $info->data_fim }}" disabled>
                                </div>
                                <div class="col-4">
                                    <label for="h_fim" class="form-label">Observação</label>
                                    <input class="form-control slct" id="observacao" name="observacao" value="{{$info->obs}}" disabled>
                                </div>
                            </div>
                            <br />
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
                                <select class="form-control" id="id_sala" name="id_sala" disabled>
                                    <option value=""></option>
                                    @foreach ($salas as $sala)
                                        <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                            data-numero="{{ $sala->numero }}"
                                            data-localizacao="{{ $sala->nome_localizacao }}" {{ $sala->id == $info->id_sala ? 'selected' : '' }} >
                                            {{ $sala->numero }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" readonly disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" readonly disabled>


                            </div>
                            <div class="row mt-5">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger" href="/gerenciar-reunioes" role="button">Fechar</a>
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
        $( document ).ready(function() {

        var selectedOption = document.getElementById('id_sala')
        selectedOption = selectedOption.options[selectedOption.selectedIndex];
        document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
        document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');


        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });


     
        })
    </script>
@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
