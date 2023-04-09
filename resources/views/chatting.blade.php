@extends('_template')

@section('title', 'Bantuan')

@section('content')
<div class="page-header">
    <h4 class="page-title">Live Chat</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('/') }}">
                <i class="flaticon-home mr-2"></i>
                Beranda
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item text-muted">
            LiveChat
        </li>
    </ul>
</div>

<div class="card">
    <div class="card-body">
        <div class="h4">
            Silahkan tekan tombol hijau di <b><u>pojok kanan bawah</u></b> untuk memulai percakapan secara langsung dengan kami. <br />Atau anda dapat menghubungi kontak dibawah ini:
            <div class="table-responsive">

                <table class="table mt-2" style="width: auto">
                    <tr>
                        <td>Rekanita Alfiyah</td>
                        <td>(Sekretariat):</td>
                        <td><a href="https://wa.me/6282264637783">+62 822-6463-7783</a></td>
                    </tr>
                    <tr>
                        <td>Rekanita Uliya</td>
                        <td>(Sistem):</td>
                        <td><a href="https://wa.me/6282293832103">+62 822-9383-2103</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer')

<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/643248be4247f20fefea9bf6/1gti6oad3';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
@endpush