@extends('admin._template')
@section('title', 'Pimpinan')

@section('content')
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
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Daftar Peserta Konferensi</h4>
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
                                    <th>Nama Pimpinan</th>
                                    <th>Revisi</th>
                                    <th>Berkas</th>
                                    <th>Peserta</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Status</th>
                                    <th>Nama Pimpinan</th>
                                    <th>Revisi</th>
                                    <th>Berkas</th>
                                    <th>Peserta</th>
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
	<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script>
        var bc = new BroadcastChannel('refresh_delegators');
        
        $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            columns: [
                { data: 'step.info', searchable:false, orderable:false, render (data, type, row){
                    let color = null;
                    if(row.step.step == {{ DelegatorStep::$DIAJUKAN }}) color = 'primary';

                    else if(row.step.step == {{ DelegatorStep::$DITOLAK }}) color = 'warning';

                    else if(row.step.step == {{ DelegatorStep::$DIBAYAR }}) color = 'secondary';

                    else if(row.step.step == {{ DelegatorStep::$DIBLOKIR }}) color = 'danger';

                    return `<span class="badge ${color ? `badge-${color}` : ''}">${data}</span>`
                } },
                { data: 'name', render: (val, type, row) => {
                    return `<a href="{{ route('admin.delegators.show', '') }}/${row.id}" class="openlink mb-1 d-block">${val}</a>` + `<span class="badge">${row.address_code}</span>`
                }},
                { data: 'attempt', render: (a) =>  `${--a}x`},
                { data: 'id', searchable:false, orderable:false, render(val, type, row){
                    return `<div class="d-flex"><a target="_blank" href="${row.surat_pengesahan}" class="btn btn-sm btn-info mr-1"><abbr title="Surat Pengesahan">SP</abbr></a>` + `<a target="_blank" href="${row.surat_tugas}" class="btn btn-sm btn-secondary ml-1"><abbr title="Surat Tugas/Mandat">ST</abbr></a></div>`
                } },
                { data: 'participants_count', searchable: false, orderable: false, render: v => `${v} peserta` },
                { data: 'address_code', searchable:false, orderable:false, render (a, b, c) {
                    let data = `<a href="//wa.me/${c.whatsapp}" target="_blank" class="btn btn-success btn-sm mx-1"><i class="flaticon-whatsapp"></i></a>`;

                    if(parseInt(c.step.step) == {{ DelegatorStep::$DIAJUKAN }}){
                        data += '<button class="btn btn-primary btn-sm cek-berkas">Validasi</button>';
                    }

                    return `<div class="d-flex">${data}</div>`;
                }},
            ],
            
            createdRow(row, data, dataIndex){
                $(row).find('.openlink').click(function(e){
                    e.preventDefault();
                    var win = window.open(e.target.href, "window-2", "toolbar=yes,scrollbars=yes,resizable=yes");
                    win.addEventListener("unload", function(){
                        var page = $('#add-row').DataTable().page.info().page;
                        $('#add-row').DataTable().ajax.reload(null, false).page(page).draw(false);
                    });
                    
                });
                $(row).find('.cek-berkas').click(function(){
                    Swal.fire({
                        title: 'Validasi Berkas',
                        html: `Apakah berkas & data yang dilampirkan oleh: <div class="d-block my-2 h1 font-weight-bolder">${data.name}</div> sudah sah untuk mendaftar?`,
                        confirmButtonText: 'Sesuai, Lanjutkan!',
                        denyButtonText: 'Tidak Sesuai, Tolak!',
                        showDenyButton: true
                    }).then(result => {
                        let aksi = myData => axios.put('{{ route('admin.delegators.store') }}/' + data.id, myData).then(e => {
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
                            return aksi({'action': 'accept'});
                        }
                        else if(result.isDenied){
                            Swal.fire({
                                title: 'Tolak Data',
                                text: 'Berikan alasan penolakan anda terhadap data yang diberikan',
                                input: 'text',
                                preConfirm(val){
                                    return aksi({'action': 'reject', 'reason': val})
                                }
                            })
                        }
                    });
                });
            }
        });

        bc.onmessage = () => $('#add-row').DataTable().ajax.reload();
    </script>
@endpush