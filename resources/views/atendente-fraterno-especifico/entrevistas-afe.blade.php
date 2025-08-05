@extends('layouts/app')
@section('title')
Encaminhar Entrevista
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
                            ENCAMINHAR PARA ENTREVISTA
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">Nr Atendimento
                            <input class="form-control" type="numeric" name="id" value="{{$assistido[0]->idat}}" disabled>
                        </div>
                        <div class="col">Nome Atendido
                            <input class="form-control" type="text" name="nome" value="{{$assistido[0]->nm_1}}" disabled>
                        </div>
                    </div>
                    <form class="form-horizontal mt-4" method="POST" action="/entrevistas-afe/{{$assistido[0]->idat}}">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="afe" name="afe" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="afe" class="form-check-label">Declarar Alta - AFE</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="ame" name="ame" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="ame" class="form-check-label">Assessoria da Medicina Espiritual - AME</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="diamo" name="diamo" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="diamo" class="form-check-label">Divisão de Apoio ao Médium Ostensivo em Eclosão da Mediunidade - DIAMO</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="nutres" name="nutres" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="nutres" class="form-check-label">Núcleo de Tratamento Espiritual - NUTRES</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="gel" name="gel" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="gel" class="form-check-label">Grupo de Evangelho no Lar - GEL</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col" style="text-align: right;">
                            <a class="btn btn-danger" href="/atendendo-afe" style="text-align:right; margin-right: 10px" role="button">Cancelar</a>
                            <button type="submit" class="btn" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <div>
        </div>
    </div>
</div>

@endsection
