@extends('_template')

@section('title', 'Beranda')
@section('content')
<div class="mt-2 mb-4">
    <h2 class="text-white pb-2">Assalamu'alaikum Wr. Wb. {{ \Sso::credential()->gender === 'Laki-laki' ? 'rekan' : 'rekanita' }}!</h2>
    <h5 class="text-white op-7 mb-4">Laman ini merupakan portal pendaftaran kegiatan Konferensi Cabang (KONFERCAB) PC IPNU-IPPNU Trenggalek masa khidmat 2021-2023.</h5>
</div>
<div class="row">
    <div class="col-md-8">
        
        @if($step == \DelegatorStep::$LUNAS)
        <div class="card card-primary bg-success-gradient">
        @elseif($step == \DelegatorStep::$DITOLAK && $delegator->attempt > 3)
        <div class="card card-danger bg-danger-gradient">
        @elseif(in_array($step, [\DelegatorStep::$DITERIMA, \DelegatorStep::$DITOLAK, null], FALSE))
        <div class="card card-secondary bg-secondary-gradient">
        @else
        <div class="card border border-primary text-light">
        @endif
        
            <div class="card-body">
                <h3 class="b-b1 pb-3 mt-1 mb-3 fw-bold"><i class="fas fa-star mr-2"></i> Pendaftaran Konferensi</h3>

                <div class="p-2">

                    @if($step == \DelegatorStep::$LUNAS)
                    <div class="text-center">
                        <div class="display-2 mb-3"><i class="fas fa-check"></i></div>
                        <p class="h4">Pendaftaran berhasil. <br> Silahkan unduh bukti pendaftaran dibawah untuk dilakukan check-in pada hari pelaksanaan.</p>
                        <button class="mt-2 btn btn-white btn-lg font-weight-bold" id="daftar"><i class="fas fa-download mr-2"></i> Unduh Bukti Pendaftaran</button>
                    </div>
                    @elseif($step == \DelegatorStep::$DITOLAK)
                        @if($delegator->attempt > 3)
                        <div class="text-center">
                            <div class="display-2 mb-3"><i class="fas fa-times"></i></div>
                            <p class="h4">Berkas ditolak. <br> Anda diblokir pada pendaftaran ini. Silahkan menghubungi panitia.</p>
                            <a href="{{ route('daftar') }}" class="mt-2 btn btn-white btn-lg font-weight-bold" id="daftar">Lihat Ringkasan Pendaftaran</a>
                        </div>
                        @else
                        <div class="text-center">
                            <div class="display-2 mb-3"><i class="fas fa-exclamation-triangle"></i></div>
                            <p class="h4">Berkas ditolak. <br> Anda memiliki {{ (4 - $delegator->attempt) }}x kesempatan untuk memperbaiki berkas.</p>
                            <a href="{{ route('daftar') }}" class="mt-2 btn btn-white btn-lg font-weight-bold" id="daftar">Perbaiki Data</a>
                        </div>
                        @endif  
                    @elseif($step == \DelegatorStep::$DIAJUKAN)
                    <div class="text-center">
                        <div class="display-2 mb-3"><i class="fas fa-clock"></i></div>
                        <p class="h4">Berkas sedang diverifikasi oleh panitia. <br> Kami akan memperbaharui status verifikasi melalui laman ini.</p>
                        <a href="{{ route('daftar') }}" class="mt-2 btn btn-white btn-lg font-weight-bold" id="daftar">Lihat Ringkasan Pendaftaran</a>
                    </div>
                    @elseif($step == \DelegatorStep::$DIBAYAR)
                    <div class="text-center">
                        <div class="display-2 mb-3"><i class="fas fa-money-check-alt"></i></div>
                        <p class="h4">Pembayaran sedang diverifikasi oleh panitia. <br> Kami akan memperbaharui status verifikasi melalui laman ini.</p>
                        <a href="{{ route('bayar') }}" class="mt-2 btn btn-white btn-lg font-weight-bold">Lihat Rincian Pembayaran</a>
                    </div>
                    @elseif($step == \DelegatorStep::$DITERIMA)
                    <div class="text-center">
                        <div class="display-2 mb-3"><i class="fas fa-play"></i></div>
                        <p class="h4">Berkas Diterima. <br> Silahkan melanjutkan ke tahap pembayaran.</p>
                        <div class="text-center">
                            <a href="{{ route('bayar') }}" class="mt-2 btn btn-white btn-lg font-weight-bold mx-1" id="daftar">Lanjut ke Pembayaran</a>

                        </div>
                    </div>
                    @else
                    <div class="text-center">
                        <p class="h4">Anda belum mendaftarkan peserta!</p>
                        <a href="{{ route('daftar') }}" class="btn btn-white btn-lg font-weight-bold"">Daftar Sekarang</a>
                    </div>
                    @endif
                                               
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-calendar mr-2"></i> Timeline Kegiatan</div>
            </div>
            <div class="card-body">
                <ol class="activity-feed">
                    <li class="feed-item feed-item-secondary">
                        <time class="date" datetime="4-7">7 April 2023</time>
                        <span class="text badge badge-secondary">Rapat Pimpinan Cabang</span>
                    </li>
                    <li class="feed-item feed-item-primary">
                        <time class="date" datetime="4-10">10 April - 10 Mei 2023</time>
                        <span class="text badge badge-primary">Pendaftaran Peserta Konferensi</span>
                    </li>
                    <li class="feed-item feed-item-danger">
                        <time class="date" datetime="4-10">10 April - 10 Mei 2023</time>
                        <span class="text badge badge-danger">Validasi Berkas Peserta oleh Panitia</span>
                    </li>
                    <li class="feed-item feed-item-success pb-0">
                        <time class="date" datetime="6-4">2 - 4 Juni 2023</time>
                        <span class="text badge badge-success">Pelaksanaan Konferensi Cabang</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection