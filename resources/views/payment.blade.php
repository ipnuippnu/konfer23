@extends('_template')

@section('title', 'Pendaftaran')

@section('content')
<div class="page-header">
    <h4 class="page-title">Pembayaran</h4>
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
            Pembayaran
        </li>
    </ul>
</div>


<form enctype="multipart/form-data" method="post" id="bayar">
    <div class="row">
        @csrf
        <div class="col">
            <div class="card border-primary" style="border-top-width: 2px !important; border-style: solid !important">
                <div class="card-header py-2">
                    <h4 class="card-title"><i class="fas fa-file-alt mr-2"></i> Pembayaran Konfercab</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            @if($editable)
                            <div class="alert alert-info text-dark"><b class="h4 font-weight-bold mb-2 d-block"><i class="fas fa-info"></i> INFORMASI! </b>
                                Silahkan melakukan pembayaran sejumlah total dibawah melalui transfer pada tujuan rekening berikut:
                                <ul>
                                    <li> <span class="badge badge-primary">{{ config('konfer.rekening.brand') }}</span> a/n <b>{{ config('konfer.rekening.name') }}</b>: {{ config('konfer.rekening.no') }}</li>
                                </ul>
                                Kemudian upload bukti pembayaran pada isian yang sudah disediakan.
                            </div>
                            @else
                            <div class="alert alert-info text-dark"><b class="h4 font-weight-bold mb-2 d-block"><i class="fas fa-info"></i> INFORMASI! </b>
                                Ini adalah halaman preview untuk rincian pembayaran. Silahkan refresh halaman ini secara berkala dan pastikan nomor whatsapp anda aktif untuk mendapatkan informasi lebih lanjut.</div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pesertas">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama Pimpinan</th>
                                            <th scope="col">Delegasi</th>
                                            <th scope="col">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($payment != null)
                                        @php $current = 0; @endphp
                                        @foreach($payment->delegators as $delegator)
                                        @php $current += ($delegator->participants->count() * 60000); @endphp
                                        <tr>
                                            <td>{{ $delegator->name }}</td>
                                            <td>{{ $delegator->participants->count() }} orang</td>
                                            <td>Rp. {{ number_format(($delegator->participants->count() * 60000)) }}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right" scope="col" colspan="2">Total</th>
                                            <th scope="col" id="total-uang">Rp. {{ number_format($current) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($editable)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="mb-1">Tambah Anggota untuk Pembayaran Kolektif:</label>
                                </div>
                                <div class="col">
                                    <select class="form-control" id="members">
                                        <option value="">Pilih Anggota..</option>
                                    </select>
                                </div>
                                <div class="col-auto my-auto">
                                    <button class="btn btn-info btn-sm" id="tambah-anggota" type="button">Tambah</button>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Pastikan pimpinan yang ingin anda bayarkan sudah melewati proses verifikasi berkas.</small>
                                </div>
                            </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="mb-2">Bukti Pembayaran</label>
                                    @if($editable)
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="bukti_pembayaran" accept="application/pdf,image/*" required>
                                        <label class="custom-file-label bg-dark border-secondary" for="customFile">Pilih berkas (PDF/Gambar)</label>
                                    </div>
                                    @else
                                    <a target="_blank" href="{{ $payment->bukti_transfer }}" class="btn btn-secondary btn-sm btn-block"><i class="fas fa-eye mr-1"></i> Lihat Bukti Pembayaran</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($editable)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col my-auto">
                            <p class="h1 m-0">Aksi Pembayaran</p>
                            <p>Untuk melakukan reset pada data yang sudah anda buat, silahkan refresh halaman ini. Jika sudah yakin, tekan tombol Kirim untuk divalidasi oleh panitia.</p>
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

@push('footer') @if($editable)
<style>
    .custom-file label{
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        padding-right: 80px;
    }
</style>
<script>
    ($ => {

        let partners = {!! $partners !!};
        let currents = 0;
        let selected_partners = [];

        function print_members(data)
        {
            $('#pesertas tbody').append(`

                <tr>
                    <td>${data.name}</td>
                    <td>${data.members} orang</td>
                    <td>${toIndonesianCurrency(data.price)}</td>
                </tr>   

            `);
            
            $('#total-uang')[0].innerHTML = toIndonesianCurrency(currents += data.price)
        }

        function toIndonesianCurrency(number){
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
        }

        partners.forEach(val => {
            if(val["is_me"])
            {
                print_members(val)
                return;
            }

            let option = document.createElement('option');
            option.innerHTML = val['name']
            option.data = btoa(JSON.stringify(val))
            $("#members").append(option)
        })

        $('.custom-file input[type=file]').change(function(e){
            $(e.target).parent().find('label').html(e.target.files[0].name)
        })

        $("#tambah-anggota").click(e => {
            let data = $('#members :selected')[0].data
            if(data === undefined) return;

            try {
                data = JSON.parse(atob(data))
                $('#members :selected').remove()
                selected_partners.push(data["id"])
                $.notify({
                    icon: 'flaticon-check',
                    title: 'Berhasil!',
                    message: data["name"] + ' telah ditambahkan.',
                }, {type: 'success'})
                // Swal.fire('Informasi!', , 'info')
                print_members(data)

            } catch (error) {
                Swal.fire('Astaghfirullah!', 'Terjadi kesalahan pada browser. Mohon untuk merefresh halaman ini.', 'error');
            }
        })

        $('form#bayar').submit(function(e){
            e.preventDefault();
            let data = new FormData(e.target)
            selected_partners.forEach(o => data.append('ids[]', o))

            Swal.fire({
                title: "Anda yakin?",
                text: 'Data akan disimpan secara permanen dan tidak dapat diubah.',
                icon: "question",
                showCancelButton: true,
                closeOnCancel: false,
                confirmButtonText: "Kirim",
                cancelButtonText: "Kembali",
                preConfirm: () => axios.postForm("{{ route('bayar') }}",data)
                .then( e => {
                    if(e.status === 200 && e.data.status === true )
                    {
                        Swal.fire('Berhasil!', 'Data anda berhasil disimpan.', 'success').then(() => location.href = "/")
                    }
                    else
                    {
                        Swal.fire('Astaghfirullah!', e.data.message ?? 'Kesalahan pada sistem. Mohon hubungi admin.', 'error')
                    }
                })
            });
        })

    })(jQuery)
</script>
@endif @endpush