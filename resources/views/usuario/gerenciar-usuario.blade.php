@extends('layouts.app')

@section('title') Gerenciar usuários @endsection

@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">


                        <h4 class="card-title">Lista de Usuários</h4>
                        <div class="row justify-content-center">
                            <form action="" class="form-horizontal mt-4" method="GET">
                                <div class="row mb-4">
                                    <div class="col-4">Nome
                                        <input class="form-control" type="text" maxlength="45"
                                            oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            id="1" name="nome">
                                    </div>
                                    <div class="col-2">CPF
                                        <input class="form-control" type="text" maxlength="45"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            id="1" name="cpf">
                                    </div>

                                    <div class="col"><br>
                                        <input class="btn btn-light btn-sm me-md-2"
                                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                            value="Pesquisar">
                                        <a href="/gerenciar-usuario"><input class="btn btn-light btn-sm me-md-2"
                                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                                value="Limpar"></a>
                            </form>

                                <a href="/usuario-incluir"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                        type="button" value="Incluir Usuário"></a>
                                <a href="/usuario-regenerar-acessos"><input class="btn btn-primary btn-sm me-md-2" style="font-size: 0.9rem;"
                                        type="button" value="Regenerar Acessos"></a>

                                </div>
                                </div>
                                </div>
                                <hr />
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">


                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                          
                                    Total de pessoas: {{ $contar }}
                                            <tr style="text-align: center;">
                                                <th>NOME</th>
                                                <th>CPF</th>
                                                <th>ATIVO</th>
                                                <th>BLOQUEADO</th>
                                                <th>DATA ATIVAÇÃO</th>
                                                <th>AÇÃO</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                          @foreach($result as $results)
                                             <tr>
                                                <td>{{$results->nome_completo}}</td>
                                                <td>{{$results->cpf}}</td>
                                                <td>{{$results->ativo ? 'Sim' : 'Não' }}</td>
                                                <td>{{$results->bloqueado ? 'Sim' : 'Não' }}</td>
                                                <td>{{date('d/m/Y', strtotime($results->data_ativacao))}}</td>
                                                <td>
                                                    <a href="/usuario/alterar/{{$results->id}}"
                                                         class="btn btn-warning btn-sm" type="button"> Alterar
                                                    </a>
                                                    <a href="/usuario/excluir/{{$results->id}}"
                                                         class="btn btn-danger btn-sm" type="button"> Excluir
                                                    </a>
                                                     <a href="/usuario/gerar-Senha/{{$results->id_pessoa}}"
                                                         class="btn btn-primary btn-sm" type="button"> Gerar Senha
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach

                                            @if(session('mensagem'))
                                                <div class="alert alert-success">
                                                    <p>{{session('mensagem')}}</p>
                                                </div>
                                            @endif

                                            @if(session('mensagemErro'))
                                                <div class="alert alert-success">
                                                    <p>{{session('mensagemErro')}}</p>
                                                </div>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                        <!-- end col -->
                    </div>

                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div class="d-flex justify-content-center">
            {{ $result->links('pagination::bootstrap-5') }}
        </div>
@endsection

@section('footerScript')
            <!-- Required datatable js -->
           <script src="{{ URL::asset('/libs/datatables/datatables.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>

@endsection
