@extends('layouts.app')

@section('title')

Visualizar Atendimentos


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
                            HISTÓRICO DE ATENDIMENTOS
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do Atendido</legend>
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Atendido:</label>
                            <input type="text" id="" value="{{$result[0]->nm_1}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-2">
                            <label for="disabledTextInput" class="form-label">Data nascimento:</label>
                            <input type="date" class="form-control" id="" value="{{$result[0]->dt_nascimento}}" style="text-align:center;" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:#525252; font-size:12px; font-family:sans-serif">Lista de atendimentos</legend>
                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($result as $results)
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="{{$a++}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$b++}}" aria-expanded="false" aria-controls="flush-collapse{{$c++}}">
                            {{date('d-m-Y', strtotime($results->dh_chegada))}}
                            </button>
                            </h2>
                            <div id="flush-collapse{{$d++}}" class="accordion-collapse collapse" aria-labelledby="{{$e++}}" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
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
                                                <td>{{$results->nm_2}}</td>
                                                <td>{{$results->nome}}</td>
                                                <td>{{$results->nm_4}}</td>
                                                <td>{{$results->dh_inicio}}</td>
                                                <td>{{$results->dh_fim}}</td>
                                                <td>{{$results->descricao}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    <br>
                    <div class="row">
                        <div class="col">
                            <a class="btn btn-danger" href="/gerenciar-atendimentos" style="text-align:right;" role="button">Fechar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')

<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection
