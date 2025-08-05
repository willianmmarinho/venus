@extends('layouts.app')

@section('title', 'Editar Atendente Dia')

@section('content')
<br />
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Editar Atendente do Dia</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/altera-atendente-dia/{{ $atende->idatd }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome AFI:</label>
                        <input type="text" class="form-control" value="{{ $atende->nm_4 }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Número:</label>
                        <input type="number" class="form-control" value="{{ $atende->idatd }}" disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Número da Sala:</label>
                        <select class="form-select text-center" name="sala">
                            <option value="{{ $atende->id_sala }}" selected>{{ $atende->nm_sala }}</option>
                            @foreach ($sala as $salas)
                                @if ($salas->numero > $atende->nm_sala && $atende->nm_sala > 0)
                                @endif
                                <option value="{{ $salas->id }}">{{ $salas->numero }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Atendimento:</label>
                        <select class="form-select text-center" name="tipo_atendimento">
                            @foreach ($tipo_atendimento as $tipos)
                                <option value="{{ $tipos->id }}" {{ $tipos->id == $atende->id_tipo_atendimento ? 'selected' : '' }} {{ (!$membro and $tipos->id == 2) ? 'hidden' : null }}>
                                    {{ $tipos->sigla }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Grupo:</label>
                        <select class="form-select text-center" name="grupo">
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id_cronograma }}" {{ $atende->nomeg == $grupo->nome ? 'selected' : '' }}>
                                    {{ $grupo->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 d-grid mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-atendente-dia">Cancelar</a>
                    </div>
                    <div class="col-md-4 d-grid mx-auto">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
@endsection
