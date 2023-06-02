<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Scanner - Youth Muslim Festival</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    @vite('resources/css/app.css')
    <style>
        .select2-container--open {
            z-index: 99999999999999;
        }
    </style>
</head>
<body>
    <div class=" flex w-screen h-screen overflow-hidden bg-purple-700 p-2">
        
        <div class=" text-center m-auto bg-white px-6 py-3 rounded-xl w-[600px]">
            <form id="gas" class="">
                <h1 class=" text-xl">Pilih Kegiatan :</h1>
                <select name="event" class=" my-3 block w-full py-3 px-3 border-2 border-purple-700 rounded-lg text-purple-700">
                    @foreach($data as $pilihan)
                        <option value="{{ $pilihan->id }}">{{ $pilihan->name }}</option>
                    @endforeach
                </select>
                <button class=" w-full py-2 bg-purple-700 rounded-lg text-white">Gaskeun!!!</button>
            </form>

            <div class=" hidden text-center" id="badan">
                <div class=" relative mx-auto rounded-xl overflow-hidden border-2 border-purple-700 min-h-[200px]">
                    <video class="hidden"></video>
                    <div id="hidden-camera" class=" hidden w-full h-full absolute top-0 bg-purple-900/80 left-0 backdrop-blur-md">
                        <span class=" m-auto text-white"><i>Dijeda</i></span>
                    </div>
                </div>
                <button id="manual" class="w-full py-2 bg-purple-700 rounded-lg text-white block my-2">Pilih Manual</button>
                <button id="pause" class=" w-full py-2 bg-purple-700 rounded-lg text-white block my-2">Stop Kamera</button>
                <button id="flash" class=" w-full py-2 bg-purple-700 rounded-lg text-white block my-2">Senter</button>
                <button id="ganti-event" class="w-full py-2 bg-purple-700 rounded-lg text-white block my-2">Ganti Event</button>
            </div>
        </div>

    </div>

    @vite('resources/js/scanner.js')
    <script>
    </script>
</body>
</html>