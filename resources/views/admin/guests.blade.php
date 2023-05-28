@extends('admin._template')
@section('title', 'Pimpinan')

@push('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.2/css/select.dataTables.min.css">
@endpush

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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Daftar Tamu Undangan</h4>
                        <button data-toggle="modal" data-target="#myModal" href="{{ route('admin.delegators.recap') }}" class="btn btn-success btn-round ml-3 ">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button id="cetak-massal" disabled="disabled" class="btn btn-warning btn-round ml-3 ">
                            <i class="fa fa-download mr-2"></i> Unduh Massal
                        </button>
                        <button class="btn btn-outline-success btn-round ml-auto ">
                            VIP: {{ $vip }}
                        </button>
                        <button class="btn btn-outline-danger btn-round ml-2 ">
                            VVIP: {{ $vvip }}
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="add-row" class="display table table-stripped" >
                            <thead>
                                <tr>
                                    <th style="width: 2%"></th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Kode</th>
                                    <th>Ditambahkan Pada</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Kode</th>
                                    <th>Ditambahkan Pada</th>
                                    <th></th>
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
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Tambah Undangan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
            <form>
            <div class="form-group">
                <label>Kepada<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="name">
              </div>
              <div class="form-group">
                <label>Jenis<i class="text-danger">*</i></label>
                <select name="type" class="form-control">
                    <option value="vip">VIP</option>
                    <option value="vvip">VVIP</option>
                </select>
              </div>
              <div class="form-group">
                <label>Jabatan</label>
                <input type="text" class="form-control" name="jabatan">
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <input type="text" class="form-control" name="alamat">
              </div>
              <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control"></textarea>
              </div>
            </form>
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal" id="gas-tambah">Tambah!</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
  
      </div>
    </div>
  </div>
@endsection

@push('footer')

	<script src="{{ asset('js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/dataTables.select.min.js"></script>
    
    <script>


        const datatables = $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            order: [[3, 'desc']],
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ],
            select: {
                style:    'multi',
                selector: 'td:first-child'
            },
            columns: [
                { name: 'name', render: () => ''},
                { data: 'name', render(d, t, r){
                    return `
                    ${r.name}
                    ${r.jabatan ? '<br />' : ''} ${r.jabatan ?? ''}
                    ${r.address ? '<br />' : ''} ${r.address ?? ''}

                    `;
                }},
                { data: 'type', render: val => val.toUpperCase()},
                { data: 'code.id', orderable: false},
                { data: 'created_at'},
                {render: (val, t, data) => function(){

                    return `
                    
                    <div class="d-flex">
                    <button class="btn btn-danger btn-sm mr-1 delete"><i class="fas fa-trash"></i></button>
                    </div>

                    `

                }, searchable:false, orderable: false},
            ],
            createdRow(row, data, dataIndex){
                $(row).find('.delete').click(function(){
                    Swal.fire({
                        icon: 'question',
                        title: 'Yakin hapus data?',
                        text: `Data yang sudah dihapus tidak bisa dikembalikan!`,
                        confirmButtonText: 'Hapus!',
                        cancelButtonText: 'Batalkan',
                        showCancelButton: true,

                        allowOutsideClick: () => !Swal.isLoading(),
                        allowEscapeKey: () => !Swal.isLoading(),

                        preConfirm: () => axios.post('{{ route('admin.guests.index') }}/' + data.id, {
                            '_method': 'delete'
                        }).then(e => {
                            if(e.data.status === true)
                            {
                                Swal.close()
                                datatables.ajax.reload()
                                setTimeout(() => {
                                    $.notify({
                                        icon: 'flaticon-check',
                                        title: 'Berhasil!',
                                        message: 'Undangan berhasil dihapus.',
                                    }, {type: 'danger'})
                                }, 200);
                            }
                            else
                            {
                                Swal.fire('Gagal!', e.data.message ?? 'Kesalahan Sistem. Hubungi Admin.', 'error')
                            }
                        })

                    })
                });
            }
        });

        const editedit = (e, dt, type, indexes) => {

            if ( datatables.rows({selected: true}).count() > 6 ) {
                datatables.rows(indexes).deselect();
                $.notify({
                    icon: 'flaticon-check',
                    title: 'Tidak diizinkan!',
                    message: 'Anda hanya boleh memilih maksimal 6 undangan',
                }, {type: 'secondary'})
            }

            if(datatables.rows( { selected: true } ).count() == 0)
            {
                $('#cetak-massal').attr('disabled', 'disabled');
            }else
            {
                $('#cetak-massal').removeAttr('disabled');
            }

        }

        $('#cetak-massal').on('click', function(){
            let newArray = [];
            let data = datatables.rows( { selected: true } ).data();

            for (var i=0; i < data.length ;i++){
                newArray.push(data[i]['id']);
            }

            Swal.fire({

                text: 'Sedang memproses...',
                allowOutsideClick: () => !Swal.isLoading(),
                allowEscapeKey: () => !Swal.isLoading(),

                didOpen(){
                    Swal.showLoading();
                    axios.post('{{ route('admin.guests.download') }}', {
                        data: newArray
                    }).then(e => {
                        if(e.data.status === true)
                        {
                            location.href = e.data.link
                            Swal.close()
                            setTimeout(() => {
                                $.notify({
                                    icon: 'flaticon-check',
                                    title: 'Berhasil!',
                                    message: 'Menuju unduhan...',
                                }, {type: 'info'})
                            }, 200);
                        }
                        else
                        {
                            Swal.fire('Gagal!', e.data.message ?? 'Kesalahan Sistem. Hubungi Admin.', 'error')
                        }
                    })
                }
            })

        })

        datatables.on('select', editedit)

        datatables.on('deselect', editedit)

        $('#myModal').on('hidden.bs.modal', function (e) {
            $(this)
                .find("input,textarea,select")
                .val('')
                .end()
                .find("input[type=checkbox], input[type=radio]")
                .prop("checked", "")
                .end();
            })

        $('#gas-tambah').on('click', function(){
            const data = new FormData($('#myModal form')[0]);

            Swal.fire({
                text: 'Sedang memproses...',
                allowOutsideClick: () => !Swal.isLoading(),
                allowEscapeKey: () => !Swal.isLoading(),
                didOpen(){
                    Swal.showLoading()
                    axios.postForm('', data).then(e => {
                        if(e.data.status === true)
                        {
                            Swal.close()
                            datatables.ajax.reload()
                            setTimeout(() => {
                                $.notify({
                                    icon: 'flaticon-check',
                                    title: 'Berhasil!',
                                    message: 'Undangan ditambahkan.',
                                }, {type: 'success'})
                            }, 200);
                        }
                        else
                        {
                            Swal.fire('Gagal!', e.data.message ?? 'Kesalahan Sistem. Hubungi Admin.', 'error')
                        }
                    });
                }
            })
        });
    </script>
@endpush