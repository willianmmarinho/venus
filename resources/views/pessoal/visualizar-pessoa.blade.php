@extends('layouts.app')

@section('title') Visualizar Pessoa @endsection

@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="col-12">
            <br>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12">
                                <div class="col">
                                   VISUALIZAR PESSOA
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/executa-edicao/{{$lista[0]->idp}}">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom01" class="form-label">Nome completo</label>
                                        <input class="form-control" type="text" maxlength="40" id="" name="nome" value="{{$lista[0]->nome_completo}}" required="required" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom02" class="form-label">CPF</label>
                                        <input class="form-control" type="numeric" maxlength="11" value="{{$lista[0]->cpf}}" id="" name="cpf" required="required" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" disabled>
                                    </div>
                                </div> --}}
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom04" class="form-label">Sexo</label>
                                        <select class="form-control" id="" name="sex" required="required" disabled>
                                            <option value="{{$lista[0]->sexo}}">{{$lista[0]->tipo}}</option>
                                            @foreach($sexo as $sexos)
                                            <option @if (old ('sex') == $sexos->id) {{'selected="selected"'}} @endif value="{{ $sexos->id }}">{{$sexos->tipo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom03" class="form-label">Data Nascimento</label>
                                        <input class="form-control" type="date" value="{{$lista[0]->dt_nascimento}}" id="" name="dt_nasc" required="required" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom05" class="form-label">Status</label>
                                        <select class="form-control" id="status_pessoa" name="status" required="required" disabled>
                                            <option value="{{$lista[0]->status}}" selected>{{$lista[0]->tipo_status_pessoa}}</option>
                                            <option value="1">Ativo</option>
                                            <option value="0">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom06" class="form-label">Motivo</label>
                                        <select class="form-control" id="tp_motivo" name="motivo" required="required" disabled>
                                            <option value="{{ $lista[0]->tipo_motivo_status_pessoa }}">{{ $lista[0]->motivo_status_pessoa_tipo_motivo }}</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                                <div class="row mt-2 justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <a class="btn btn-danger" href="/gerenciar-pessoas" role="button">Fechar</a>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>


@endsection
