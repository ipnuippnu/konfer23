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
            Anda anda dapat menghubungi kontak dibawah ini:
            <div class="table-responsive">

                <table class="table mt-2" style="width: auto">
                    <tr>
                        <td>Rekan Andhika</td>
                        <td>(Ketua Pelaksana):</td>
                        <td><a href="https://wa.me/6281230627875">+62 812-3062-7875</a></td>
                    </tr>
                    <tr>
                        <td>Rekanita Zulfa</td>
                        <td>(Sekretariat):</td>
                        <td><a href="https://wa.me/6281217105916">+62 812-1710-5916</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection