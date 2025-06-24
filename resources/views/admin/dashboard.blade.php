@extends('admin._template')

@section('title', 'Dashboard')
@section('content')

<div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
        <div class="d-flex">
            <div class="mx-auto">
                <h2 class="text-white pb-2 fw-bold h1">Selayang Pandang</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="row mt--2">
        <div class="col-md-6">
            <div class="card full-height">
                <div class="card-body">
                    <div class="card-title">Statistik Pendaftaran <span class="badge badge-primary">Realtime</span></div>
                    <div class="card-category">Informasi peserta Konferensi Cabang</div>
                    <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                        <div class="px-2 pb-2 pb-md-0 text-center">
                            <div id="circles-1"></div>
                            <h6 class="fw-bold mt-3 mb-0">Jumlah Pimpinan</h6>
                        </div>
                        <div class="px-2 pb-2 pb-md-0 text-center">
                            <div id="circles-3"></div>
                            <h6 class="fw-bold mt-3 mb-0">Terverifikasi (Pimpinan)</h6>
                        </div>
                        <div class="px-2 pb-2 pb-md-0 text-center">
                            <canvas id="circles-2"></canvas>
                            <h6 class="fw-bold mt-3 mb-0">Total Peserta</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card full-height">
                <div class="card-body">
                    <div class="card-title">Statistik HTM (Data Saja)</div>
                    <div class="row py-3">
                        <div class="col-md-4 d-flex flex-column justify-content-around">
                            <div class="mb-3">
                                <h6 class="fw-bold text-uppercase text-success op-8">Sudah Bayar</h6>
                                <h3 class="fw-bold">Rp. {{ number_format($bayar['sudah'],0, ',', '.') }},-</h3>
                            </div>
                            <div class="mb-3">
                                <h6 class="fw-bold text-uppercase text-danger op-8">Belum Bayar</h6>
                                <h3 class="fw-bold">Rp. {{ number_format($bayar['belum'],0, ',', '.') }},-</h3>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="chart-container">
                                <canvas id="totalKeuangan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-card-no-pd">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Statistik Peserta per Kecamatan <span class="badge badge-danger">Update Per Menit</span></h4>
                    </div>
                    <p class="card-category">Data ini diambil dari pengelompokan peserta per kecamatan dan diupdate setiap menit</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive table-hover table-sales">
                                <table class="table">
                                    <tbody>
                                        @foreach($perkecamatan['data'] as $key => $kecamatan)
                                        <tr>
                                            <td>
                                                <div class="label" style="background: {{ $kecamatan['warna'] }}; width: 20px; height: 20px"></div>
                                            </td>
                                            <td>{{ $kecamatan['name'] }}</td>
                                            <td class="text-right">
                                                {{ $kecamatan['total'] }} peserta
                                            </td>
                                            <td class="text-right">
                                                {{ $kecamatan['persentase'] }}%
                                            </td>
                                        </tr> 
                                        @endforeach
                                        <tr>
                                            <td colspan="2" class="text-right font-weight-bold">TOTAL</td>
                                            <td class="text-right font-weight-bold">
                                                {{ $jumlah['peserta']['total'] }} peserta
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                100%
                                            </td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mapcontainer mb-2">
                                <canvas id="piePesertaKecamatan"></canvas>
                            </div>

                            <p class="text-center">Diupdate pada: <b>{{ $perkecamatan['updated_at']->format('Y-m-d H:i') }} WIB</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            @include('partials.jadwal')
        </div>
        <div class="col-md-6">
            <div class="card full-height">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Aktivitas Terakhir</div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($activities as $key => $activity)
                    <div class="d-flex">
                        <div class="avatar avatar-offline">
                            <span class="avatar-title rounded-circle border border-white bg-secondary">{{ ++$key }}</span>
                        </div>
                        <div class="flex-1 ml-3 pt-1">
                            <h6 class="text-uppercase fw-bold mb-1">{{ $activity->description }} <span class="text-success pl-3">{{ $activity->log_name }}</span></h6>
                            <span class="text-muted">{{ $activity->subject?->name ?? $activity->causer?->name }}</span>
                        </div>
                        <div class="float-right pt-1">
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="separator-dashed"></div>
                    @empty
                    <p class="text-center">Tidak ada aktivitas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer')

<!-- Chart JS -->
<script src="{{ asset("assets/js/plugin/chart.js/chart.min.js") }}"></script>

<!-- Chart Circle -->
<script src="{{ asset("assets/js/plugin/chart-circle/circles.min.js") }}"></script>

<script>

		Circles.create({
			id:'circles-1',
			radius:45,
			value:100,
			maxValue:100,
			width:7,
			text: "{{ $jumlah["pimpinan"] }}",
			colors:['#f1f1f1', '#FF9E27'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

        const totalPeserta = document.querySelector('#circles-2').getContext('2d')
        new Chart(totalPeserta, {
            type: 'pie',
            options: {legend: {display: false}},
            data: {
                labels: [
                    'IPNU',
                    'IPPNU'
                ],
                datasets: [{
                    label: 'Keuangan',
                    data: @json([$jumlah['peserta']['ipnu'], $jumlah['peserta']['ippnu']]),
                    backgroundColor: [
                        'rgb(54, 162, 54)',
                        'rgb(255, 99, 132)',
                    ],
                    hoverOffset: 4
                }]
            }
        })

		Circles.create({
			id:'circles-3',
			radius:45,
			value:{{ $jumlah['verified'] * $jumlah['pimpinan'] / 100 }},
			maxValue:100,
			width:7,
			text: "{{ $jumlah["verified"] }}",
			colors:['#f1f1f1', '#F25961'],
			duration:400,
			wrpClass:'circles-wrp',
			textClass:'circles-text',
			styleWrapper:true,
			styleText:true
		})

        const totalKeuangan = document.querySelector('#totalKeuangan').getContext('2d')
        new Chart(totalKeuangan, {
            type: 'pie',
            data: {
                labels: [
                    'Sudah',
                    'Belum'
                ],
                datasets: [{
                    label: 'Keuangan',
                    data: {{ json_encode($bayar['data']) }},
                    backgroundColor: [
                        'rgb(54, 162, 54)',
                        'rgb(255, 99, 132)',
                    ],
                    hoverOffset: 4
                }]
            }
        })

        const pesertaKecamatan = document.querySelector('#piePesertaKecamatan').getContext('2d')
        new Chart(pesertaKecamatan, {
            type: 'pie',
            options: {legend: {display: false}},
            data: {
                labels: {!! $perkecamatan['data']->pluck('name') !!},
                datasets: [{
                    label: 'Keuangan',
                    data: {!! $perkecamatan['data']->pluck('total') !!},
                    backgroundColor: {!! $perkecamatan['data']->pluck('warna') !!},
                    hoverOffset: 4
                }]
            }
        })
	
</script>
@endpush