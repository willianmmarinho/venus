<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário de Contato</title>
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <h2>Formulário de Contato</h2>

        <!-- Mostra mensagens de sucesso -->
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?= session('success') ?>
            </div>
        <?php endif; ?>

            <form action="/enviar-email/{{$destino[0]->id_cronograma}}" method="POST">
                <!-- CSRF Token obrigatório em Laravel -->
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="nome" class="form-label">De:</label>
                        <input type="text" class="form-control" value="ati@comunhaoespirita.org.br" id="emailo" name="emailo" readonly >
                    </div>
                    <div class="col">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" class="form-control" value="Central de Voluntários" id="name" name="name" readonly >
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="nome" class="form-label">Para:</label>
                        <input type="text" class="form-control" value="{{$destino[0]->emaild}}" id="emaild" name="emaild" readonly >
                    </div>
                     <div class="col">
                        <label for="nome" class="form-label">CC:</label>
                        <input type="text" class="form-control" value="{{$destino[0]->emailv}}" id="emailv" name="emailv" readonly >
                    </div>
                </div>
                <div class="row">
                    <label for="email" class="form-label">Assunto:</label>
                    <input type="email" class="form-control" value="Encaminhamento de Voluntário pela Central de Voluntários - Comunhão Espírita de Brasília" id="subject" name="subject" readonly >
                </div>
                <div class="row">
                    <label for="mensagem" class="form-label">Mensagem:</label>
                    <textarea class="form-control" id="message" name="message" rows="15" required>
                        Voluntário: {{$destino[0]->nome_completo}}
                        Telefones:  ({{$destino[0]->dddvol}})- {{$destino[0]->celular}}
                        Email:  {{$destino[0]->emailv}}

                        Grupo:  {{$destino[0]->nome_grupo}}
                        Dia/horário: {{$destino[0]->sigla}}-{{$destino[0]->h_inicio}}-{{$destino[0]->h_fim}}
                        Coordenador: {{$destino[0]->nomedirigente}}
                        Contato: ({{$destino[0]->ddddirigente}})-{{$destino[0]->celulardirigente}}
                        Email: {{$destino[0]->emaild}}

                        Atenciosamente,
                        {{$trabalhador}}
                        Central de Voluntários - Comunhão Espírita de Brasília
                        Jesus trabalha usando as nossas mãos
                    </textarea>
                </div>
                <br>
                <div class="row gap-4">                    
                    <div class="col">
                        <a class="btn btn-danger w-100" href="javascript:history.back()" role="button">Cancelar</a>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Enviar</button>
                    </div>
                </div>
            </form>
    </div>
</body>

</html>