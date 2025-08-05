@extends('layouts.app')
@section('title', 'Editar Limite Cronograma')
@section('content')


    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR LIMITE CRONOGRAMA
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-limite-cronograma/{{ $cronograma->id }}">
                    @csrf


                    <div class="row">
                        <div class="col-12">
                            Cronograma
                            <input class="form-control" type="text"
                                value="{{ $cronograma->nome }} ({{ $cronograma->setor }})-{{ $cronograma->dia }} | {{ $cronograma->h_inicio }}/{{ $cronograma->h_fim }} | Sala {{ $cronograma->numero }}"
                                disabled>
                        </div>
                        <div class="col mt-3">
                            Limite de Assistidos
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <input class="form-control" type="number" min="1" max="800" name="max_atend" required
                                value="{{ $cronograma->max_atend }}">
                        </div>
                        <div class="col mt-3">
                            Limite de Membros
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <input class="form-control" type="number" min="1" max="100" name="max_trab" required
                                value="{{ $cronograma->max_trab }}">
                        </div>
                    </div>



            </div>
        </div>
        <div class="row mt-3 justify-content-center">
            <div class="d-grid gap-1 col-4 mx-auto">
                <a class="btn btn-danger" href="/gerenciar-grupos-membro" role="button">Cancelar</a>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
            </form>
        </div>
    </div>


@endsection
