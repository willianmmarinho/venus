@extends('layouts.app')

@section('title')

Agendar Tratamento

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
                            ALTERAR GRUPO - TRATAMENTO
                        </div>
                    </div>
                </div>
                <div class="card-body">
                        <legend style="color:#525252; font-size:12px; font-family:sans-serif">Dados do Encaminhamento</legend>
                        <fieldset class="border rounded border-secondary p-2">
                        <div class="form-group row">
                            <div class="col">Tipo Prioridade:
                                <input type="text" class="form-control" value="{{$result->prdesc}}" Disabled="Disabled">
                            </div>
                            <div class="col">Nome do assistido:
                                <input type="text" class="form-control" value="{{$result->nm_1}}" Disabled="Disabled">
                            </div>
                            <div class="col">Nome do representante:
                                <input type="text" class="form-control" value="{{$result->nm_2}}" Disabled="Disabled">
                            </div>
                            <div class="col">Parentesco:
                                <input type="text" class="form-control" value="{{$result->nome}}" Disabled="Disabled">
                            </div>
                            <div class="col">Tratamento:
                                <input type="text" class="form-control" value="{{$result->desctrat}}" Disabled="Disabled">
                            </div>
                        </div>
                        </fieldset>
                    <br/>
                    <div class="row"><div class="col">Vermelho: 10% das vagas livres</div></div>
                    <form class="form-horizontal mt-2" method="get" action="/escolher-horario/{{$result->ide}}">
                        @csrf
                        <div class="row g-2 justify-content-evenly" style="text-align:center;  column-gap:10px;">
                            @foreach ($dadosDias as $dadoDia)
                                <div class="col"
                                    style="background-color:light; border-radius:8px; box-shadow: 1px 2px 5px #000000; margin:5px;">
                                    <div class="form-check form-check-inline p-3 d-grid gap-2">
                                        <input type="radio" class="btn-check" name="dia"
                                            id="option{{ $dadoDia->dia }}" value="{{ $dadoDia->dia }}">
                                        <label class="btn btn-outline-dark"
                                            for="option{{ $dadoDia->dia }}">{{ $dadoDia->dia_semana }}</label>
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>Nr Grupos</td>
                                                    <td>Max vagas</td>
                                                    <td>Vagas Disp</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $dadoDia->numerocronograma }}</td>
                                                    <td>{{ $dadoDia->maximovagas }}</td>
                                                    @if (($dadoDia->maximovagas / 100) * 10 < $dadoDia->vagas)
                                                        <td style="background-color:#90EE90;">{{ $dadoDia->vagas }}
                                                        </td>
                                                    @else
                                                        <td style="background-color:#FA8072;">{{ $dadoDia->vagas }}
                                                        </td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    <br/>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-encaminhamentos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto" >
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                    </form>
                    </div> <br/>
                </div>
            </div>

        </div>

    </div>

</div>


@endsection

@section('footerScript')


@endsection
