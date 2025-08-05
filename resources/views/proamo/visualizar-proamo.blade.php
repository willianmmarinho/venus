@extends('layouts.app')

@section('title')
    Histórico
@endsection

@section('content')

    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>


    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-10">
                                DADOS DO ASSISTIDO @if (!$encaminhamento)
                                    - <span style="color:red">Este assistido não está em um Tratamento PTD!</span>
                                @endif
                            </div>
                            <div class="d-md-flex justify-content-md-end col">
                                <a href="/gerenciar-proamo?grupo={{ $result->id_reuniao }}" class=" btn-sm"
                                    style="color: red"><i class="bi bi-x-lg"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col">
                                <label for="disabledTextInput" class="form-label">Assistido:</label>
                                <input type="text" id="" value="{{ $result->nm_1 }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="col-2">
                                <label for="disabledTextInput" class="form-label">Sexo:</label>
                                <input type="text" id="" value="{{ $result->tipo }}" style="text-align:center;"
                                    class="form-control" disabled>
                            </div>
                            <div class="col-3">
                                <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                                <input type="date" class="form-control" id="" name="date"
                                    value="{{ $result->dt_nascimento }}" class="form-control" disabled>
                            </div>
                        </div>
                        <br>

                    </div>
                </div>

                <br />

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                DADOS DO TRATAMENTO
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <legend
                            style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">
                            Dados do Atendimento Fraterno</legend>

                        <table class="table table-sm table-bordered table-striped">
                            <thead style="text-align:center; background: #daffe0;">
                                <tr style="text-align:center; font-weight: bold; font-size:12px">
                                    <td class="col">NR</td>
                                    <td class="col-3">REPRESENTANTE</td>
                                    <td class="col-1">PARENTESCO</td>
                                    <td class="col-3">ATENDENTE</td>
                                    <td class="col-1">DT/H INÍCIO</td>
                                    <td class="col-1">DT/H FIM</td>
                                    <td class="col-2">STATUS</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="text-align:center;font-size:13px">
                                    <td>{{ $result->ida }}</td>
                                    <td>{{ $result->nm_2 }}</td>
                                    <td>{{ $result->nome }}</td>
                                    <td>{{ $result->nm_4 }}</td>
                                    <td>{{ $result->dh_inicio }}</td>
                                    <td>{{ $result->dh_fim }}</td>
                                    <td>{{ $result->statat }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <legend
                            style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">
                            Dados do Tratamento</legend>

                        <table class="table table-sm table-bordered table-striped">
                            <thead style="text-align:center; background: #daffe0;">
                                <tr style="text-align:center; font-weight: bold; font-size:12px">
                                    <td class="col">NR</td>
                                    <td class="col">INICIO</td>
                                    <td class="col">FIM</td>
                                    <td class="col">TRATAMENTO</td>
                                    <td class="col">GRUPO</td>
                                    <td class="col">HORÁRIO</td>
                                    <td class="col">SALA</td>
                                    <td class="col">STATUS</td>
                                    <td class="col">MOTIVO</td>
                                </tr>

                            </thead>

                            <tbody>
                                <tr style="text-align:center;font-size:13px">
                                    <td>{{ $result->ide }}</td>
                                    <td>{{ date('d-m-Y', strtotime($result->dt_inicio)) }}</td>
                                    <td>{{ $result->dt_fim ? date('d-m-Y', strtotime($result->dt_fim)) : '-' }}
                                    </td>
                                    <td>{{ $result->desctrat }}</td>
                                    <td>{{ $result->nomeg }}</td>
                                    <td>{{ $result->rm_inicio }}</td>
                                    <td>{{ $result->sala }}</td>
                                    <td>{{ $result->tsenc }}</td>
                                    <td>{{ $result->tpmotivo }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                <br />

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                DADOS PRESENÇA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <legend
                            style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">
                            Dados de Presenças Proamo </legend>
                        Nr de faltas: {{ $faul }}
                        <table class="table table-sm table-bordered table-striped">
                            <thead style="text-align:center; background: #daffe0;">
                                <tr style="text-align:center; font-weight: bold; font-size:12px">
                                    <td class="col">NR</td>
                                    <td class="col">DATA</td>
                                    <td class="col">GRUPO</td>
                                    <td class="col">PRESENÇA</td>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($list as $lists)
                                    <tr style="text-align:center;font-size:13px">
                                        <td>{{ $lists->idp }}</td>
                                        <td>{{ date('d/m/Y', strtotime($lists->data)) }}</td>
                                        <td>{{ $lists->nome }}</td>
                                        @if ($lists->presenca == 1)
                                            <td style="background-color:#90EE90;">Sim</td>
                                        @elseif ($lists->presenca == 0)
                                            <td style="background-color:#FA8072;">Não</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <legend
                            style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">
                            Dados de Presenças PTD</legend>
                        Nr de faltas: {{ $faul2 }}
                        <table class="table table-sm table-bordered table-striped">
                            <thead style="text-align:center; background: #daffe0;">
                                <tr style="text-align:center; font-weight: bold; font-size:12px">
                                    <td class="col">NR</td>
                                    <td class="col">DATA</td>
                                    <td class="col">GRUPO</td>
                                    <td class="col">PRESENÇA</td>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($list2 as $lists1)
                                    <tr style="text-align:center;font-size:13px">
                                        <td>{{ $lists1->idp }}</td>
                                        <td>{{ date('d/m/Y', strtotime($lists1->data)) }}</td>
                                        <td>{{ $lists1->nome }}</td>
                                        @if ($lists1->presenca == 1)
                                            <td style="background-color:#90EE90;">Sim</td>
                                        @elseif ($lists1->presenca == 0)
                                            <td style="background-color:#FA8072;">Não</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

                <br>

                {{-- Inicio accordion do presença de emergência --}}
                @if (count($emergencia) > 0)
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    TRATAMENTO DE EMERGÊNCIA
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col">NR</td>
                                                <td class="col">DATA</td>
                                                <td class="col">GRUPO</td>
                                                <td class="col">PRESENÇA</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($emergencia as $lists1)
                                                <tr style="text-align:center; font-size:13px">
                                                    <td>{{ $lists1->idp }}</td>
                                                    <td>{{ $lists1->data }}</td>
                                                    <td>{{ $lists1->nome }}</td>
                                                    @if ($lists1->presenca == 1)
                                                        <td style="background-color:#90EE90;">Sim</td>
                                                    @elseif ($lists1->presenca == 0)
                                                        <td style="background-color:#FA8072;">Não</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Fim accordion do presença de emergência --}}

                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-proamo?grupo={{ $result->id_reuniao }}"
                            role="button">Fechar</a>
                    </div>
                </div>

                <br>
                <br>

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

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
