@extends('layouts.app')
@section('title')
Incluir Fatos
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <br>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Cadastrar Fato</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-4" method="post" action="/incluir-fatos">
                        @csrf
                        <div class="mb-3">
                            <label for="fatos" class="form-label">Informe o fato:</label>
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <input type="text" class="form-control" id="fatos" aria-describedby="" name="fato">
                        </div>
                        <div class="row justify-content-center">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger btn-block" href="/gerenciar-fatos" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <button class="btn btn-primary btn-block">Confirmar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
