@extends('layouts.app')
@section('title')
    Relatório Presença AFI
@endsection
@section('content')
    <div class="container">
        <br />
        <form action="/presenca-afi">
            <div class="row">
                <div class="col-2">
                    Data de Início
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $dt_inicio }}">
                </div>
                <div class="col-2">
                    Data de fim
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ $dt_fim }}">
                </div>
                <div class="col mt-3">
                    <input class="btn btn-light btn-sm me-md-2"
                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                    value="Pesquisar">
                <a href="/presenca-afi"><input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                        value="Limpar"></a>
                </div>
                <div class="col mt-3 offset-3">
                <a href="/gerenciar-relatorio-afi"><input class="btn btn-primary btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                        value="Retornar ao Menu"></a>
                </div>
            </div>
        </form>
        <br />
        <div class="card">
            <div class="card-header">
                Relatório de Presença - {{ isset($afiSelecionado->nome_completo) ? $afiSelecionado->nome_completo : ''}}
            </div>
            <div class="card-body">


           
             <center>
                 <div class='col-3'>
                     <canvas id="myChart"></canvas>
                    </div>
                </center>

            <br />

                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">REUNIAO</th>
                            <th class="col">DATA</th>
                            <th class="col">DIA</th>
                            <th class="col">HORARIO</th>
                            <th class="col">PRESENÇA</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align:center;">
                        @foreach ($dados as $dado)
                                <tr>
                                    <td>{{ $dado['nome'] }}</td>
                                    <td>{{ date( 'd/m/Y' , strtotime($dado['data']))}}</td>
                                    <td>{{ $dado['dia'] }}</td>
                                    <td>{{ $dado['h_inicio'] }}</td>
                                    @if($dado['presenca'] == 1)
                                    <td style="background-color:#90EE90;">Presente</td>
                                    @else
                                    <td style="background-color:#FA8072;">Ausente</td>
                                    @endif
                                </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>





    <script>
        const ctx = document.getElementById('myChart');


        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Faltas',
                    'Presenças',
                ],
                datasets: [{
                    label: 'Numero',
                    data: @JSON($contaFaltas),
                    backgroundColor: [
                        'rgb(217, 83, 79)',
                        'rgb(92, 184, 92)',
                    ],

                }]
            },

        });
    </script>
@endsection
