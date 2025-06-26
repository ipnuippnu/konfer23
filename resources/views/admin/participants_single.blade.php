@extends('admin._template')
@section('title', 'Pimpinan')

@section('content')
<style>
    table.info td{
        padding: 5px
    }
</style>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Pimpinan</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin./') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.participants.index') }}">Peserta</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-2 pb-2 fw-bold">Peserta</h4>
                    <form class="" id="formulir">
                        <table class="info mb-4 ml-5">
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td>{{ $participant->name }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>:</td>
                                <td>{{ $participant->gender }}</td>
                            </tr>
                            <tr>
                                <td>Asal Delegasi</td>
                                <td>:</td>
                                <td>{{ $delegator->name }}</td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>:</td>
                                <td>{{ $kecamatan['name'] }}</td>
                            </tr>
                            <tr>
                                <td>S. MAKESTA</td>
                                <td>:</td>
                                <td>
                                    <button onclick="showPdf('{{ $sertifikat_makesta }}')" href="" class="btn btn-primary btn-sm">Lihat</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Tempat</td>
                                <td>:</td>
                                <td>
                                    <input type="text" class="form-control" value="{{ data_get($saedo, 'training.location') ?? '' }}" id="tempat_makesta" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal MAKESTA</td>
                                <td>:</td>
                                <td>
                                    <input type="date" class="form-control" value="{{ data_get($saedo, 'training.executed_at') ?? '' }}" id="tanggal_makesta" required>
                                </td>
                            </tr>
                            <tr>
                                <td>No Surat</td>
                                <td>:</td>
                                <td>
                                    <input type="text" class="form-control" value="" id="no_surat" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Ganti Foto</td>
                                <td>:</td>
                                <td>
                                    <div class="d-flex">
                                        <img id="preview-foto" src="{{ $participant->foto_resmi ? \Storage::url($participant->foto_resmi) : '../assets/img/no-image.png' }}" class="img-thumbnail" style="max-height: 50px; max-width: 50px" alt="Foto Resmi">
                                        <input type="file" class="form-control" id="input-foto" accept="image/*" onchange="document.getElementById('preview-foto').src = window.URL.createObjectURL(this.files[0])">
                                        <button type="button" class="btn btn-sm btn-secondary" id="reset-foto">Reset</button>

                                    </div>
                                </td>
                            </tr>

                        </table>

                        <div class="d-flex justify-content-end mt-4">
                            {{-- <button class="btn btn-danger ml-2" onclick="tolak()">Tolak</button> --}}
                            <button class="btn btn-success ml-2" type="submit">Terima</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card preview-berkas">
                <div class="card-header" style="background-image: url('../assets/img/blogpost.jpg')">
                    <div class="profile-picture">
                        Preview Berkas
                    </div>
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('footer')
<link rel="stylesheet" href="{{ asset('assets/js/plugin/cropper/cropper.min.css') }}">
<script src="https://rawcdn.githack.com/nextapps-de/spotlight/0.7.8/dist/spotlight.bundle.js"></script>
<script src="{{ asset('assets/js/plugin/cropper/cropper.min.js') }}"></script>

<script>

    $('#formulir').submit(function(e) {
        e.preventDefault();
        terima();
    })

    $('#reset-foto').click(function() {
        document.getElementById('input-foto').value = '';
        document.getElementById('preview-foto').src = '{{ $participant->foto_resmi ? \Storage::url($participant->foto_resmi) : '../assets/img/no-image.png' }}';
    })

    $(document).ready(function() {
        showPdf('{{ $sertifikat_makesta }}', 'Sertifikat MAKESTA')
    });

    function showPdf(url, title){

        if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            // mobile
            if(url.match(/\.(jpe?g|png|gif|bmp)$/i)){
                Swal.fire({
                    title: title,
                    html: `<a class="spotlight" href="${url}" data-control="autofit,page,fullscreen"><img src="${url}" style="width: 100%; height: 600px; object-fit: cover"></a>`,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    width: '800px'
                })
            }else{
                Swal.fire({
                    title: title,
                    html: `<embed src="${url}" style="width: 100%; height: 600px; object-fit: cover">`,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    width: '800px'
                })
            }
        }else{
            // desktop
            if(url.match(/\.(jpe?g|png|gif|bmp)$/i)){
                $('.preview-berkas .card-body').html(`<a class="spotlight" href="${url}"><img src="${url}" style="width: 100%; height: 600px; object-fit: cover"></a>`);
            }else{
                $('.preview-berkas .card-body').html(`<embed src="${url}" style="width: 100%; min-height: 600px; " type="application/pdf">`);
            }

            return;
        }

    }
    let aksi = myData => axios.put('{{ route('admin.participants.update', $delegator->id) }}', myData).then(e => {
        if(e.status === 204)
        {
            Swal.fire({
                icon: 'success',
                text: e.data.message ?? 'Data Berhasil Disimpan!'
            }).then(() => location.href = '{{ route('admin.delegators.index') }}')
        }
        else
        {
            Swal.fire({
                icon: 'warning',
                text: e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.'
            })
        }

        window.close()

    })

    function terima()
    {
        Swal.fire({
            title: 'Pas Foto',
            confirmButtonText: 'Terima!',
            cancelButtonText: 'Batalkan',
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            html: "<div id='profile-container'><img id='img-foto' class='img-fluid'></div>",
            didOpen(){
                let image = document.querySelector('#img-foto')
                const imageSource = document.querySelector('#input-foto').files[0] ? URL.createObjectURL(document.querySelector('#input-foto').files[0]) : "{{ \Storage::url($participant->foto_resmi ?? '') }}";
                image.src = imageSource
                cropper = new Cropper(image, {
                    aspectRatio: 3 / 4,
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
                data.append('foto_resmi', foto);
                data.append('_method', 'put');
                data.append('action', 'accept');
                data.append('tempat_makesta', $('#tempat_makesta').val());
                data.append('tanggal_makesta', $('#tanggal_makesta').val());
                data.append('no_surat', $('#no_surat').val());

                return axios.postForm('{{ route('admin.participants.update', $participant->id) }}', data).then(() => {
                    window.close()
                })
            }
        })
        .then(result => {
            console.log(result)
            // if(result.isConfirmed){
            //     return aksi({'action': 'accept', @if($delegator->banom == 'ippnu') 'participants': $('#peserta').serializeArray(), @endif 'status_s  p': $('#status_sp').val()});
            // }
        })
    }

    // function tolak()
    // {
    //     Swal.fire({
    //         title: 'Tolak Data',
    //         text: 'Berikan alasan penolakan anda terhadap data yang diberikan',
    //         input: 'text',
    //         cancelButtonText: 'Batalkan',
    //         showCancelButton: true,
    //         preConfirm(val){
    //             return aksi({
    //                 'action': 'reject',
    //                 'reason': val,
    //                 'status': $('#peserta').serializeArray()
    //             })
    //         }
    //     })
    // }
</script>
@endpush