@extends('admin._template')
@section('title', 'Peserta')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Peserta</h4>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Daftar Peserta Konferensi</h4>
                        <a href="{{ route('admin.participants.recap') }}" class="btn btn-success btn-round ml-auto">
                            <i class="fa fa-download mr-1"></i>
                            Unduh Data Peserta (.xlsx)
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="add-row" class="display table table-stripped" >
                            <thead>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Tempat / Tgl. Lahir</th>
                                    <th>Asal Delegasi</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Tempat / Tgl. Lahir</th>
                                    <th>Asal Delegasi</th>
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
                { data: 'name' },
                { data: 'jabatan' },
                { data: 'born_date', render (a, b, c) {
                    return `${c.born_place} / ${c.born_date}`;
                }},
                { data: 'delegator.name', render (a, b, c) {
                    return a + ` <br /> <span class='badge'>${c.delegator.address_code}</span>`
                }},
                { render (a, b, c) {
                    // return `<div class="form-button-action">
                    //             <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task">
                    //                 <i class="fa fa-edit"></i>
                    //             </button>
                    //             <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove">
                    //                 <i class="fa fa-times"></i>
                    //             </button>
                    //         </div>`
                    return '<i>Belum Tersedia</i>'
                }, orderable: false, searchable: false},

                { data: 'delegator.address_code', searchable: true, visible: false },
                { data: 'born_place', searchable: true, visible: false }
            ],
            createdRow(row, data, dataIndex){
                if(data.jabatan == 'Ketua') $(row).find('td:eq(0)').css('text-decoration', 'underline dotted')
            }
        });
    </script>
@endpush