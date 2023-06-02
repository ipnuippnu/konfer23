<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laman Mimin</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="robots" content="nofollow,noindex">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/atlantis.css') }}">
    <style>
        body::before{
            content: " ";   
            position: fixed;
            background: url("{{ asset('assets/img/konferab_logo_white.webp') }}");
            background-size: cover;
            width: 300px;
            height: 300px;
            right: 25px;
            bottom: 50px;
            opacity: .1;
            filter: blur(2px);
        }
    </style>
</head>
<body class="d-flex" style="background: #135">
    <div class="container py-3 my-auto h-100">
        <div class="card mx-auto bg-transparent" style="max-width: 450px; overflow:hidden">
            <div class="card-header bg-dark">
                <h5 class="card-title text-center font-weight-bold text-light">Portal Mimin</h5>
            </div>
            <div class="card-body bg-light">
                <form method="post">
                    @csrf
                    <div class="form-group">
                        <label for="input-email">Alamat Surel</label>
                        <input type="email" class="form-control" id="input-email" placeholder="Masukkan Email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="input-password">Kata Sandi</label>
                        <input type="password" class="form-control" id="input-password" placeholder="Masukkan Password" name="password" required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-block btn-dark">Masuk!</button>
                    </div>
                </form>
                <div class="form-group">
                    <p class="mb-0 text-center">Bukan Panitia?</p>
                    <a href="{{ route('/') }}" class="btn btn-block btn-primary">
                        <img src="{{ asset('assets/img/konferab_logo_white.webp') }}" height="24">
                        OTW Portal Peserta
                    </a>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('assets/js/plugin/axios/axios.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/yunyun-2.js') }}"></script>
    <script>

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = new FormData(this)
            Swal.fire({
                text: "Verifikasi Autentikasi...",
                didOpen(){
                    Swal.showLoading();
                    axios.postForm("{{ route('admin.login') }}", form).then( e => {
                        if(e.status === 204)
                        {
                            location.href = ""
                        }
                        else
                        {
                            Swal.fire({
                                icon: 'warning',
                                text: e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.'
                            })
                        }
                    })
                }
            })
        });

    </script>
</body>
</html>