@extends('layouts.app')
@section('title')
    Relatório de Temáticas
@endsection
@section('content')
    <br />

   

    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <div class="container">

        <form action="/relatorio-tematicas">
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
                    <a href="/relatorio-tematicas"><input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Limpar"></a>
                </div>

            </div>
        </form>
        <br />

        <div class="card">
            <div class="card-header">
                Relatório de Temáticas
            </div>
            <div class="card-body" id="printTable">
                <canvas id="myChart"></canvas>

                <table class="table  table-striped table-bordered border-secondary table-hover align-middle mt-5">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>TEMÁTICA</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach ($tematicasArray as $key => $tematica)
                            <tr>
                                <td> {{ $key }} </td>
                                <td> {{ $tematica }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


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


        new Chart(ctx, {
            type: 'bar',
            data: {
                label: [
                    'desanimo',
                    'dificuldade'
                ],
                datasets: [

                    {
                        label: 'Total',
                        data: @JSON($tematicasArray),
                        backgroundColor: [
                            'rgba(0, 145, 255, 0.4)',
                            'rgba(0, 145, 255, 0.4)',
                            'rgba(0, 145, 255, 0.4)',
                            'rgba(188, 35, 130, 0.4)',
                            'rgba(188, 35, 130, 0.4)',
                            'rgba(188, 35, 130, 0.4)',
                            'rgba(188, 35, 130, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(255, 134, 46, 0.4)',
                            'rgba(75, 30, 254, 0.4)',
                            'rgba(75, 30, 254, 0.4)',
                            'rgba(75, 30, 254, 0.4)',
                            'rgba(140, 255, 96, 0.4)',
                            'rgba(140, 255, 96, 0.4)',
                            'rgba(140, 255, 96, 0.4)',
                            'rgba(140, 255, 96, 0.4)',
                            'rgba(140, 255, 96, 0.4)',
                        ],
                        borderColor: [
                            'rgba(0, 145, 255, 0.8)',
                            'rgba(0, 145, 255,0.8)',
                            'rgba(0, 145, 255, 0.8)',
                            'rgba(188, 35, 130, 0.8)',
                            'rgba(188, 35, 130, 0.8)',
                            'rgba(188, 35, 130, 0.8)',
                            'rgba(188, 35, 130, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(255, 134, 46, 0.8)',
                            'rgba(75, 30, 254, 0.8)',
                            'rgba(75, 30, 254, 0.8)',
                            'rgba(75, 30, 254, 0.8)',
                            'rgba(140, 255, 96, 0.8)',
                            'rgba(140, 255, 96, 0.8)',
                            'rgba(140, 255, 96, 0.8)',
                            'rgba(140, 255, 96, 0.8)',
                            'rgba(140, 255, 96, 0.8)',
                        ]
                    }
                ]
            },
            options: {
                indexAxis: 'x',
                elements: {
                    bar: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Temáticas (' + @JSON(date('d/m/Y', strtotime($dt_inicio))) + ' - ' +
                            @JSON(date('d/m/Y', strtotime($dt_fim))) +
                            ')'
                    }
                }
            },
        })
    </script>


    <script>
        //Get the button
        let mybutton = document.getElementById("btn-back-to-top");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (
                document.body.scrollTop > 20 ||
                document.documentElement.scrollTop > 20
            ) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        // When the user clicks on the button, scroll to the top of the document
        mybutton.addEventListener("click", backToTop);

        function backToTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
@endsection
