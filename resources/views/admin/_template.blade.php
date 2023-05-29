<!DOCTYPE html>
<html lang="id">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="robots" content="noindex,nofollow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Tidak Ditemukan') - Admin {{ config('app.name') }}</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

	<!-- Fonts and icons -->
	<script src="{{ asset("js/plugin/webfont/webfont.min.js") }}"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset("css/fonts.css?v2") }}', '{{ asset("css/fa.min.css") }}']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="{{ asset("css/bootstrap.min.css") }}">
	<link rel="stylesheet" href="{{ asset("css/atlantis.min.css") }}">

	@stack('header')

</head>
<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				
				<a href="{{ route('admin./') }}" class="logo">
					<img src="{{ asset("img/konferab_logo_white.webp") }}" alt="navbar brand" class="navbar-brand" height="36">
					<span class="align-middle font-weight-bold text-white">PENGELOLA</span>
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
				
				<div class="container-fluid">

					<div class="collapse" id="search-nav">
						<form class="navbar-left navbar-form nav-search mr-md-3">
							<div class="input-group">
								<div class="input-group-prepend">
									<button type="submit" class="btn btn-search pr-1">
										<i class="fa fa-search search-icon"></i>
									</button>
								</div>
								<input type="text" placeholder="Search ..." class="form-control">
							</div>
						</form>
					</div>

					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item toggle-nav-search hidden-caret">
							<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
								<i class="fa fa-search"></i>
							</a>
						</li>
						<li class="nav-item dropdown hidden-caret">
							<a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-envelope"></i>
							</a>
							<ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
								<li>
									<div class="dropdown-title d-flex justify-content-between align-items-center">
										Pesan 									
										{{-- <a href="#" class="small">Mark all as read</a> --}}
									</div>
								</li>
								<i class="d-block p-3 text-center text-sm text-muted">Tidak ada pesan</i>
							</ul>
						</li>
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="{{ auth()->user()->avatar }}" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="{{ auth()->user()->avatar }}" alt="image profile" class="avatar-img rounded"></div>
											<div class="u-text">
												<h4>{{ auth()->user()->name }}</h4>
												<p class="text-muted">{{ auth()->user()->jabatan }}</p>
												<a href="{{ route('admin.profile') }}" class="btn btn-xs btn-secondary btn-sm">Edit Profil</a>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<form method="post" action="{{ route('admin.logout') }}">
											@csrf
											<button class="dropdown-item"><i class="fa fa-arrow-left"></i> &nbsp; Keluar</button>
										</form>
										{{-- <a class="dropdown-item" href="{{ config('sso.url') }}"><i class="fa fa-arrow-left"></i> &nbsp; Ke Portal EDO</a> --}}
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>

		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="{{ auth()->user()->avatar }}" alt="Foto Profil {{ auth()->user()->name }}" class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a href="{{ route('admin.profile') }}">
								<span>
									{{ auth()->user()->name }}
									<span class="user-level">{{ auth()->user()->jabatan }}</span>
								</span>
							</a>
							<div class="clearfix"></div>
						</div>
					</div>
					<ul class="nav nav-primary">
						
						@can(AdminPermission::DASHBOARD_READ)
						<li class="nav-item {{ active('admin./') }}">
							<a href="{{ route('admin./') }}">
								<i class="fas fa-home"></i>
								<p>Dashboard</p>
							</a>
						</li>
						@endcan
						
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Pendaftaran</h4>
						</li>
						
						@can(AdminPermission::DELEGATOR_READ)
						<li class="nav-item {{ active(['admin.delegators.*']) }}">
							<a href="{{ route('admin.delegators.index') }}">
								<i class="fas fa-sitemap"></i>
								<p>Pimpinan</p>
							</a>
						</li>
						@endcan
						
						@can(AdminPermission::PARTICIPANT_READ)
						<li class="nav-item {{ active(['admin.participants.*']) }}">
							<a href="{{ route('admin.participants.index') }}">
								<i class="fas fa-user"></i>
								<p>Peserta</p>
							</a>
						</li>
						@endcan
						
						@can(AdminPermission::PAYMENT_READ)
						<li class="nav-item {{ active(['admin.payments.*']) }}">
							<a href="{{ route('admin.payments.index') }}">
								<i class="fas fa-dollar-sign"></i>
								<p>Pembayaran</p>
							</a>
						</li>
						@endcan

						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Alat</h4>
						</li>
						
						@can(AdminPermission::EVENT_READ)
						<li class="nav-item {{ active(['admin.events.*']) }}">
							<a href="{{ route('admin.events.index') }}">
								<i class="fas fa-tasks"></i>
								<p>Kegiatan</p>
							</a>
						</li>
						@endcan
						
						@can(AdminPermission::GUEST_READ)
						<li class="nav-item {{ active(['admin.guests.*']) }}">
							<a href="{{ route('admin.guests.index') }}">
								<i class="fas fa-mail-bulk"></i>
								<p>Undangan</p>
							</a>
						</li>
						@endcan
						
						@can(AdminPermission::BROADCAST_READ)
						<li class="nav-item {{ active('admin.broadcast') }}">
							<a href="{{ route('admin.broadcast') }}">
								<i class="fas fa-broadcast-tower"></i>
								<p>Broadcast</p>
							</a>
						</li>
						@endcan

					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">

			
			<div class="content">
				@yield('content')
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link" href="https://wa.me/6282264637783">
									Butuh Bantuan
								</a>
							</li>
						</ul>
					</nav>
					<div class="copyright ml-auto">
						2023 &copy; <a href="https://pelajartrenggalek.or.id">PC IPNU-IPPNU Trenggalek</a>, Desain oleh <a href="https://www.themekita.com">ThemeKita</a>
					</div>				
				</div>
			</footer>
		</div>
		
	</div>
	<!--   Core JS Files   -->
	<script src="{{ asset("js/core/jquery.3.2.1.min.js") }}"></script>
	<script src="{{ asset("js/core/popper.min.js") }}"></script>
	<script src="{{ asset("js/core/bootstrap.min.js") }}"></script>

	<!-- jQuery UI -->
	<script src="{{ asset("js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js") }}"></script>
	<script src="{{ asset("js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js") }}"></script>

	<!-- jQuery Scrollbar -->
	<script src="{{ asset("js/plugin/jquery-scrollbar/jquery.scrollbar.min.js") }}"></script>

	<!-- Bootstrap Notify -->
	<script src="{{ asset("js/plugin/bootstrap-notify/bootstrap-notify.min.js") }}"></script>

	<!-- Sweet Alert 2 -->
	<script src="{{ asset("js/plugin/sweetalert2/sweetalert2.min.js") }}"></script>

	<!-- Axios -->
	<script src="{{ asset("js/plugin/axios/axios.min.js") }}"></script>

	<!-- Atlantis JS -->
	<script src="{{ asset("js/atlantis.min.js") }}"></script>

	<!-- MyCustomScript -->
	<script src="{{ asset("js/yunyun-2.js") }}"></script>

	@stack('footer')
</body>
</html>