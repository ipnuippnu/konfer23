@extends('admin._template')

@section('title', 'Undangan')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Undangan</h4>
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
                <a href="{{ route('admin.guests.index') }}">Undangan</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title"><i class="fas fa-broadcast-tower mr-2"></i> Lakukan Siaran!</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col border-right">
                            <h5 class="font-weight-bold"><i class="fas fa-dollar-sign mr-2"></i> Pesan Siaran Pembayaran</h5>
                            <p>Kirimkan pesan untuk seluruh pimpinan yang belum melakukan pendaftaran!</p>
                            <button id="gas-pembayaran" class="btn btn-danger">Kirim Pesan Pembayaran!</button>
                        </div>
                        <div class="col">
                            <h5 class="font-weight-bold"><i class="fas fa-file mr-2"></i> Pesan Siaran Revisi</h5>
                            <p>Peringatkan seluruh pimpinan yang belum melakukan perbaikan berkas!</p>
                            <button id="gas-revisi" class="btn btn-danger">Kirim Peringatan!</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer')
<script>
    (() => {
            document.querySelector('#gas-pembayaran').addEventListener('click', function(e){
                e.preventDefault()
                Swal.fire({
                    title: "Broadcast Pembayaran?",
                    text: 'Anda yakin ingin mengirimkan pengingat kepada seluruh pimpinan yang belum melakukan pembayaran?',
                    icon: "question",
                    showCancelButton: true,
                    closeOnCancel: false,
                    confirmButtonText: "Kirim",
                    cancelButtonText: "Kembali",
                    preConfirm: () => axios.post("{{ route('admin.broadcast.unpaids') }}")
                    .then( e => {
                        if(e.status === 200 || e.status === 204 )
                        {
                            Swal.fire('Berhasil!', 'Aksi berhasil dieksekusi.', 'success').then(() => location.href = "")
                        }
                        else
                        {
                            Swal.fire('Astaghfirullah!', e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.', 'error')
                        }
                    })
                });
            })

            document.querySelector('#gas-revisi').addEventListener('click', function(e){
                e.preventDefault()
                Swal.fire({
                    title: "Broadcast Revisi Berkas?",
                    text: 'Anda yakin ingin mengirimkan pengingat kepada seluruh pimpinan yang perlu melakukan revisi?',
                    icon: "question",
                    showCancelButton: true,
                    closeOnCancel: false,
                    confirmButtonText: "Kirim",
                    cancelButtonText: "Kembali",
                    preConfirm: () => axios.post("{{ route('admin.broadcast.revisions') }}")
                    .then( e => {
                        if(e.status === 200 || e.status === 204 )
                        {
                            Swal.fire('Berhasil!', 'Aksi berhasil dieksekusi.', 'success').then(() => location.href = "")
                        }
                        else
                        {
                            Swal.fire('Astaghfirullah!', e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.', 'error')
                        }
                    })
                });
            })

    })()
</script>
@endpush