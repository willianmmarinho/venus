@extends('layouts.app')

@section('title')
    Visualizar Versões
@endsection

@section('content')

<br />
<div class="container">
    <form action="/atualizar-versoes/{{ $versao->id }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
              Visualizar Versão
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        Versão
                        <input type="text" class="form-control" placeholder="0.0.0" name="versao" value="{{ $versao->versao }}" disabled>
                      </div>
                    <div class="col-6 mt-3">
                        Data Inicio
                        <input type="text" class="form-control" name="versao" value="{{ date('d/m/Y', strtotime($versao->dt_inicio)) }}" disabled>
                      </div>
                    <div class="col-6 mt-3">
                        Data Fim
                        <input type="text" class="form-control"name="versao" value="{{ $versao->dt_fim ? date('d/m/Y', strtotime($versao->dt_fim)) : ''}}" disabled>
                      </div>
                    <div class="col-12 mt-3">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-light active" aria-current="true">Descrição</li>
                            @foreach ($descricoes as $descricao)
                            <li class="list-group-item">{{ $descricao }}</li>
                            @endforeach

                          </ul>
                      </div>


                    </div>

                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <br>
                            <a class="btn btn-danger" href="/gerenciar-versoes" role="button">Fechar</a>
                        </div>
                      </div>



            </div>
          </div>
    </form>
</div>




@endsection
