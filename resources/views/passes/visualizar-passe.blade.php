@extends('layouts.app')
@php use Carbon\Carbon; @endphp
@section('title', 'Visualizar Passes')
@section('content')
<button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
    <i class="bi bi-arrow-up"></i>
</button>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <strong>{{ $cronograma->nome }}</strong> - {{ $cronograma->setor }} - {{ $cronograma->dia }} - {{ $cronograma->h_inicio }} às {{ $cronograma->h_fim }}
                </div>

                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped table-sm text-center">
                            <thead class="text-center">
                                <tr>
                                    <th style="width: 150px;">Data</th>
                                    <th style="width: 150px;">Número de Passes</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($dias_cronograma as $cronograma_dia)
                                    @if ($cronograma_dia->nr_acompanhantes > 0)
                                        <tr>
                                            <td>{{ Carbon::parse($cronograma_dia->data)->format('d/m/Y') }}</td>
                                            <td>{{ $cronograma_dia->nr_acompanhantes }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-center mt-4">
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-passe" role="button">Fechar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #btn-back-to-top {
        position: fixed;
        bottom: 20px;
        right: 10px;
        display: none;
        z-index: 100;
    }
</style>

<script>
    let mybutton = document.getElementById("btn-back-to-top");

    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    mybutton.addEventListener("click", backToTop);

    function backToTop() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
@endsection

@section('footerScript')
@endsection
