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
                                    <th>#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Tempat / Tgl. Lahir</th>
                                    <th>Asal Delegasi</th>
                                    <th style="width: 10%">ID Card</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Tempat / Tgl. Lahir</th>
                                    <th>Asal Delegasi</th>
                                    <th>ID Card</th>
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
        $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            columns: [
                { searchable: false, orderable: false, render(val, type, row){
                    return `
                    
                    <div class="d-flex">
                        <a href="{{ \Storage::url('') }}${row.foto_resmi}" target="_blank" class="btn btn-primary btn-sm mr-1">Foto</a>
                ${row.sertifikat_makesta ? `<a href="{{ \Storage::url('') }}${row.sertifikat_makesta}" target="_blank" class="btn btn-secondary btn-sm mr-1">S. MAKESTA</a>` : '' }
                        

                    </div>

                    `
                }},
                { data: 'name' },
                { data: 'jabatan' },
                { data: 'born_date', render (a, b, c) {
                    return `${c.born_place} / ${c.born_date}`;
                }},
                { data: 'delegator.name', render (a, b, c) {
                    return a + ` <br /> <span class='badge'>${c.delegator.address_code}</span>`
                }},
                { searchable: false, orderable: false, render(val, type, row){
                    return `
                    
                    <div class="d-flex">
                        <a href="{{ route('admin.participants.index') }}/${row.id}?type=pdf" target="_blank" class="btn btn-primary btn-sm mr-1">PDF</a>
                        <a href="{{ route('admin.participants.index') }}/${row.id}?type=front" target="_blank" class="btn btn-success btn-sm mr-1">Depan</a>
                        <a href="{{ route('admin.participants.index') }}/${row.id}?type=back" target="_blank" class="btn btn-secondary btn-sm mr-1">Belakang</a>
                    </div>

                    `
                }},

                { data: 'delegator.address_code', searchable: true, visible: false },
                { data: 'born_place', searchable: true, visible: false }
            ],
            createdRow(row, data, dataIndex){
                if(data.jabatan == 'Ketua') $(row).find('td:eq(0)').css('text-decoration', 'underline dotted')
            }
        });
    </script>
@endpush