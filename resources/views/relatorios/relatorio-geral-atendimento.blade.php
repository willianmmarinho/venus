@extends('layouts.app')
@section('title')
    Relatório de Atendimentos
@endsection
@section('content')
    <br />



    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>



    <div class="container">

        <!-- Filtro por datas e botões -->
        <form method="GET" action="{{ url('/relatorio-geral-atendimento') }}">
            <div class="row align-items-center mb-4">
                <!-- Data de Início -->
                <div class="col-md-3">
                    <label for="dt_inicio" class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{date('Y-m-d',strtotime($dt_inicio))}}">
                </div>
                <!-- Data de Fim -->
                <div class="col-md-3">
                    <label for="dt_fim" class="form-label">Data de Fim</label>
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ date('Y-m-d' , strtotime($dt_fim)) }}">
                </div>
                <div class="col-md-2">
                    <label for="tipo_tratamento" class="form-label">Tipo Tratamento</label>
                    <select class="form-select" id="tipo_tratamento" name="tipo_tratamento">
                        <option value="1" @if (request('tipo_tratamento') == 1) selected @endif>PTD</option>
                        <option value="2" @if (request('tipo_tratamento') == 2) selected @endif>PTI</option>
                        <option value="4" @if (request('tipo_tratamento') == 4) selected @endif>PROAMO</option>
                        <option value="6" @if (request('tipo_tratamento') == 6) selected @endif>Integral</option>
                        <option value="5" @if (request('tipo_tratamento') == 5) selected @endif>Todos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status_atendimento" class="form-label">Tipo Visualização</label>
                    <select class="form-select" id="tipo_visualizacao" name="tipo_visualizacao">
                        <option value="1" @if (request('tipo_visualizacao') == 1) selected @endif>Total</option>
                        <option value="2" @if (request('tipo_visualizacao') == 2) selected @endif>Mensal</option>
                        <option value="3" @if (request('tipo_visualizacao') == 3) selected @endif>Anual</option>
                    </select>
                </div>
                <!-- Botões -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 me-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Pesquisar
                    </button>
                    <a href="{{ url('/relatorio-geral-atendimento') }}" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px  #000000; margin-top: 27px;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>
        <br />
        <div class="card">
            <div class="card-header">
                Relatório de Atendimentos
            </div>
            <div class="card-body" id="printTable">
                <canvas id="myChart"></canvas>
                <?php $i = 0; ?>
                <table class="table  table-striped table-bordered border-secondary table-hover align-middle mt-5">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            @if (request('tipo_visualizacao') == 2)
                                <th>MÊS</th>
                            @endif
                            <th>TIPO</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach ($dadosChart as $key => $dado)
                            @if (request('tipo_visualizacao') == 2 or request('tipo_visualizacao') == 3)
                                <?php $i = 1; ?>
                                @foreach ($dado as $innerKey => $info)
                                    <tr>
                                        @if ($i == 1)
                                            <td rowspan="{{ count($dado) }}">
                                                {{ $key }}
                                            </td>
                                        @endif
                                        <td> {{ $innerKey }} </td>
                                        <td> {{ $info }} </td>
                                    </tr>
                                    <?php $i = 0; ?>
                                @endforeach
                            @else
                                <tr>
                                    <td> {{ $key }} </td>
                                    <td> {{ $dado }} </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br />
    <br />
    <br />
    <br />


    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>

    <script>
        const ctx = document.getElementById('myChart');

        let dados = []
        let lab = []
        let atendimentos = @JSON($dadosChart);
        let i = 0


        if ($('#tipo_visualizacao').val() == 2 || $('#tipo_visualizacao').val() == 3) {
            for (const [key, value] of Object.entries(atendimentos)) {
                lab.push(key)
                for (const [innerKey, innerValue] of Object.entries(value)) {
                    if (!dados[i]) {
                        dados[i] = {
                            label: `${innerKey}`,
                            data: [`${innerValue}`],
                            borderWidth: 2,
                        }
                    } else {
                        dados[i] = {
                            label: `${innerKey}`,
                            data: dados[i].data.concat([`${innerValue}`]),
                            borderWidth: 2,
                        }
                    }
                    i++
                }
                i = 0;
            }
        } else {
            lab = ['Total de Passes (' + @JSON(date('d/m/Y', strtotime($dt_inicio))) + ' - ' +
                @JSON(date('d/m/Y', strtotime($dt_fim))) +
                ')'
            ];
            for (const [key, value] of Object.entries(atendimentos)) {
                dados[i] = {
                    label: `${key}`,
                    data: [`${value}`],
                    borderWidth: 2,
                }
                i++
            }
        }
console.log(dados)

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: lab,
                datasets: dados
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,

            }
        });
    </script>
@endsection
