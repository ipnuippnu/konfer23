@extends('_template')

@section('title', 'Beranda')
@section('content')
<div class="mt-2 mb-4">
    <h2 class="text-white pb-2">Assalamu'alaikum Wr. Wb.</h2>
    <h5 class="text-white op-7 mb-4">Laman ini merupakan portal pendaftaran kegiatan Konferensi Cabang (KONFERCAB) PC IPNU-IPPNU Trenggalek masa khidmat 2021-2023.</h5>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card border border-primary text-light">
        
            <div class="card-body">
                <h3 class="b-b1 pb-3 mt-1 mb-3 fw-bold"><i class="fas fa-star mr-2"></i> Pendaftaran Ditutup!</h3>

                <div class="p-2">
                    <div class="text-center">
                        <div class="display-4 mb-3 text-danger" style="position: relative">
                            <img src="{{ asset('img/konferab_logo_white.webp') }}" alt="" width="150">
                            <i class="fas fa-times-circle" style="position:
                            absolute; text-shadow: 0 0 10px #0007;transform: translateX(-58px); bottom: 0"></i>
                        </div>
                        <p class="h4">Mohon maaf, pendaftaran peserta telah ditutup.<br />Anda tetap bisa berpartisipasi dalam kegiatan dengan selain menjadi peserta.</p>
                        {{-- <a href="//wa.me/{{ $delegator->payment->owner->whatsapp }}" class="mt-2 btn btn-white btn-lg font-weight-bold">Hubungi {{ $delegator->payment->owner->name }}</a> --}}
                    </div>
                                               
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-4">
        @include('partials.jadwal')
    </div>
</div>
@endsection