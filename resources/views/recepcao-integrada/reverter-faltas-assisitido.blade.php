@extends('layouts.app')

@section('title')
    Reverter faltas assistidos
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
                                REVERTER FALTAS DO ASSISTIDO
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-2" method="post"
                            action="/remarcar-faltas-assistido">
                            @csrf
                            <input type="text" name="url" value="{{$urlAnterior}}" hidden>
                                <div class="form-group row">
                                    <div class="col">
                                        <label for="disabledTextInput" class="form-label">Assistido:</label>
                                        <input type="text" id="" value="{{ $result->nm_1 }}"
                                            class="form-control" placeholder="Disabled input" disabled>
                                    </div>
                                    <div class="col-2">
                                        <label for="disabledTextInput" class="form-label">Sexo:</label>
                                        <input type="text" id="" value="{{ $result->tipo }}"
                                            style="text-align:center;" class="form-control" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col-3">
                                        <label for="disabledTextInput" class="form-label">Dt nascimento:</label>
                                        <input type="date" class="form-control" id="" name="date"
                                            value="{{ $result->dt_nascimento }}" class="form-control"
                                            placeholder="Disabled input" disabled>
                                    </div>
                                </div>
                            <legend>

                                <!-- Dados de Presenças -->
                                @if($list)
                                <br>
                                <legend style="font-size:14px; font-weight:bold;">Dados de Presenças</legend>
                                @else
                                <br />
                                <br />
                                <center>
                                    <legend style="font-size:14px; font-weight:bold;">Nenhuma Presença/Falta Registrada</legend>
                                </center>
                                <br />
                                @endif

                                <div class="accordion" id="accordionExample">
                                    @foreach ($list as $key => $presenca)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                                    aria-controls="collapse{{ $key }}">
                                                    {{ $key }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $key }}" class="accordion-collapse collapse  {{ $loop->first ? 'show' : ''}}"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <table
                                                        class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                                        <thead>
                                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                                <th>REVERTER</th>
                                                                <th>DATA</th>
                                                                <th>GRUPO</th>
                                                                <th>PRESENÇA</th>
                                                            </tr>
                                                        <tbody style="font-size: 14px; color:#000000; text-align:center;">
                                                            <div class="accordion" id="accordionExample">
                                                                @foreach ($presenca as $itemPresenca)
                                                                    <tr>
                                                                        <td>
                                                                            <input
                                                                                class="form-check-input checkbox-trigger check_io"
                                                                                type="checkbox"
                                                                                name="checkbox[{{ $itemPresenca->id }}]"
                                                                                value="{{ $itemPresenca->presenca }}">

                                                                        </td>
                                                                        <td>{{ date('d/m/Y', strtotime($itemPresenca->data)) }}
                                                                        </td>
                                                                        <td>{{ $itemPresenca->nome }}</td>
                                                                        @if ($itemPresenca->presenca == 1)
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
                                    @endforeach
                                </div>


                                <br />
                                <div class="row mt-1 justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                            
                                        <a class="btn btn-danger" href="{{ $urlAnterior }}" role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection

        @section('footerScript')
            <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
        @endsection
