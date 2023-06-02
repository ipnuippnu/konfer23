<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Youth Muslim Festival</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body{
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
    <div id="badan" class="relative text-center text-white bg-gradient-to-br from-purple-700 to-purple-900 h-screen w-screen overflow-hidden">
        
        <img src="{{ asset('assets/qr/awan.png') }}" class="blur-[1px] animate__slower animate__animated animate__fadeIn">
        <img src="{{ asset('assets/qr/logocahya.png') }}" id="logo" class="animate__slow animate__delay-2s animate__animated animate__fadeInUp" alt="">
        <img src="{{ asset('assets/qr/gedung.png') }}" id="gedung" class=" animate__delay-1s animate__fadeInUp  animate__animated" alt="">
        <img src="{{ asset('assets/qr/kapal.png') }}" class="animate__animated animate__fadeInUp">

        <div class="top-[50%] text-center w-full translate-y-[-40%] absolute">
            <div class="aanimate__animated animate__delay-3s animate__fadeInUp animate__slow">
                <p class="text-5xl mb-5 text-white font-bold drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.8)]">Selamat Datang di</p>
                <h1 class="text-8xl font-extrabold font-outline-4 text-purple-700 drop-shadow-xl">Youth Muslim Festival</h1>
            </div>
        </div>

    </div>
    @vite('resources/js/scanner.js')
    <script>
        const animateCSS = (element, animation, prefix = 'animate__') =>
            new Promise((resolve, reject) => {
                const animationName = `${prefix}${animation}`;
                const node = document.querySelector(element);
                node.classList.add(`${prefix}animated`, animationName);
                function handleAnimationEnd(event) {
                event.stopPropagation();
                node.classList.remove(`${prefix}animated`, animationName);
                resolve('Animation ended');
            }
            node.addEventListener('animationend', handleAnimationEnd, {once: true});
        });

        animateCSS('#logo', 'fadeInUp').then(() => {
            document.querySelector('#logo').classList.remove('animate__delay-2s')
            document.querySelector('#logo').classList.add('animate__pulse', 'animate__animated', 'animate__infinite', 'animate__slow')
        })

    </script>
</body>
</html>