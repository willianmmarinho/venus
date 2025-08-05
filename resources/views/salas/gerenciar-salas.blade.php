@extends('layouts.app')

@section('title') Gerenciar Salas @endsection

@section('content')
<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR SALAS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <form action="{{route('salas')}}" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-3">Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome {{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col-3">Número
                        <input class="form-control" type="text" id="numero" name="numero" placeholder="Pesquisar número {{ request('numero') }}">
                    </div>

                    <div class="col"><br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-salas"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                        <a href="/criar-salas"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Nova Sala +"></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr>

    <div>Quantidade de salas: {{ $contar }}</div>
    <div class="row" style="text-align:center;">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col-2">NOME</th>
                        <th class="col">FINALIDADE SALA</th>
                        <th class="col">NÚMERO</th>
                        <th class="col">LOCALIZAÇÃO</th>
                        <th class="col">M² DA SALA</th>
                        <th class="col">NÚMERO DE LUGARES</th>
                        <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach ($sala as $salas)
                    <tr>
                        <td> {{$salas->nome1}} </td>
                        <td> {{$salas->descricao}} </td>
                        <td> {{$salas->numero}} </td>
                        <td> {{$salas->nome2}} </td>
                        <td> {{$salas->tamanho_sala}} </td>
                        <td> {{$salas->nr_lugares}} </td>
                        <td class="text-center">{{$salas->status_sala ? 'Ativo' : 'Inativo' }}</td>
                        <td>
                            <a href="/editar-salas/{{$salas->ids}}" type="button" class="btn btn-outline-warning btn-sm tooltips">
                                <span class="tooltiptext">Editar</span>
                                <i class="bi bi-pencil" style="font-size: 1.1rem; color:#000;"></i>
                            </a>
                            <a href="/visualizar-salas/{{$salas->ids}}" type="button" class="btn btn-outline-primary btn-sm tooltips">
                                <span class="tooltiptext">Visualizar</span>
                                <i class="bi bi-search" style="font-size: 1.1rem;color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                            <a href="/deletar-salas/{{ $salas->ids }}" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#confirmacaoDelecao" onclick="confirmarExclusao('{{ $salas->ids }}', '{{ $salas->nome1 }}')">
                                <span class="tooltiptext">Inativar</span>
                                <i class="bi bi-x-circle" style="font-size: 1.1rem; color:#000;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
</div class="d-flex justify-content-center">
{{ $sala->links('pagination::bootstrap-5') }}
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="confirmacaoDelecao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#DC4C64">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Confirmação de Exclusão </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center; ">
                Tem certeza que deseja excluir essa sala? <br /><span id="modal-body-text" style="color:#DC4C64; font-weight: bold;"></span>&#63;
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <a type="button" class="btn btn-primary"  id="btn-confirmar-exclusao" onclick="confirmarDelecao()">Confirmar </a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarExclusao(id, nome) {
        document.getElementById('btn-confirmar-exclusao').setAttribute('data-id', id);
        document.getElementById('modal-body-text').innerText = nome;
        $('#confirmacaoDelecao').modal('show');
    }

    function confirmarDelecao() {
        var id = document.getElementById('btn-confirmar-exclusao').getAttribute('data-id');
        window.location.href = '/deletar-salas/' + id;
    }
</script>

@endsection
