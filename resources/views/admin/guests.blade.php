@extends('admin._template')
@section('title', 'Pimpinan')

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
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Daftar Tamu Undangan</h4>
                        <button data-toggle="modal" data-target="#myModal" href="{{ route('admin.delegators.recap') }}" class="btn btn-success btn-round ml-3">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="add-row" class="display table table-stripped" >
                            <thead>
                                <tr>
                                    <th style="width: 10%"></th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Kode</th>
                                    <th>Ditambahkan Pada</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Kode</th>
                                    <th>Ditambahkan Pada</th>
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
    <script>


        const datatables = $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            order: [[3, 'desc']],
            columns: [
                {render: (val, t, data) => function(){

                    return `
                    
                    <div class="d-flex">
                    <button class="btn btn-danger btn-sm mr-1 delete"><i class="fas fa-trash"></i></button>
                    </div>

                    `

                }, searchable:false, orderable: false},
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