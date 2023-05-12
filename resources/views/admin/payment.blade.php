@extends('admin._template')
@section('title', 'Pembayaran')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Pembayaran</h4>
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
                <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Daftar Status Pembayaran</h4>
                        <a href="{{ route('admin.delegators.recap') }}" class="btn btn-success btn-round ml-auto">
                            <i class="fa fa-download mr-1"></i>
                            Unduh Data Pimpinan (.xlsx)
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="add-row" class="display table table-stripped" >
                            <thead>
                                <tr>
                                    <th style="width: 10%">Status</th>
                                    <th>Koordinator</th>
                                    <th>Anggota</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Status</th>
                                    <th>Koordinator</th>
                                    <th>Anggota</th>
                                    <th>Jumlah Peserta</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer')
	<script src="{{ asset('js/plugin/datatables/datatables.min.js') }}"></script>
    <script>
        $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            columns: [
                { data: 'accepted_at', render (data, type, row){
                    let color = 'warning';
                    if(data != null) color = 'success';

                    return `<span class="badge ${color ? `badge-${color}` : ''}">${data == null ? `Perlu Verif.` : 'Selesai'}</span>`
                } },
                { data: 'owner.name'},
                { data: 'owner.delegators.name', orderable: false, searchable: false, render(data, type, row){
                    return (row.delegators.reduce((a, v) => {
                        let current = v.id == row.owner.id ? null : v.name ;
                        
                        return a === false ? (current ?? '') : (a + (current ? (', ' + current) : ''))

                    } , false))
                }},
                { data: 'participants_count'},
                { data: 'amount', orderable: false, searchable: false, render: val => `Rp. ${val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")},-`},
                { searchable:false, orderable:false, render (a, b, c) {
                    let data = `<a href="//wa.me/${c.owner.whatsapp}" target="_blank" class="btn btn-success btn-sm mx-1"><i class="flaticon-whatsapp"></i></a>`;

                    if(c.accepted_at == null){
                        data += '<button class="btn btn-primary btn-sm cek-berkas">Validasi</button>';
                    }

                    return `<div class="d-flex">${data}</div>`;
                }},
            ],
            
            createdRow(row, data, dataIndex){
                $(row).find('.cek-berkas').click(function(){
                    Swal.fire({
                        title: 'Validasi Pembayaran',
                        html: `Pastikan antara bukti pendaftaran dan jumlah tagihan sudah sesuai! Pastikan juga pembayaran sudah masuk di rekening Panitia (BRI: Alfiah Yunia Pratama)!`,
                        confirmButtonText: 'Sesuai, Verifikasi!',
                        denyButtonText: 'Tidak Sesuai, Perbaiki!',
                        showDenyButton: true
                    }).then(result => {
                        let aksi = myData => axios.postForm('{{ route('admin.payments.store') }}/' + data.id, myData).then(e => {
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

                        if(result.isConfirmed){
                            let data = new FormData();
                            data.append('_method', 'put')
                            data.append('action', 'accept');
                            return aksi(data);
                        }
                        else if(result.isDenied){
                            Swal.fire({
                                title: 'Perbaiki Data',
                                text: 'Silahkan upload bukti pembayaran yang valid',
                                input: 'file',
                                inputAttributes: {
                                    'accept': 'application/pdf,image/*'
                                },
                                preConfirm(val){
                                    let data = new FormData();
                                    data.append('_method', 'put')
                                    data.append('action', 'reject');
                                    data.append('file', val)
                                    return aksi(data)
                                }
                            })
                        }
                    });
                });
            }
        });
    </script>
@endpush