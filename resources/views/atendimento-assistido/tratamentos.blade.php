@extends('layouts/app')
@section('title', 'Encaminhar Tratamento')
@section('content')

<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            ENCAMINHAR PARA TRATAMENTO/GRUPOS
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
                    <form class="form-horizontal mt-4" method="POST" action="/tratamentos/{{$assistido[0]->idat}}/{{$assistido[0]->idas}}">
                    @csrf
                    <div class="row form-group">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="ga" name="ga" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="ga" class="form-check-label">Grupo Acolher - GA</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="gv" name="gv" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="gv" class="form-check-label">Grupo Viver - GV</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="gdq" name="gdq" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="gdq" class="form-check-label">Grupo de Dependência Química - GDQ</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="pph" name="pph" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="pph" class="form-check-label">Palestra/Passe de Harmonização - PPH</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="ptd" name="ptd" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="ptd" class="form-check-label">Passe Tratamento Desobsessivo - PTD</label>
                            </div>
                            <!-- <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="ptig" name="ptig" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="ptig" class="form-check-label">Passe Tratamento Integral - PTIg</label>
                            </div>
                            <br>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="pti" name="pti" class="form-check-input" data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                <label for="pti" class="form-check-label">Passe Tratamento Intensivo - PTI</label>
                            </div> -->
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col" style="text-align: right;">
                            <a class="btn btn-danger" href="/atendendo" style="text-align:right; margin-right: 10px" role="button">Cancelar</a>
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
