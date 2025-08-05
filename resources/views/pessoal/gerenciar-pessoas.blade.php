@extends('layouts.app')

@section('title') Gerenciar Pessoas @endsection

@section('content')

<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PESSOAS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
                <form action="{{route('pesdex')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class="col">Nome
                        <input class="form-control" type="text" maxlength="45" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="1" name="nome" value="{{$nome}}">
                    </div>
                    <div class="col-2">CPF
                        <input class="form-control" type="text" maxlength="11" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="2" name="cpf" value="{{$cpf}}">
                    </div>
                    <div class="col-2">Status
                        <select class="form-select" id="3" name="status" type="numeric" required="required">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                            <option value="*">Todos</option>
                        </select>
                    </div>
                    <div class="col-5"><br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-pessoas"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                    <a href="/dados-pessoa"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" style="font-size: 0.9rem;" type="button" value="Nova Pessoa +"></a>
                    <a href="/gerenciar-atendimentos"><input class="btn btn-danger btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" style="font-size: 0.9rem;" type="button" value="Retornar principal"></a>
                    </div>
                </div>
        </div>

            <hr>
            Quantidade filtrada: {{$soma}}
            <div class="table">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">NOME</th>
                            <th class="col">NASCIMENTO</th>
                            <th class="col">SEXO</th>
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach($pessoa as $pessoas)
                        <tr>
                            <td scope="" style="text-align: left;">{{$pessoas->nome_completo}}</td>
                            {{-- <td scope="" >{{str_pad($pessoas->cpf, 11, "0", STR_PAD_LEFT)}}</td> --}}
                            <td scope="" >{{$pessoas->dt_nascimento ? date( 'd/m/Y' , strtotime($pessoas->dt_nascimento)) : '--'}}</td>
                            <td scope="" >{{$pessoas->tipo}}</td>
                            <td scope="" >{{$pessoas->tpsta}}</td>
                            <td scope="">
                                @if(in_array(2 ,session()->get('usuario.acesso')))
                                <a href="/ficha-voluntario/{{ $pessoas->idp }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                   <i class="bi bi-file-person" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                @endif
                                    @if(in_array(2 ,session()->get('usuario.acesso')))
                                <a href="/editar-pessoa/{{ $pessoas->idp }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                @endif
                                <a href="/visualizar-pessoa/{{ $pessoas->idp }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>
                                @if(in_array(2 ,session()->get('usuario.acesso')))
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal{{ $pessoas->idp }}" >
                                    <span class="tooltiptext">Deletar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                @endif



                                <!-- Modal de Exclusao -->
                                <div class="modal fade" id="modal{{ $pessoas->idp }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color:#DC4C64">
                                                <h5 class="modal-title" id="exampleModalLabel" style="color:white">Exclusão de pessoa </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja excluir essa pessoa <br /><span style="color:#DC4C64; font-weight: bold;">{{ $pessoas->nome_completo }}</span>&#63;
                                            </div>
                                            <div class="modal-footer mt-3">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                                <a type="button" class="btn btn-primary" href="/excluir-pessoa/{{ $pessoas->idp }}">Confirmar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- Fim Modal de Exclusao --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
            {{ $pessoa->links('pagination::bootstrap-5') }}
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
    </div>
</div>



<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })


</script>


@endsection

@section('footerScript')


@endsection
