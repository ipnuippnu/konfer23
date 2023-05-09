@extends('admin._template')
@section('title', $title)

@section('content')
<div class="page-inner">
    <h4 class="page-title">{{ $title }}</h4>
    <div class="row">
        <div class="col-md-8">
            <div class="card card-with-nav">
                <div class="card-header">
                    <div class="row row-nav-line">
                        <ul class="nav nav-tabs nav-line nav-color-secondary w-100 pl-3" role="tablist">
                            <li class="nav-item submenu"> <a class="nav-link active" href="#general" data-toggle="tab">Umum</a> </li>
                            @can('change-password', $user)
                            <li class="nav-item submenu"> <a class="nav-link" href="#password-change" data-toggle="tab">Ganti Kata Sandi</a> </li>
                            @endcan
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="general">
                            <form action="" method="post" id="form-general">
                                @csrf
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Nama</label>
                                            <input type="text" class="form-control" name="name" placeholder="Nama" value="{{ $user->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Alamat Surel</label>
                                            <input type="email" class="form-control" name="email" placeholder="Email" value="{{ $user->email }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>Jenis Kelamin</label>
                                            <select class="form-control" name="gender">
                                                <option value="L" {{ $user->gender === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="P" {{ $user->gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" name="jabatan" value="{{ $user->jabatan }}" placeholder="Jabatan dalam panitia/PC">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>No. WhatsApp</label>
                                            <input type="text" class="form-control" value="+{{ $user->phone }}" name="phone" placeholder="Nomor yang bisa dihubungi">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Tentang Saya</label>
                                            <textarea class="form-control" name="bio" placeholder="Tuliskan bio anda secara singkat" rows="3">{{ $user->bio }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right mt-3 mb-3">
                                    <button class="btn btn-success">Simpan Data</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="password-change">
                            <form action="" id="form-password">
                                <div class="row mt-3 mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Sandi Lama</label>
                                            <input type="password" class="form-control" name="old-pass">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Masukkan Kata Sandi Baru</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label>Ulangi Masukkan Kata Sandi Baru</label>
                                            <input type="password" class="form-control" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right mt-3 mb-3">
                                    <button class="btn btn-danger">Ganti Kata Sandi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-profile">
                <div class="card-header" style="background-image: url('../assets/img/blogpost.jpg')">
                    <div class="profile-picture">
                        <div class="avatar avatar-xl">
                            <img src="{{ $user->avatar }}" alt="Profil {{ $user->name }}" class="avatar-img rounded-circle bg-white shadow">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-profile text-center">
                        <div class="name">{{ $user->name }}</div>
                        <div class="job">{{ $user->jabatan }}</div>
                        <div class="desc">{{ $user->bio }}</div>
                        <div class="social-media">
                            <a class="btn btn-success btn-sm btn-link" href="https://wa.me/{{ $user->phone }}" target="_blank"> 
                                <span class="btn-label just-icon"><i class="flaticon-whatsapp"></i> </span> 
                            </a>
                        </div>
                        <div class="view-profile">
                            <div class="d-none">
                                <input type="file" id="input-foto" accept="image/*">
                            </div>
                            <button class="btn btn-secondary btn-block" onclick="changePhoto()">Ubah Foto Profil</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row user-stats text-center">
                        <div class="col">
                            <div class="number">{{ $user->logs->count() }}</div>
                            <div class="title">Aktivitas</div>
                        </div>
                        <div class="col">
                            <div class="number">0</div>
                            <div class="title">Jodoh</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('header')
    <link rel="stylesheet" href="{{ asset('js/plugin/cropper/cropper.min.css') }}">
    <style>
        #profile-container{
            position: relative;
        }
        #profile-container .overlay{
            position: absolute;
            width: 100%;
            height: 100%;
            background: #0008;
            top: 0;
            left: 0;
        }
    </style>
@endpush

@push('footer')
    <script src="{{ asset('js/plugin/cropper/cropper.min.js') }}"></script>
    <script>
        let changePhoto = () => {
            document.querySelector('#input-foto').click()   
        }

        document.querySelector("#input-foto").addEventListener('change', function(e){
            let cropper = null;

            Swal.fire({
                allowOutsideClick: false,
                title: 'Ganti Foto Profil',
                showCancelButton: true,
                html: "<div id='profile-container'><img id='img-foto' class='img-fluid'></div>",
                didOpen(){
                    let url = URL.createObjectURL(e.target.files[0]);
                    let image = document.querySelector('#img-foto')
                    image.src = url
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move'
                    });
                },
                async preConfirm(){
                    $('#profile-container').append('<div class="overlay"></div>');
                    let foto = await new Promise((resolve, reject) => {
                        cropper.getCroppedCanvas().toBlob((blob) => {
                            resolve(blob);
                        });
                    });
                    let data = new FormData();
                    data.append('photo', foto);
                    return axios.postForm('{{ route('admin.change-picture') }}', data)
                }
            })
            
        })

        // AKSI KE SERVER

        let simpanAksi = (url, data) => 
            Swal.fire({
                title: '{{ _('Menyimpan Data') }}',
                didOpen(){
                    Swal.showLoading();
                    axios.postForm(url, data).then(e => {
                        if(e.status === 204)
                        {
                            Swal.fire({
                                icon: 'success',
                                text: e.data.message ?? 'Data Berhasil Disimpan!'
                            }).then(() => location.href = '')
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
            });

        document.querySelector('#form-general').addEventListener('submit', function(e){
            e.preventDefault();
            simpanAksi("{{ route('admin.profile') }}", new FormData(e.target))
        });

        document.querySelector('#form-password').addEventListener('submit', function(e){
            e.preventDefault();
            simpanAksi("{{ route('admin.change-password') }}", new FormData(e.target))
        });

    </script>
@endpush