@extends('layouts.app')
@section('title')
    Relatório Salas Cronograma
@endsection
@section('content')
<br />
<div class="container">
    <form action="/relatorio-salas-cronograma">
        <div class="row">
            <div class="col-3">
                Sala
                <select class="form-select select2" id="sala" name="sala">
                   @foreach ($salas as $sala)
                       <option value="{{$sala->id}}" {{$requestSala == $sala->id ? 'selected' : ''}}>{{$sala->numero}} - {{$sala->nome}}</option>
                   @endforeach
                </select>
            </div>
            <div class="col-4">
                Grupo
                <select class="form-select select2" id="grupo" name="grupo">
                    @foreach ($cronogramasPesquisa as $cronogramaPesquisa)
                    <option value="{{$cronogramaPesquisa->id}}" {{$requestGrupo == $cronogramaPesquisa->id ? 'selected' : ''}}>{{$cronogramaPesquisa->nome}}</option>
                @endforeach
                </select>
            </div>
            <div class="col-2">
                Setor
                <select class="form-select select2" id="setor" name="setor">
                    @foreach ($setoresPesquisa as $setorPesquisa)
                    <option value="{{$setorPesquisa->id}}" {{$requestSetor == $setorPesquisa->id ? 'selected' : ''}}>{{$setorPesquisa->nome}} - {{$setorPesquisa->sigla}}</option>
                @endforeach
                </select>
            </div>
            <div class="col mt-3">
                <input class="btn btn-light btn-sm me-md-2"
                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                <a href="/relatorio-salas-cronograma"><input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                        value="Limpar"></a>
            </div>
        </div>
    </form>




    <br />
    
        <div class="card">
            <div class="card-header">
                Relatório de Salas por Dia
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>
    </div>






    <div class="modal fade" id="showModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="inativarLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#0275d8;color:white">
                    <h1 class="modal-title fs-5" id="inativarLabel">Informação de Agendamento</h1>
                    <button data-bs-dismiss="modal" type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <br />
                <div class="modal-body">


                    <center>
                        <label style="font-weight: bolder">Nome do Grupo:</label>
                        <div id="nome"></div>
                        <label style="font-weight: bolder" class="mt-3">Horário da Reunião:</label>
                        <div id="horario"></div>
                        <label style="font-weight: bolder" class="mt-3">Dia de ocorrência:</label>
                        <div id="dia_semana"></div>
                        <label style="font-weight: bolder" class="mt-3">Setor do Grupo:</label>
                        <div id="setor" class="mb-5"></div>

                    </center>

                </div>

            </div>
        </div>
    </div>

    <script>
        if({{$requestSala == null ? 1:0}}){
            $('#sala').prop('selectedIndex', -1);
        }
        if({{$requestGrupo == null ? 1:0}}){
            $('#grupo').prop('selectedIndex', -1);
        }


        
       
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            event = []

            event = @JSON($eventosCronogramas);

            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {

                locale: 'us',
                timeZone: 'BRT',
                themeSystem: 'bootstrap5',
                aspectRatio: 1.8,
                events: event,
                selectable: true,
                "displayEventEnd": true,
                eventClick: function(info) {


                    $('#showModal').modal('show')
                    $('#nome').html(info.event.extendedProps.nome)
                    $('#horario').html(info.event.extendedProps.h_inicio + ' - ' + info.event
                        .extendedProps.h_fim)
                    $('#dia_semana').html(info.event.extendedProps.dia)
                    $('#setor').html(info.event.title + ' - ' + info.event.extendedProps.setor)
                }
            });

            calendar.render();
        });
    </script>
@endsection
