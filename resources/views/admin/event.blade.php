@extends('admin._template')
@section('title', 'Kegiatan')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Kegiatan</h4>
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
                <a href="{{ route('admin.events.index') }}">Kegiatan</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Jenis Kegiatan</h4>
                        <button data-toggle="modal" data-target="#myModal" href="{{ route('admin.delegators.recap') }}" class="btn btn-success btn-round ml-3 ">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="add-row" class="display table table-stripped" >
                            <thead>
                                <tr>
                                    <th style="width: 10%">No.</th>
                                    <th>Nama Event</th>
                                    <th>Target Event</th>
                                    <th>Masa Aktif</th>
                                    <th>Telah Terdata</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Event</th>
                                    <th>Target Event</th>
                                    <th>Masa Aktif</th>
                                    <th>Telah Terdata</th>
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
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Tambah Kegiatan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
            <form>
              <div class="form-group">
                <label>Nama Event<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="name">
              </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Mulai<i class="text-danger">*</i></label>
                            <input type="datetime-local" class="form-control" name="start" value="{{ Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Selesai</label>
                            <input type="datetime-local" class="form-control" name="end">
                        </div>
                    </div>
              </div>
              <div class="form-group">
                  <label>Target Event<i class="text-danger">*</i></label>
                  <select name="type" class="form-control" name="target" required>
                      @foreach($types as $type)
                      <option value="{{ $type->value }}">{{ $type->value }}</option>                               
                      @endforeach
                  </select>
              </div>

              <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="2" class="form-control"></textarea>
              </div>

              <hr>

              <div class="form-group">
                <label>Parameter :</label>
                @foreach($params as $param)
                <div class="form-check">
                  <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" value="{{ $param->value }}" name="params[]">
                      <span class="form-check-sign">{{ __('params.' . $param->value) }}</span>
                  </label>
                </div>
                @endforeach
              </div>

            </form>
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal" id="gas-tambah">Tambah</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
  
      </div>
    </div>
  </div>
@endsection

@push('footer')
	<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script>
        const datatables = $('#add-row').DataTable({
            processing: true,
            serverSide: true,
            ajax: "",
            columns: [
                { name: 'created_at', data: 'DT_RowIndex' },
                { data: 'name'},
                { data: 'target_type'},
                { data: 'event_start', render(val, type, row){
                    return `<span class="badge badge-info">${val.replace(/ /, 
                        ' | ')}</span> - <span class="badge badge-warning">${row.event_end.replace(/ /, 
                        ' | ')}</span>`
                } },
                { data: 'members_count', render: (val, type, row) => `<span class="badge badge-primary">${val}</span>&nbsp; dari ${row.target_count}`},
                {render: (val, t, data) => function(){

                    return `
                    
                    <div class="d-flex">
                    <button class="btn btn-danger btn-sm mr-1 delete"><i class="fas fa-trash"></i></button>
                    <a href="{{ route('admin.events.index') }}/${data.id}" class="btn btn-success btn-sm mr-1 recap" title="Rekap">Rekap</a>
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

                        preConfirm: () => axios.post('{{ route('admin.events.index') }}/' + data.id, {
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
                                        message: 'Event berhasil dihapus.',
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
                            setTimeout(() => {
                                $.notify({
                                    icon: 'flaticon-check',
                                    title: 'Berhasil!',
                                    message: 'Undangan ditambahkan.',
                                }, {type: 'success'})
                            }, 200);
                            location.href = ""
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