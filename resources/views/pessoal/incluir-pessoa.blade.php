@extends('layouts.app')
@section('title', 'Incluir Pessoa')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                INCLUIR PESSOA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="POST" action="/criar-pessoa">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom01" class="form-label">Nome</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <input class="form-control" type="text" maxlength="80"
                                            oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            id="" name="nome" value="{{ old('nome') }}" required="required">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom02" class="form-label">CPF</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <input class="form-control" type="text" maxlength="11"
                                            placeholder="888.888.888-88"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            value="{{ old('cpf') }}" id="" name="cpf" required="required">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom04" class="form-label">Sexo</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <select class="form-select" id="" name="sex" required="required">
                                            <option value=""></option>
                                            @foreach ($sexo as $sexos)
                                                <option @if (old('sex') == $sexos->id) {{ 'selected="selected"' }} @endif
                                                    value="{{ $sexos->id }}">{{ $sexos->tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom03" class="form-label">Data Nascimento</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <input class="form-control" type="date" id="" name="dt_na"
                                            value="{{ old('dt_na') }}" required="required">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom05" class="form-label">DDD</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <select class="form-select" id="validationCustom05" name="ddd"
                                            required="required">
                                            <option value=""></option>
                                            @foreach ($ddd as $ddds)
                                                <option value="{{ $ddds->id }}"
                                                    @if (old('ddd') == $ddds->id) selected="selected" @endif>
                                                    {{ $ddds->descricao }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-5" style="text-align:left;">
                                        <label for="validationCustom06" class="form-label">Celular</label>
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                        <input class="form-control" minlength="9" maxlength="9" type="text"
                                            name="celular" required="required" value="{{ old('celular') }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);"
                                            placeholder="" pattern="\d{9}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-5" style="text-align:left;">
                                    <label for="validationCustom06" class="form-label">Telefone Estrangeiro</label>
                                    <span class="tooltips"></span>
                                    <input class="form-control" type="text" name="cel_estrangeiro" value="{{ old('cel_estrangeiro') }}"
                                        oninput="this.value = this.value.replace(/[^+\d\(\)\-\s]/g, '').slice(0, 15);"
                                        placeholder="Ex: +1 (415) 555-1234" pattern="[\+]?[0-15]{1,3}[\s]?[(]?[0-9]{1,3}[)]?[\s]?[0-9]{3}[\-]?[0-9]{4}">
                                </div>

                                <div class="col-6 mb-5" style="text-align:left;">
                                    <label for="validationCustom06" class="form-label">Telefone Alternativo</label>
                                    <span class="tooltips"></span>
                                    <input class="form-control" type="text" name="tel_fixo" value="{{ old('tel_fixo') }}"
                                        oninput="this.value = this.value.replace(/[^+\d\(\)\-\s]/g, '').slice(0, 15);"
                                         pattern="[\+]?[0-15]{1,3}[\s]?[(]?[0-9]{1,3}[)]?[\s]?[0-9]{3}[\-]?[0-9]{4}">
                                </div>
                            </div>


                            <br>
                            <div class="row mt-1 justify-content-center">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <a class="btn btn-danger" href="/gerenciar-pessoas" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-2 col-4 mx-auto">
                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
