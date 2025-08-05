@extends('layouts.app')
@section('title', 'Agendar Dia')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                AGENDAR HORÁRIO - TRATAMENTO
                            </div>
                        </div>
                    </div>
                
                <div class="card-body">
                    <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do assistido</legend>
                    <fieldset class="border rounded border-secondary p-4">
                        <div class="row">
                            <div class="col-2">Encaminhamento
                                <input class="form-control"
                                    style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                    value="{{ $result->ide }}" name="" id="" type="text" disabled>
                            </div>

                            <div class="col">Assistido
                                <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;"
                                    type="text" name="" id="" value="{{ $result->nm_1 }}" disabled>
                            </div>

                            <div class="col">Representante
                                <input class="form-control"
                                    style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                    value="{{ $result->nm_2 }}" name="" id="" type="text" disabled>
                            </div>
                            <div class="col">Tratamento
                                <input class="form-control"
                                    style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                    value="{{ $result->desctrat }}" name="" id="" type="text" disabled>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="col">Dia da semana:
                            <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);"
                                value="{{ current(current($trata))->nomed }}" name="" id="" type="text"
                                disabled>
                        </div>
                        <form class="form-horizontal mt-4" method="POST"
                            action="/trocar-grupo-tratamento/{{ $result->ide }}">
                            @csrf
                            <legend style="color:#525252; font-size:12px; font-family:sans-serif">Reuniões do dia:</legend>

                            @foreach ($trata->groupBy('h_inicio') as $horario => $tratasDoHorario)
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="{{ current(current($tratasDoHorario))->idr }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#flush-collapse{{ current(current($tratasDoHorario))->idr }}"
                                                aria-expanded="false"
                                                aria-controls="flush-collapse{{ current(current($tratasDoHorario))->idr }}">
                                                <label> {{ date('H:i:s', strtotime($horario)) }} -
                                                    @if (array_sum(array_column($tratasDoHorario->toArray(), 'max_atend')) > 0)
                                                        <label style="color:green">Vagas:
                                                            {{ array_sum(array_column($tratasDoHorario->toArray(), 'trat')) }}</label>
                                                    @else
                                                        <label style="color:red">Vagas:
                                                            {{ array_sum(array_column($tratasDoHorario->toArray(), 'trat')) }}</label>
                                                    @endif
                                                </label>

                                            </button>
                                        </h2>

                                        <div id="flush-collapse{{ current(current($tratasDoHorario))->idr }}"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="{{ current(current($tratasDoHorario))->idr }}"
                                            data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">

                                                @foreach ($tratasDoHorario as $tratas)
                                                    <table class="table table-sm table-bordered table-striped">
                                                        <thead style="text-align:center; background: #daffe0;">
                                                            <tr
                                                                style="text-align:center; font-weight: bold; font-size:13px">
                                                                <th class="col">NR REU</th>
                                                                <th class="col-3">DIRIGENTE</th>
                                                                <th class="col-2">GRUPO</th>
                                                                <th class="col-">OBS</th>
                                                                <th class="col">SALA</th>
                                                                <th class="col">TRATAMENTO</th>
                                                                <th class="col">HORÁRIO INÍCIO</th>
                                                                <th class="col">HORÁRIO FIM</th>
                                                                <th class="col">MAX ATENDIDOS</th>
                                                                <th class="col">NR VAGAS</th>
                                                                <th class="col">MARCAR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr style="text-align:center;font-size:14px">
                                                                <td>{{ $tratas->idr }}</td>
                                                                <td>{{ $tratas->nome_completo }}</td>
                                                                <td>{{ $tratas->nomeg }}</td>
                                                                <td>{{$tratas->des}}</td>
                                                                <td>{{ $tratas->numero }}</td>
                                                                <td>{{ $tratas->tstd }}</td>
                                                                <td>{{ date('H:i:s', strtotime($tratas->h_inicio)) }}</td>
                                                                <td>{{ date('H:i:s', strtotime($tratas->h_fim)) }}</td>
                                                                <td>{{ $tratas->max_atend }}</td>
                                                                <td>{{ $tratas->trat }}</td>
                                                                <td>
                                                                    <center><input type="radio" class="form-check"
                                                                            name="reuniao" id=""
                                                                            value="{{ $tratas->idr }}" autocomplete="off"
                                                                            required></center>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                            <br>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-encaminhamentos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                        </form>

                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScript')



@endsection
