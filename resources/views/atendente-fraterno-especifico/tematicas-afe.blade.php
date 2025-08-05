@extends('layouts/app')
@section('title', 'Temáticas')
@section('content')


    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row  mb-4">
                    <div class="col-2">Nr atendimento
                        <input class="form-control" type="numeric" name="id" value="{{ $assistido[0]->idat }}" disabled>
                    </div>
                    <div class="col">Nome assistido
                        <input class="form-control" type="text" name="nome" value="{{ $assistido[0]->nm_1 }}"
                            disabled>
                    </div>
                </div>
                <form class="form-horizontal mt-4" method="POST" action="/tematicas-afe/{{ $assistido[0]->idat }}">
                    @csrf
                    <div class="row mb-4">
                        <div class="col" style="text-align:left;">Anotações:
                            <textarea class="form-control" maxlength="300" rows="3" type="text" name="nota" value=""></textarea>
                        </div>
                    </div>
                    <fieldset class="border rounded border-secoundary ">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12" style="text-align:left;">
                                        <span style="color:#525252; font-size:14px;">Temática do Atendimento</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col" style="text-align:center">Espirituais
                                        <div class="form-check m-2">
                                            <input id="21" type="checkbox" name="tematicas[]" data-size="small"
                                                data-size="small" data-toggle="toggle" data-onstyle="success"
                                                data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não"
                                                value="1">
                                            <label for="maf" class="form-check-label tooltips">1.1<span
                                                    class="tooltiptext">Mediunidade aflorada</span></label>
                                        </div>
                                        <div class="form-check m-2">
                                            <input id="1" type="checkbox" name="tematicas[]" data-size="small"
                                                data-size="small" data-toggle="toggle" data-onstyle="success"
                                                data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não"
                                                value="2">
                                            <label for="ies" class="form-check-label tooltips">1.2<span
                                                    class="tooltiptext">Influenciação espiritual</span></label>
                                        </div>
                                        <div class="form-check m-2">
                                            <input id="2" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="3">
                                            <label for="obs" class="form-check-label tooltips">1.3<span
                                                    class="tooltiptext">Obsessão</span></label>
                                        </div>
                                    </div>
                                    <div class="col" style="text-align:center;">Relacionamento
                                        <div class="form-check  m-2">
                                            <input id="5" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="4">
                                            <label for="coj" class="form-check-label tooltips">2.1<span
                                                    class="tooltiptext">Conjugal</span></label>
                                        </div>
                                        <div class="form-check   m-2">
                                            <input id="6" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="5">
                                            <label for="fam" class="form-check-label tooltips">2.2<span
                                                    class="tooltiptext">Familiar</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="7" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="6">
                                            <label for="soc" class="form-check-label tooltips">2.3<span
                                                    class="tooltiptext">Social</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="8" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="7">
                                            <label for="prf" class="form-check-label tooltips">2.4<span
                                                    class="tooltiptext">Profissional</span></label>
                                        </div>
                                    </div>
                                    <div class="col" style="text-align:center;">Físicas/mentais
                                        <div class="form-check  m-2">
                                            <input id="14" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="8">
                                            <label for="sau" class="form-check-label tooltips">3.1<span
                                                    class="tooltiptext">Saúde</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="15" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="9">
                                            <label for="pdg" class="form-check-label tooltips">3.2<span
                                                    class="tooltiptext">Psiquiátrica diagnosticada</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="16" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="10">
                                            <label for="sex" class="form-check-label tooltips">3.3<span
                                                    class="tooltiptext">Sexualidade</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="17" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="11">
                                            <label for="dts" class="form-check-label tooltips">4.1<span
                                                    class="tooltiptext">Desânimo / Tristeza /<br />
                                                    Solidão</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="18" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="12">
                                            <label for="adp" class="form-check-label tooltips">4.2<span
                                                    class="tooltiptext">Ansiedade / Depressão</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="19" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="13">
                                            <label for="dqu" class="form-check-label tooltips">4.3<span
                                                    class="tooltiptext">Dependência química</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="20" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="14">
                                            <label for="est" class="form-check-label tooltips">4.4<span
                                                    class="tooltiptext">Estresse</span></label>
                                        </div>
                                    </div>
                                    <div class="col" style="text-align:center;">Comportamentais
                                        <div class="form-check  m-2">
                                            <input id="3" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="15">
                                            <label for="abo" class="form-check-label tooltips">5.1<span
                                                    class="tooltiptext">Aborto</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="4" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="16">
                                            <label for="sui" class="form-check-label tooltips">5.2<span
                                                    class="tooltiptext">Suicídio</span></label>
                                        </div>
                                    </div>
                                    <div class="col" style="text-align:center;">Cotidiano
                                        <div class="form-check  m-2">
                                            <input id="9" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="17">
                                            <label for="dou" class="form-check-label tooltips">6.1<span
                                                    class="tooltiptext">Interesse pela Doutrina</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="10" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="18">
                                            <label for="son" class="form-check-label tooltips">6.2<span
                                                    class="tooltiptext">Sonhos</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="11" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="19">
                                            <label for="esp" class="form-check-label tooltips">6.3<span
                                                    class="tooltiptext">Medo de espíritos</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="12" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="20">
                                            <label for="dpr" class="form-check-label tooltips">6.4<span
                                                    class="tooltiptext">Dificuldades profissionais</span></label>
                                        </div>
                                        <div class="form-check  m-2">
                                            <input id="13" type="checkbox" name="tematicas[]" data-size="small"
                                                data-toggle="toggle" data-onstyle="success" data-offstyle="danger"
                                                data-onlabel="Sim" data-offlabel="Não" value="21">
                                            <label for="dpr" class="form-check-label tooltips">6.5<span
                                                    class="tooltiptext">Desencarne de ente querido</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col" style="text-align: right;">
                                    <a class="btn btn-danger" href="/atendendo-afe"
                                        style="text-align:right; margin-right: 50px" role="button">Cancelar</a>
                                    <button type="submit" class="btn btn-primary"
                                        style="background-color:#007bff; color:#fff;"
                                        data-bs-dismiss="modal">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
    <div>
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
