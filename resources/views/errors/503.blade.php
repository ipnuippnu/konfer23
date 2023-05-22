<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perbaikan - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        html, body{
            background: #32384C;
        }

        html,body,.container{
            min-height: 100vh;
        }

        .img1{
            height: 500px;
            object-fit: contain;
        }
        
        .img2{
            height: 200px;
        }
    </style>
</head>
<body>
    <div class="container d-flex">
        <div class="row w-100 mx-auto">
            <div class="col-sm-6 my-auto d-sm-block d-none">
                <img src="{{ asset('img/col1.png') }}" class="img-fluid img1" alt="Maskot YMF">
            </div>
            <div class="col-sm-6 my-auto">
                <img src="{{ asset('img/col2.svg') }}" class="img-fluid img2" alt="Maskot YMF">
            </div>
        </div>
    </div>
</body>
</html>