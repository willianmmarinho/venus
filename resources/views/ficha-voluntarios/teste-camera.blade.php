<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Teste de Câmera</title>
    <style>
        video,
        canvas {
            display: block;
            margin: 10px auto;
            border: 2px solid #000;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">Teste de Câmera</h2>
    <video id="video" autoplay playsinline style="display: none;"></video>

    <canvas id="canvasPreview"></canvas>

    <button onclick="tirarFoto()">Tirar Foto</button>

    <canvas id="canvas"></canvas> <!-- Imagem final -->

    {{-- Importa o JS corretamente --}}
    
</body>

</html>
