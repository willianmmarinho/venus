@extends('layouts.app')

@section('title') Gerenciar Fatos @endsection


@section('content')




<div class="container-fluid" ;>
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR FATOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                    <form action="{{route('descricao')}}" class="form-horizontal mt-4" method="GET" >
                    <div class="row">
                        <div class="col-3">Descrição
                            <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar descrição {{ request('descrição') }}">
                        </div>

                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-fatos"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        </form>
                        <a href="/criar-fatos"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Novo Fato +"></a>

                        </div>
                    </div>
            </div>

        </div>
        <hr>

    <div class="row" style="text-align:center;">
        <div class="table">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <th scope="col">ID</th>
                <th scope="col">DESCRIÇÃO</th>
                <th scope="col">AÇÕES</th>
                @foreach ($lista as $listas)
                <tr>
                    <td>{{ $listas->id }}</td>
                    <td>{{ $listas->descricao }}</td>
                    <td>
                        <a href="/editar-fatos/{{ $listas->id }}" type="button" class="btn btn-outline-warning btn-sm tooltips" >
                            <span class="tooltiptext">Editar</span>
                            <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                        </a>

                        <a href="/deletar-fatos" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#confirmacaoDelecao" onclick="confirmarExclusao('{{ $listas->id }}')" >
                            <span class="tooltiptext">Deletar</span>
                            <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Modal de Confirmação de Exclusão -->


<div class="modal fade" id="confirmacaoDelecao"{{ $listas->id}} tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#DC4C64">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Confirmação de Exclusão </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center; ">
                Tem certeza que deseja excluir este fato <br/><span style="color:#DC4C64; font-weight: bold;"></span>&#63;
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <a type="button" class="btn btn-primary"  id="btn-confirmar-exclusao" onclick="confirmarDelecao()">Confirmar </a>
            </div>
        </div>
    </div>
    {{ $lista->links('pagination::bootstrap-5') }}
</div>
 <script src="caminho/para/bootstrap/js/bootstrap.bundle.min.js" async defer></script>
<link href="caminho/para/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<script>
    function confirmarExclusao(id) {
        document.getElementById('btn-confirmar-exclusao').setAttribute('data-id', id);
        $('#confirmacaoDelecao').modal('show');
    }

    function confirmarDelecao() {
        var id = document.getElementById('btn-confirmar-exclusao').getAttribute('data-id');
        window.location.href = '/deletar-fatos/' + id;
    }
</script>
<script>

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    </script>

@endsection
