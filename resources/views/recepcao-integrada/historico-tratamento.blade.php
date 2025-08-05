@extends('layouts.app')

@section('title')
    Histórico Tratamento
@endsection

@section('content')
    <br>
    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                {{-- Inicío Card de Dados de Atendimento --}}
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                HISTÓRICO DO ASSISTIDO
                            </div>
                            <div class="d-md-flex justify-content-md-end col">
                                <a href="/gerenciar-tratamentos" class=" btn-sm" style="color: red"><i
                                        class="bi bi-x-lg"></i></a>
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
                                    <td>{{ date('d/m/Y G:i', strtotime($result->dh_inicio)) }}</td>
                                    <td>{{ date('d/m/Y G:i', strtotime($result->dh_fim)) }}</td>
                                    <td>{{ $result->tst }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Fim Card Dados de Atendimento --}}

                <br>
                {{-- Inicio Card do Atendimento Atual --}}
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                DADOS DO {{ $result->idt ? 'TRATAMENTO' : 'ENCAMINHAMENTO' }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <table class="table table-sm table-bordered table-striped">
                            <thead style="text-align:center; background: #daffe0;">
                                <tr style="text-align:center; font-weight: bold; font-size:12px">
                                    <td class="col">NR</td>
                                    <td class="col">INICIO</td>
                                    <td class="col">FINAL</td>
                                    <td class="col">TRATAMENTO</td>
                                    <td class="col">GRUPO</td>
                                    <td class="col">DIA</td>
                                    <td class="col">SALA</td>
                                    <td class="col">HORÁRIO</td>
                                    <td class="col">STATUS</td>
                                    <td class="col">MOTIVO</td>
                                </tr>

                            </thead>
                            <tbody>
                                <tr style="text-align:center;font-size:13px">
                                    <td>{{ $result->idt ?? $result->ide }}</td>
                                    <td>{{ $result->dt_inicio != null ? date('d/m/Y', strtotime($result->dt_inicio)) : '-' }}
                                    </td>
                                    <td>{{ $result->final != null ? date('d/m/Y', strtotime($result->final)) : '-' }}</td>
                                    <td>{{ $result->desctrat }}</td>
                                    <td>{{ $result->nomeg }}</td>
                                    <td>{{ $result->nomedia }}</td>
                                    <td>{{ $result->sala }}</td>
                                    <td>{{ $result->rm_inicio }}</td>
                                    <td>{{ $result->tsenc }}</td>
                                    <td>{{ $result->tpmotivo }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <legend
                            style="color:#62829d; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">
                            Dados de presenças</legend>
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
                                        <td>{{ date('d-m-Y', strtotime($lists->data)) }}</td>
                                        <td>{{ $lists->nome }}</td>

                                        @if ($lists->presenca == true)
                                            <td style="background-color:#90EE90;">Sim</td>
                                        @else
                                            <td style="background-color:#FA8072;">Não</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br />

                    </div>
                </div>
                 {{-- Fim card Tratamento/Encaminhamento --}}
                 
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
                                                    <td>{{date('d-m-Y',strtotime($lists1->data)) }}</td>
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

                <br>

                {{-- Inicio Card Outros Encaminhamentos --}}
                @if (count($encaminhamentosAlternativos) > 0)
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    OUTROS TRATAMENTOS
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($encaminhamentosAlternativos as $encaminhamento)
                                <table class="table table-sm table-bordered table-striped">
                                    <thead style="text-align:center; background: #daffe0;">
                                        <tr style="text-align:center; font-weight: bold; font-size:12px">
                                            <td class="col">NR</td>
                                            <td class="col">INICIO</td>
                                            <td class="col">FINAL</td>
                                            <td class="col">TRATAMENTO</td>
                                            <td class="col">GRUPO</td>
                                            <td class="col">DIA</td>
                                            <td class="col">HORÁRIO</td>
                                            <td class="col">STATUS</td>
                                            <td class="col">LINK</td>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr style="text-align:center;font-size:13px">
                                            <td>{{ $encaminhamento->idt ?? $encaminhamento->ide }}</td>
                                            <td>{{ $encaminhamento->dt_inicio != null ? date('d/m/Y', strtotime($encaminhamento->dt_inicio)) : '-' }}
                                            </td>
                                            <td>{{ $encaminhamento->dt_fim != null ? date('d/m/Y', strtotime($encaminhamento->dt_fim)) : '-' }}
                                            </td>
                                            <td>{{ $encaminhamento->descricao }}</td>
                                            <td>{{ $encaminhamento->nome }}</td>
                                            <td>{{ $encaminhamento->dia }}</td>
                                            <td>{{ $encaminhamento->h_inicio }}</td>
                                            <td>{{ $encaminhamento->status }}</td>
                                            <td>
                                                <a class="btn btn-sm tooltips"
                                                    href="/visualizar-tratamento/{{ $encaminhamento->idt }}"
                                                    style="text-align:right;" role="button">
                                                    <span class="tooltiptext">Visualizar</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- Fim Card Outros Encaminhamentos --}}

                <div class="row mt-4 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-tratamentos" role="button">Fechar</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <br>
    <br>
    <br>


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
@endsection
