@extends('layouts.app')

@section('title')
    Incluir Versões
@endsection

@section('content')

<br />
<div class="container">
    <form action="/armazenar-versoes" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
              Incluir Versão
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        Versão
                        <input type="text" class="form-control" placeholder="0.0.0" name="versao" required>
                      </div>
                    <div class="col-12 mt-3">
                        Descrição
                        <textarea class="form-control" id="descricao" name="descricao" rows="6" required></textarea>
                      </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <br>
                            <a class="btn btn-danger" href="/gerenciar-versoes" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <br>
                            <button class="btn btn-primary">Confirmar</button>
                        </div>
                      </div>



            </div>
          </div>
    </form>
</div>




@endsection
