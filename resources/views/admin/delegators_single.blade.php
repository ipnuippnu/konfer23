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
                <a href="{{ route('admin.delegators.index') }}">Pimpinan</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.delegators.show', $delegator) }}">{{ $delegator->name }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-2 pb-2 fw-bold">Data Pimpinan</h4>
                    <table class="info mb-4">
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td>{{ $delegator->name }}</td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td>{{ $kecamatan['name'] }}</td>
                        </tr>
                        <tr>
                            <td>Surat Pengesahan</td>
                            <td>:</td>
                            <td>
                                <button onclick="showPdf('{{ $delegator->surat_pengesahan }}')" class="btn btn-primary btn-sm"><i class="fas fa-file-pdf mr-2"></i> Lihat</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Surat Tugas</td>
                            <td>:</td>
                            <td>
                                <button onclick="showPdf('{{ $delegator->surat_tugas }}')" class="btn btn-secondary btn-sm"><i class="fas fa-file-pdf mr-2"></i> Lihat</button>
                            </td>
                        </tr>
                    </table>

                    <h4 class="mt-2 pb-2 fw-bold">Peserta ({{ $delegator->participants->count() }} orang)</h4>
                    <form id="peserta">
                        <table class="table">
                            <thead>
                              <tr>
                                <th style="margin: 0 !important; padding: 0!important; text-align: center">#</th>
                                <th>Nama Lengkap</th>
                                <th>MAKESTA</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($delegator->participants as $participant)
                                    <tr>
                                        <td style="margin: 0 !important; padding: 0!important; text-align: center">
                                            <img onclick="showPdf('{{ \Storage::url($participant->foto_resmi) }}')" src="{{ \Storage::url($participant->foto_resmi) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; cursor: pointer" alt="">
                                        </td>
                                        <td>
                                            <b>{{ $participant->name }}</b>
                                            <small class="d-block text-muted">{{ $participant->jabatan }}</small>
                                        </td>
                                        <td class="d-flex">
                                            <button onclick="showPdf('{{ $participant->sertifikat_makesta }}')" class="btn btn-primary btn-sm my-auto mr-2"><i class="fas fa-file-pdf"></i></button>
                                            <select class="my-auto flex-1" style="width: 100px;" name="{{ $participant->id }}">
                                                <option value="" disabled selected>Pilih Status</option>
                                                <option value="1" {{ $participant->status == 1 ? 'selected' : '' }}>Sesuai</option>
                                                <option value="2" {{ $participant->status == 2 ? 'selected' : '' }}>Tidak Sesuai, Sudah MAKESTA</option>
                                                <option value="3" {{ $participant->status == 3 ? 'selected' : '' }}>Tidak Sesuai, Belum MAKESTA</option>
                                            </select>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                          </table>
                    </form>

                      <div class="d-flex justify-content-end mt-4">
                          <button class="btn btn-danger ml-2" onclick="tolak()">Tolak</button>
                          <button class="btn btn-success ml-2" onclick="terima()">Terima</button>
                      </div>
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
<script>
    function showPdf(url, title){

        if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            // mobile
            Swal.fire({
                title: title,
                html: `<embed src="${url}" style="width: 100%; height: 600px; object-fit: cover" type="application/pdf">`,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                width: '800px'
            })
        }else{
            // desktop
            $('.preview-berkas .card-body').html(`<embed src="${url}" style="width: 100%; min-height: 600px; object-fit: cover" type="application/pdf">`);
            return;
        }

    }
    let aksi = myData => axios.put('{{ route('admin.delegators.update', $delegator->id) }}', myData).then(e => {
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
    })

    function terima()
    {
        Swal.fire({
            title: 'Validasi Berkas',
            html: `Yakin ingin menerima pengajuan: <div class="d-block my-2 h1 font-weight-bolder">{{ $delegator->name }}</div> sudah sah untuk mendaftar?`,
            confirmButtonText: 'Terima!',
            cancelButtonText: 'Batalkan',
            showCancelButton: true
        }).then(result => {
            if(result.isConfirmed){
                return aksi({'action': 'accept', 'participants': $('#peserta').serializeArray()});
            }
        })
    }

    function tolak()
    {
        Swal.fire({
            title: 'Tolak Data',
            text: 'Berikan alasan penolakan anda terhadap data yang diberikan',
            input: 'text',
            cancelButtonText: 'Batalkan',
            showCancelButton: true,
            preConfirm(val){
                return aksi({
                    'action': 'reject',
                    'reason': val,
                    'status': $('#peserta').serializeArray()
                })
            }
        })
    }
</script>
@endpush