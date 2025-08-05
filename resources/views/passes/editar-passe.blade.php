@extends('layouts.app')
@php use Carbon\Carbon; @endphp
@section('title', 'Editar Grupos')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                {{ $cronograma->nome }}-{{ $cronograma->setor }}- {{ $cronograma->dia }} -
                                {{ $cronograma->h_inicio }} -
                                {{ $cronograma->h_fim }}
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card-body">

                        <div class="accordion" id="accordionExample">
                            @foreach ($dias_cronograma as $index => $cronograma_dia)
                                {{-- @if ($cronograma_dia->nr_acompanhantes > 0) --}}
                                    <!-- Filtrando datas com acompanhantes -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                                aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                {{ Carbon::parse($cronograma_dia->data)->format('d/m/Y') }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $index }}"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">

                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="row">

                                                                    <div class="col">
                                                                        <p>Número de Passes: <span
                                                                                id="nrAcompanhantes{{ $index }}">{{ $cronograma_dia->nr_acompanhantes }}</span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col d-flex justify-content-end">
                                                                        <button
                                                                            class="btn btn-outline-warning btn-sm tooltips"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#editModal{{ $cronograma_dia->id }}"
                                                                            data-id="{{ $cronograma_dia->id }}">
                                                                            <span class="tooltiptext"
                                                                                style="z-index:10000">Editar</span>
                                                                            <i class="bi bi-pencil"
                                                                                style="font-size: 1rem; color:#000;"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal{{ $cronograma_dia->id }}"
                                                    tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background-color:#ffc107;color:white">
                                                                <h5 class="modal-title" id="editModalLabel">Editar Número de
                                                                    Passes</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="editForm" method="POST"
                                                                    action="/atualizar-passe/{{ $cronograma->id }}?data={{ $cronograma_dia->data }}">
                                                                    @csrf
                                                                    <center>
                                                                        <div class="col-10 mt-3">
                                                                            <input type="hidden" id="passId"
                                                                                name="passId">
                                                                            <div class="mb-3">
                                                                                <label for="nrAcompanhantes"
                                                                                    class="form-label">Número
                                                                                    de Passes</label>
                                                                                <input type="number" class="form-control"
                                                                                    id="nrAcompanhantes"
                                                                                    name="nr_acompanhantes" placeholder="0"
                                                                                    min="1" max="500" required>
                                                                            </div>
                                                                        </div>
                                                                    </center>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button data-bs-dismiss="modal"
                                                                    class="btn btn-danger">Cancelar</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Confirmar</button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                {{-- @endif --}}
                            @endforeach
                        </div>

                        <div class="row justify-content-center mt-3">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-passe" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button class="btn btn-primary">Confirmar</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
    <script>
        const editButtons = document.querySelectorAll('.tooltips');
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const nrAcompanhantes = button.getAttribute('data-nr-acompanhantes');
                document.getElementById('passId').value = id;
                document.getElementById('nrAcompanhantes').value = nrAcompanhantes;
                document.getElementById('editForm').action = `/atualizar-passe/${id}`;
            });
        });
    </script>
    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 10px;
            display: none;
            z-index: 100;
        }
    </style>

@endsection
