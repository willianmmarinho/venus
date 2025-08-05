<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
    </style>
</head>
<body>
    <p><strong>{{ $data['subject'] }}</strong></p>
    <p>{!! nl2br(e($data['message'])) !!}</p>
</body>
</html>
