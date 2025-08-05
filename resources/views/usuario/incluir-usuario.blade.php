@extends('layouts.app')

@section('title') Incluir usuário @endsection

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            SELECIONAR PESSOA
                        </div>
                    </div>
                </div>
                <div class="card-body">
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
                                    <a href="/usuario-incluir"><input class="btn btn-light btn-sm me-md-2"
                                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                            value="Limpar"></a>
                                </div>
                            </div>
                        </div>
                        </form>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            Total de pessoas: {{ $contar }}
                            <tr style="text-align: center;">
                                <th>ID</th>
                                <th>NAME</th>
                                <th>CPF</th>
                                <th>IDENTIDADE</th>
                                <th>EMAIL</th>
                                <th>AÇÃO</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($result as $results)
                                <tr>
                                <td>{{$results->id}}</td>
                                <td>{{$results->nome_completo}}</td>
                                <td>{{$results->cpf}}</td>
                                <td>{{$results->idt}}</td>
                                <td>{{$results->email}}</td>
                                    <td style="text-align: center;">
                                    <a href="cadastrar-usuarios/configurar/{{$results->id}}">
                                        <input class="btn btn-secondary" type="button" value="Selecionar">
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
