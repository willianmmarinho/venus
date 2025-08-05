@extends('layouts.app')
@section('title')
    Relatório Reunião
@endsection
@section('content')
    <div class="container">



        <form action="/visualizar-relatorio-reuniao/{{ $id }}" class="form-horizontal mt-4" method="GET">
            <div class="row">

                <div class="col">
                    Data de Inicio
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ $dt_inicio }}">
                </div>
                <div class="col">
                    Data de Limite
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ $dt_fim }}">
                </div>
                <div class="col">
                    <br>
                    <input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                        value="Pesquisar">
                    <a href="/visualizar-relatorio-reuniao/{{ $id }}"><input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Limpar"></a>


                </div>
            </div>

        </form>


        <br />
        <div class="card">
            <div class="card-header">
                Gerenciar Relatório Assistidos - {{ $grupo->nome }} - {{ $grupo->dia }}
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
                <hr />

                {{-- Accrodion de Assistidos --}}
                <div div class="accordion" id="accordionAssistido">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAssistido" aria-expanded="false" aria-controls="collapseOne">
                                Assistidos
                            </button>
                        </h2>
                        <div id="collapseAssistido" class="accordion-collapse collapse "
                            data-bs-parent="#accordionAssistido">
                            <div class="accordion-body">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" id="nome_assistido"
                                        name="nome_assistido"">
                                    <label for="floatingTextarea">Pesquisar Assistido</label>
                                </div>
                                <div div class="accordion" id="accordionAssistidoPessoa">

                                    @foreach ($presencasAssistidosArray as $key => $presencaAssistido)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ current($presencaAssistido)->id }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse{{ current($presencaAssistido)->id }}">
                                                    {{ $key }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ current($presencaAssistido)->id }}"
                                                class="accordion-collapse collapse"
                                                data-bs-parent="#accordionAssistidoPessoa">
                                                <div class="accordion-body">
                                                    <table
                                                        class="table table-striped table-bordered border-secondary table-hover align-middle">
                                                        <thead style="text-align: center;">
                                                            <tr
                                                                style="background-color: #d6e3ff; font-size:14px; color:#000000">

                                                                <th>DATA</th>
                                                                <th>PRESENÇA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 14px; color:#000000; text-align: center;">

                                                            @foreach ($presencaAssistido as $presenca)
                                                                <tr>


                                                                    <td> {{ $presenca->data }} </td>
                                                                    @if ($presenca->presenca == 1)
                                                                        <td style="background-color:#90EE90;">Sim</td>
                                                                    @elseif ($presenca->presenca == 0)
                                                                        <td style="background-color:#FA8072;">Não</td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Fim Accordion Assistidos --}}


                {{-- Accrodion de Membros --}}
                <div div class="accordion" id="accordionMembros">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseMembros" aria-expanded="false" aria-controls="collapseOne">
                                Membros
                            </button>
                        </h2>
                        <div id="collapseMembros" class="accordion-collapse collapse "
                            data-bs-parent="#accordionMembros">
                            <div class="accordion-body">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" id="nome_membro"
                                        name="nome_membro">
                                    <label for="floatingTextarea">Pesquisar Membros</label>
                                </div>
                                <div div class="accordion" id="accordionMembrosPessoa">

                                    @foreach ($presencasMembrosArray as $key => $presencaMembros)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ current($presencaMembros)->id }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse{{ current($presencaMembros)->id }}">
                                                    {{ $key }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ current($presencaMembros)->id }}"
                                                class="accordion-collapse collapse"
                                                data-bs-parent="#accordionMembrosPessoa">
                                                <div class="accordion-body">
                                                    <table
                                                        class="table table-striped table-bordered border-secondary table-hover align-middle">
                                                        <thead style="text-align: center;">
                                                            <tr
                                                                style="background-color: #d6e3ff; font-size:14px; color:#000000">

                                                                <th>DATA</th>
                                                                <th>PRESENÇA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 14px; color:#000000; text-align: center;">

                                                            @foreach ($presencaMembros as $presenca)
                                                                <tr>

                                                                    <td> {{ $presenca->data }} </td>
                                                                    @if ($presenca->presenca == 1)
                                                                        <td style="background-color:#90EE90;">Sim</td>
                                                                    @elseif ($presenca->presenca == 0)
                                                                        <td style="background-color:#FA8072;">Não</td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Fim Accordion Membros --}}





                </div>
                <br />
            </div>
        </div>
        <script>
             $(document).ready(function() {
            $("#nome_assistido").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                console.log(value)
                $("#accordionAssistidoPessoa .accordion-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $("#nome_membro").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                console.log(value)
                $("#accordionMembrosPessoa .accordion-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

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
                            'rgb(217, 83, 79)', // Cor para 'Faltas'
                            'rgb(92, 184, 92)', // Cor para 'Presenças'
                            'rgb(91, 192, 222)', // Cor para 'Acompanhantes'
                        ],
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Presenças de Assistidos'
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
                        '                           ' // Adicione uma etiqueta para o espaço vazio
                    ],
                    datasets: [{
                        label: 'Número',
                        data: @JSON($presencasCountMembros),
                        backgroundColor: [
                            'rgb(217, 83, 79)', // Cor para 'Faltas'
                            'rgb(92, 184, 92)', // Cor para 'Presenças'
                            'rgba(0, 0, 0, 0)' // Cor para 'Espaço Vazio'
                        ],
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Presenças de Trabalhadores'
                        }
                    }
                },
            });
        </script>
    @endsection
