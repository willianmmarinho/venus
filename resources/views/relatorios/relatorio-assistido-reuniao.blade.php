@extends('layouts.app')
@section('title')
    Relatório Reuniao
@endsection
@section('content')
    <div class="container">



        <form action="/gerenciar-relatorio-reuniao" class="form-horizontal mt-4" method="GET">
            <div class="row">
                <div class="col-5">
                    Grupo
                    <select class="form-select select2 grupo" type="text" id="nome_grupo" name="nome_grupo"
                         value="{{ request('nome_grupo') }}">
                         @foreach($reunioesPesquisa as $reuniao)
                         <option value="{{ $reuniao->id }}" {{request('nome_grupo') == $reuniao->id ? 'selected' : ''}}>{{ $reuniao->nome }}-({{ $reuniao->sigla }})-{{ $reuniao->dia }}-{{ date('H:i', strtotime($reuniao->h_inicio)) }}/{{ date('H:i', strtotime($reuniao->h_fim ))}}  | {{ $reuniao->status == 'Inativo' ? 'Inativo' : $reuniao->descricao}}</option>>
                        @endforeach
                        </select>
                </div>
                <div class="col">
                    Data de Inicio
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio"
                        value="{{ $dt_inicio }}">
                </div><div class="col">
                    Data de fim
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim"
                        value="{{ $dt_fim }}">
                </div>
                <div class="col">
                    <br>
                    <input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                        value="Pesquisar">
                    <a href="/gerenciar-relatorio-reuniao"><input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Limpar"></a>


                </div>
            </div>

        </form>


        <br />
        <div class="card">
            <div class="card-header">
                Gerenciar Relatório Assistidos
            </div>
            <div class="card-body">
                <div class="row" style="align-items: center; display: flex; justify-content:center">
                    <div class='col-4'>
                        <canvas id="myChart"></canvas>
                    </div>
                    <div class='col-4'>
                        <canvas id="myChart1"></canvas>
                    </div>
                </div>
                <br />


                <table class="table  table-striped table-bordered border-secondary table-hover align-middle mt-5">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>NOME GRUPO</th>
                            <th>DIA</th>
                            <th>HORA INCÍCIO</th>
                            <th>HORA FINAL</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                       @foreach ($reunioesDirigentes as $reuniao)

                                <tr>
                                    <td> {{$reuniao->nome}} </td>
                                    <td> {{$reuniao->dia}} </td>
                                    <td> {{$reuniao->h_inicio}} </td>
                                    <td> {{$reuniao->h_fim}} </td>
                                    <td>
                                        <a href="/visualizar-relatorio-reuniao/{{ $reuniao->id }}?dt_inicio={{request('dt_inicio')}}&dt_fim={{request('dt_fim')}}" type="button"
                                            class="btn btn-outline-primary btn-sm tooltips">
                                            <span class="tooltiptext">Gerenciar</span>
                                            <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                         </a>
                                    </td>
                                </tr>

                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                let idCronogramaPesquisa = @JSON(request('nome_grupo'));

                if(idCronogramaPesquisa == null){
                    $('#nome_grupo').prop('selectedIndex',-1);
                }
            });
        </script>
        <script>
            const ctx = document.getElementById('myChart');
            const ctx1 = document.getElementById('myChart1');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [
                        'Faltas',
                        'Presenças',
                        'Acompanhantes',
                    ],
                    datasets: [{
                        label: 'Número',
                        data: @JSON($presencasCountAssistidos),
                        backgroundColor: [
                            'rgb(217, 83, 79)',  // Cor para 'Faltas'
                            'rgb(92, 184, 92)',  // Cor para 'Presenças'
                            'rgb(91, 192, 222)', // Cor para 'Acompanhantes'
                        ],
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: ' Assistidos'
                        }
                    }
                },
            });

            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: [
                        'Faltas',
                        'Presenças',
                        '                           '  // Adicione uma etiqueta para o espaço vazio
                    ],
                    datasets: [{
                        label: 'Número',
                        data: @JSON($presencasCountMembros),
                        backgroundColor: [
                            'rgb(217, 83, 79)',  // Cor para 'Faltas'
                            'rgb(92, 184, 92)',  // Cor para 'Presenças'
                            'rgba(0, 0, 0, 0)'  // Cor para 'Espaço Vazio'
                        ],
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trabalhadores'
                        }
                    }
                },
            });
        </script>
    @endsection
