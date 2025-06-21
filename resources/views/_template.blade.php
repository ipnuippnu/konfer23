<!DOCTYPE html>
<html lang="id">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>@yield('title', 'Tidak Ditemukan') - {{ config('app.name') }}</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

	<!-- Fonts and icons -->
	<script src="{{ asset("assets/js/plugin/webfont/webfont.min.js") }}"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset("assets/css/fonts.min.css") }}']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="{{ asset("assets/css/bootstrap.min.css") }}">
	<link rel="stylesheet" href="{{ asset("assets/css/atlantis.min.css") }}">

</head>
<body data-background-color="dark">
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="dark2">
				
				<a href="/" class="logo">
					<img src="{{ asset("assets/img/konferab_logo_white.webp") }}" alt="navbar brand" class="navbar-brand" height="36">
					<span class="align-middle font-weight-bold text-white">KONFERCAB</span>
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
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="dark">
				
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
								{{-- <li>
									<div class="message-notif-scroll scrollbar-outer">
										<div class="notif-center">
											<a href="#">
												<div class="notif-img"> 
													<img src="{{ asset("assets/img/jm") }}_denis.jpg" alt="Img Profile">
												</div>
												<div class="notif-content">
													<span class="subject">Jimmy Denis</span>
													<span class="block">
														How are you ?
													</span>
													<span class="time">5 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-img"> 
													<img src="{{ asset("assets/img/chadengle.jpg") }}" alt="Img Profile">
												</div>
												<div class="notif-content">
													<span class="subject">Chad</span>
													<span class="block">
														Ok, Thanks !
													</span>
													<span class="time">12 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-img"> 
													<img src="{{ asset("assets/img/mlane.jpg") }}" alt="Img Profile">
												</div>
												<div class="notif-content">
													<span class="subject">Jhon Doe</span>
													<span class="block">
														Ready for the meeting today...
													</span>
													<span class="time">12 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-img"> 
													<img src="{{ asset("assets/img/talha.jpg") }}" alt="Img Profile">
												</div>
												<div class="notif-content">
													<span class="subject">Talha</span>
													<span class="block">
														Hi, Apa Kabar ?
													</span>
													<span class="time">17 minutes ago</span> 
												</div>
											</a>
										</div>
									</div>
								</li>
								<li>
									<a class="see-all" href="javascript:void(0);">See all messages<i class="fa fa-angle-right"></i> </a>
								</li> --}}
							</ul>
						</li>
						<li class="nav-item dropdown hidden-caret">
							<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-users"></i>
								<span class="notification">4</span>
							</a>
							<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
								<li>
									<div class="dropdown-title">You have 4 new notification</div>
								</li>
								<li>
									<div class="notif-scroll scrollbar-outer">
										<div class="notif-center">
											<a href="#">
												<div class="notif-icon notif-primary"> <i class="fa fa-user-plus"></i> </div>
												<div class="notif-content">
													<span class="block">
														New user registered
													</span>
													<span class="time">5 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-icon notif-success"> <i class="fa fa-comment"></i> </div>
												<div class="notif-content">
													<span class="block">
														Rahmad commented on Admin
													</span>
													<span class="time">12 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-img"> 
													<img src="{{ asset("assets/img/profile") }}2.jpg" alt="Img Profile">
												</div>
												<div class="notif-content">
													<span class="block">
														Reza send messages to you
													</span>
													<span class="time">12 minutes ago</span> 
												</div>
											</a>
											<a href="#">
												<div class="notif-icon notif-danger"> <i class="fa fa-heart"></i> </div>
												<div class="notif-content">
													<span class="block">
														Farrah liked Admin
													</span>
													<span class="time">17 minutes ago</span> 
												</div>
											</a>
										</div>
									</div>
								</li>
								<li>
									<a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i> </a>
								</li>
							</ul>
						</li>
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="{{ \Sso::credential()->avatar }}" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="{{ \Sso::credential()->avatar }}" alt="image profile" class="avatar-img rounded"></div>
											<div class="u-text">
												<h4>{{ \Sso::credential()->name }}</h4>
												<p class="text-muted">{{ \Sso::credential()->address->kecamatan->name }}</p>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="{{ config('sso.url') }}"><i class="fa fa-arrow-left"></i> &nbsp; Ke Portal EDO</a>
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
		<div class="sidebar sidebar-style-2" data-background-color="dark2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="{{ \Sso::credential()->avatar }}" alt="Foto Profil {{ \Sso::credential()->name }}" class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a href="#">
								<span>
									{{ \Sso::credential()->name }}
									<span class="user-level">{{ \Sso::credential()->address->kecamatan->name }}</span>
								</span>
							</a>
							<div class="clearfix"></div>
						</div>
					</div>
					<ul class="nav nav-primary">
						
						<li class="nav-item {{ active('/') }}">
							<a href="{{ route('/') }}">
								<i class="fas fa-home"></i>
								<p>Beranda</p>
							</a>
						</li>

						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Menu</h4>
						</li>
						
						<li class="nav-item {{ active('daftar') }}">
							<a href="{{ route('daftar') }}">
								<i class="fas fa-plus-circle"></i>
								<p>Pendaftaran</p>
							</a>
						</li>
						
						<li class="nav-item {{ active('bayar') }}">
							<a href="{{ route('bayar') }}">
								<i class="fas fa-credit-card"></i>
								<p>Pembayaran</p>
							</a>
						</li>

						{{-- <li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Layanan Bantuan</h4>
						</li> --}}
						
						<li class="nav-item {{ active('chat') }}">
							<a href="{{ route('chat') }}">
								<i class="fas fa-headset"></i>
								<p>LiveChat</p>
							</a>
						</li>

					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">

			
			<div class="content">
				<div class="page-inner">
					@yield('content')
				</div>
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link" href="https://wa.me/6285175303855">
									Butuh Bantuan
								</a>
							</li>
						</ul>
					</nav>
					<div class="copyright ml-auto">
						2025 &copy; <a href="https://pelajartrenggalek.or.id">PC IPNU-IPPNU Trenggalek</a>, Desain oleh <a href="https://www.themekita.com">ThemeKita</a>
					</div>				
				</div>
			</footer>
		</div>
		
	</div>
	<!--   Core JS Files   -->
	<script src="{{ asset("assets/js/core/jquery.3.2.1.min.js") }}"></script>
	<script src="{{ asset("assets/js/core/popper.min.js") }}"></script>
	<script src="{{ asset("assets/js/core/bootstrap.min.js") }}"></script>

	<!-- jQuery UI -->
	<script src="{{ asset("assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js") }}"></script>
	<script src="{{ asset("assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js") }}"></script>

	<!-- jQuery Scrollbar -->
	<script src="{{ asset("assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js") }}"></script>


	<!-- Chart JS -->
	<script src="{{ asset("assets/js/plugin/chart.js/chart.min.js") }}"></script>

	<!-- jQuery Sparkline -->
	<script src="{{ asset("assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js") }}"></script>

	<!-- Chart Circle -->
	<script src="{{ asset("assets/js/plugin/chart-circle/circles.min.js") }}"></script>

	<!-- Datatables -->
	<script src="{{ asset("assets/js/plugin/datatables/datatables.min.js") }}"></script>

	<!-- Bootstrap Notify -->
	<script src="{{ asset("assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js") }}"></script>

	<!-- Sweet Alert 2 -->
	<script src="{{ asset("assets/js/plugin/sweetalert2/sweetalert2.min.js") }}"></script>

	<!-- Axios -->
	<script src="{{ asset("assets/js/plugin/axios/axios.min.js") }}"></script>

	<!-- Atlantis JS -->
	<script src="{{ asset("assets/js/atlantis.min.js") }}"></script>

	<!-- MyCustomScript -->
	<script src="{{ asset("assets/js/yunyun-2.js") }}"></script>

	@stack('footer')
</body>
</html>