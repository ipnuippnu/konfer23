@extends('_template')

@section('title', 'Pendaftaran')

@section('content')
<div class="page-header">
    <h4 class="page-title">Pendaftaran</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('/') }}">
                <i class="flaticon-home mr-2"></i>
                Beranda
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item text-muted">
            Pendaftaran
        </li>
    </ul>
</div>

@if(!$allow_edit)
<div class="row mb-3">
    <div class="col">
        <div class="alert alert-info text-dark"><b class="h4 font-weight-bold mb-2 d-block"><i class="fas fa-info"></i> INFORMASI! </b>
            Ini adalah halaman preview untuk pendaftaran peserta. Anda hanya diperbolehkan mengubah data ini jika Panitia memberikan intruksi untuk perbaikan data.</div>
    </div>
</div>
@elseif($step == \DelegatorStep::$DITOLAK)
<div class="row mb-3">
    <div class="col">
        <div class="alert alert-warning text-dark"><b class="h4 font-weight-bold mb-2 d-block"><i class="fas fa-info"></i> PESAN DARI PANITIA: </b> &nbsp;
                <span class="h5">{{ $delegator->step->keterangan }}</span>
        </div>
    </div>
</div>
@endif

<form enctype="multipart/form-data" method="post" id="daftar">
    <div class="row">
        @for($i = 0; $i < 2; $i++)
            <div class="col-md-6 mumet">
            <div class="card card-sm">
                <div class="card-header py-2">
                    <h4 class="card-title">
                        <i class="fas fa-user mr-2"></i> Peserta <b>#{{ ($i + 1) }}</b>
                    </h4>
                </div>
                <div class="card-body">

                    @if($allow_edit)
                    
                    <div class="row mb-3">
                        <div class="col">
                            <label class="mb-2">Pilih Peserta</label>
                            <select class="select2" name="data[{{ $i }}][user]" style="width: 100%;">
                                <!-- @foreach($users as $user)
                                <option value="{{$user['ulid']}}">{{$user['name']}}</option>
                                @endforeach -->
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info text-dark belum-diisi">
                        Peserta tidak ada? <br /> Silahkan mendaftar dulu di EDOv2 : <a target="_blank" href="https://v2.pelajartrenggalek.or.id/register">Buat Akun</a>
                    </div>

                    @else

                    <div class="row mb-3">
                        <div class="col">
                            <label class="mb-2">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-sm" name="data[{{ $i }}][name]" {{ $allow_edit ?: 'disabled' }} value="{{ $delegator?->participants[$i]->name ?? '' }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="mb-2">Tempat Lahir</label>
                            <input type="text" class="form-control form-control-sm" name="data[{{ $i }}][born_place]" {{ $allow_edit ?: 'disabled' }} value="{{ $delegator?->participants[$i]->born_place ?? '' }}">
                        </div>
                        <div class="col">
                            <label class="mb-2">Tgl. Lahir</label>
                            <input type="date" class="form-control form-control-sm" name="data[{{ $i }}][born_date]" {{ $allow_edit ?: 'disabled' }} value="{{ $delegator?->participants[$i]->born_date ?? '' }}">
                        </div>
                    </div>
                    
                    @if(optional($delegator?->participants[$i] ?? [])->sertifikat_makesta)
                    <a target="_blank" href="{{ \Storage::url($delegator?->participants[$i]->sertifikat_makesta) }}"" class=" btn btn-sm btn-block btn-secondary"><i class="fas fa-download mr-1"></i> Lihat Sertifikat MAKESTA</a>
                    @endif

                    @if(optional($delegator?->participants[$i] ?? [])->foto_resmi)
                    <a target="_blank" href="{{ \Storage::url($delegator?->participants[$i]->foto_resmi) }}"" class=" btn btn-sm btn-block btn-secondary"><i class="fas fa-download mr-1"></i> Lihat Foto Resmi</a>
                    @endif

                    @endif

                    <div class="sudah-diisi d-none">

                        <div class="row jabatan">
                            <div class="col">
                                <label class="mb-2">Jabatan</label>

                                @if($allow_edit)
                                <select class="form-control form-control-sm" name="data[{{ $i }}][jabatan]">
                                    <option value="">Silahkan Pilih...</option>
                                    <option {{ ($delegator?->participants[$i]->jabatan ?? '') === 'ketua' ? 'selected' : '' }} value="ketua">Ketua</option>
                                    <option {{ ($delegator?->participants[$i]->jabatan ?? '') === 'sekretaris' ? 'selected' : '' }}value="sekretaris">Sekretaris</option>
                                    <option {{ ($delegator?->participants[$i]->jabatan ?? '') === 'bendahara' ? 'selected' : '' }}value="bendahara">Bendahara</option>
                                    <option {{ ($delegator?->participants[$i]->jabatan ?? '') === 'anggota' ? 'selected' : '' }}value="anggota">Anggota</option>
                                </select>
                                @else
                                <input type="text" class="form-control form-control-sm" {{ $allow_edit ?: 'disabled' }} value="{{ $delegator?->participants[$i]->jabatan ?? '' }}">
                                @endif

                                <small class="form-text text-muted">Jika anda mewakili pengurus, silahkan pilih jabatan yang anda wakilkan.</small>
                            </div>
                        </div>

                        <div class="makesta d-none mt-4">
                            <div class="alert alert-warning text-dark mt-3 d-none"></div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="mb-2">Sertifikat MAKESTA</label>
                                    @if($allow_edit)
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="data[{{ $i }}][sertifikat_makesta]" accept="application/pdf">
                                        <label class="custom-file-label bg-dark border-secondary" for="customFile">Pilih berkas (PDF)</label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="mb-2">
                                        Foto 3x4 Background Merah, Baju PDH / Berjas / Batik
                                    </label>
                                    @if($allow_edit)
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="data[{{ $i }}][foto_resmi]" accept="image/*">
                                        <label class="custom-file-label bg-dark border-secondary" for="customFile">Pilih berkas (Gambar)</label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- @if($i > 0)
                        <button class="hapus-peserta btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    @endif -->

                </div>
            </div>
    </div>
    @endfor
    @csrf

    <div class="col-md-6">
        <div class="card border-primary" style="border-top-width: 2px !important; border-style: solid !important">
            <div class="card-header py-2">
                <h4 class="card-title"><i class="fas fa-file-alt mr-2"></i> Persyaratan Tambahan</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="mb-2">Surat Tugas</label>
                        @if($allow_edit)
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="surat_tugas" accept="application/pdf" required>
                            <label class="custom-file-label bg-dark border-secondary" for="customFile">Pilih berkas (PDF)</label>
                        </div>
                        @else
                        <a target="_blank" href="{{ $delegator?->surat_tugas ?? '#' }}" class="btn btn-sm btn-block btn-secondary"><i class="fas fa-download mr-1"></i> Lihat Surat Tugas</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="mb-2">Surat Pengesahan</label>
                        @if($allow_edit)
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="surat_pengesahan" accept="application/pdf" required>
                            <label class="custom-file-label bg-dark border-secondary" for="customFile">Pilih berkas (PDF)</label>
                        </div>
                        @else
                        <a target="_blank" href="{{ $delegator?->surat_pengesahan ?? '#' }}"" class=" btn btn-sm btn-block btn-secondary"><i class="fas fa-download mr-1"></i> Lihat Surat Pengesahan</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="mb-2">Kontak WhatsApp yang Bisa Dihubungi</label>
                        <input type="text" class="form-control form-control-sm" name="phone" {{ $allow_edit ?: 'disabled' }} value="{{ $delegator?->whatsapp }}" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($allow_edit)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col my-auto">
                        <p class="h1 m-0">Aksi Pendaftaran</p>
                        <p>Data akan disimpan secara permanen. Pastikan data yang anda masukkan sudah benar.</p>
                    </div>
                    <div class="col-md-auto my-auto">
                        <a href="{{ route('/') }}" class="btn btn-secondary btn-lg mr-2">KEMBALI</a>
                        <button class="btn btn-success btn-lg ml-2">KIRIM</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div>
</form>

@endsection

@push('footer')
<style>
    .custom-file label {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        padding-right: 80px;
    }

    body[data-background-color="dark"] input[disabled].form-control {
        background: unset !important;
    }
</style>

@if($allow_edit)
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const users = @json($users);

    ($ => {

        $('.hapus-peserta').click(function(e){
            e.target.closest('.mumet').remove()
        })

        $('.select2').select2({
            theme: 'bootstrap-5',
            data: [{
                    id: '-',
                    text: '-'
                },
                ...Object.entries(users).map((val) => ({
                    id: val[1].ulid,
                    text: val[1].name,
                    ...val[1]
                }))
            ]
        });

        $('.select2').on('select2:select', function(e) {
            const makesta = e.target.closest('.mumet').querySelector('.makesta')
            const data = e.params.data

            const container = e.target.closest('.mumet').querySelector('.sudah-diisi')
            const containerAlert = e.target.closest('.mumet').querySelector('.belum-diisi')

            if(data.id == '-') {
                container.classList.add('d-none');
                containerAlert.classList.remove('d-none');
                return
            }

            container.classList.remove('d-none');
            containerAlert.classList.add('d-none');


            if (data.gender == 'P') {
                if (data.training == null) {
                    makesta.querySelector('.alert').innerHTML = "Anda belum menambahkan MAKESTA pada EDOv2"
                    makesta.classList.remove('d-none')
                    return
                }

                if (data.training.user_certificate == null) {
                    makesta.querySelector('.alert').innerHTML = "Sertifikat MAKESTA belum diupload pada EDOv2"
                    makesta.classList.remove('d-none')
                    return
                }
            }

            makesta.classList.add('d-none')
        })

        $('.custom-file input[type=file]').change(function(e) {
            $(e.target).parent().find('label').html(e.target.files[0].name)
        })

        $('form#daftar').submit(function(e) {
            e.preventDefault()

            let gaskeun = () => Swal.fire({
                title: "Anda yakin?",
                text: 'Data akan disimpan secara permanen dan tidak dapat diubah.',
                icon: "question",
                showCancelButton: true,
                closeOnCancel: false,
                confirmButtonText: "Kirim",
                cancelButtonText: "Kembali",
                preConfirm: () => axios.postForm("{{ route('daftar') }}", new FormData(e.target))
                    .then(e => {
                        if (e.status === 200 && e.data.status === true) {
                            Swal.fire('Berhasil!', 'Data anda berhasil disimpan.', 'success').then(() => location.href = "/")
                        } else {
                            Swal.fire('Astaghfirullah!', e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.', 'error')
                        }
                    })
            });

            //Cek apakah ada data kosong
            if ($('form#daftar input, form#daftar select').filter(function() {
                    return this.value == ""
                }).length > 0) {
                Swal.fire({
                    title: "Data Belum Lengkap!",
                    text: 'Beberapa entitas yang anda masukkan belum lengkap. Yakin ingin melanjutkan?',
                    icon: "warning",
                    showCancelButton: true,
                    closeOnCancel: false,
                    confirmButtonText: "Lanjutkan",
                    cancelButtonText: "Kembali",
                }).then(function(data) {
                    if (data.isConfirmed) gaskeun();
                });
            } else {
                gaskeun();
            }
        })

    })(jQuery);
</script>
@endif
@endpush