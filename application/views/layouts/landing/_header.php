<nav id="navbar-outer-up" class="navbar navbar-inverse">
	<div class="container">
		<div id="nav-header-up" class="navbar-header">
			<div id="navbar-title" class="navbar-brand" href="#">
				<i class="glyphicon glyphicon-book icon-navbar-up"></i><b>INFO MAHASISWA PFIS UIN SUNAN KALIJAGA</b>
			</div>

		</div>
	</div>
</nav>
<nav id="navbar-outer-bottom" class="navbar navbar-inverse">
	<div class="container">
		<div class="navbar-header" style="width: 100%;padding-top:29px;">
			<div id="navbar-kiri" class="navbar-brand col-sm-7">
				<div class="col-sm-2 col-xs-4 gambar" style="text-align: center;">
					<img src="<?= base_url('assets/img/logo-uin.png') ?>" style="float: left;" class="img-responsive">
				</div>
				<div class="col-sm-10 col-xs-8 isi-logo" style="text-align: left;">
					<h2 style="color: #fff; font-family: myf;float: left;margin-bottom: 0px;margin-top: 0px">PENDIDIKAN FISIKA</h2>
					<h3 style="color: #fff; font-family: myf2;float: left;margin-top:5px">UIN SUNAN KALIJAGA</h3>
				</div>
			</div>
			<div id="navbar-kanan" class="navbar-brand col-sm-5 ">
				<!-- <div class="col-sm-6">
            <div class="col-sm-3" style="text-align: right;">
              <h2 class="glyphicon glyphicon-earphone" style="margin-right:5px"></h2>
            </div>
            <div class="col-sm-9" style="text-align: left;">
              <h5 class="text-contact"><b>+62-274-512474</b></h5>
              <h6 class="text-contact">pkim@uin-suka.ac.id</h6>
            </div>
          </div> -->
				<div class="col-sm-12" style="text-align: right;padding:0px">
					<!-- <p class="text-contact"><i class="glyphicon glyphicon-home" style="margin-right:5px"></i>Jl. Marsda Adisucipto Yogyakarta</p> -->
					<form action="<?= base_url('landing/search') ?>" method="GET">
						<input id="cari" type="text" name="cari" placeholder="Cari disini" style="position: relative;z-index: 999">

					</form>
				</div>
			</div>
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="collapse navbar-collapse dropdown" id="myNavbar">
				<div class="col-sm-12" id="wrap-putih">
					<ul class="nav navbar-nav navbar-left menu-nav-bottom">
						<li><a href="http://pfis.uin-suka.ac.id/"><i class="glyphicon glyphicon-home" style="margin-right:5px"></i>Home</a> </li>

						<?php
						function doOutputList($treeMenu, $deep = 0)
						{
							$padding = str_repeat('  ', $deep * 3);

							if ($deep != 0) {
								echo '<ul class="dropdown-menu">';
							}
							foreach ($treeMenu as $arr) {
								if ($deep == 0) {
									echo '<li>';
								} else {
									if (isset($arr['sub_menu'])) {
										echo '<li class="dropdown-submenu">';
									} else {
										echo '<li>';
									}
								}
								if (isset($arr['sub_menu'])) {
									if ($deep != 0) {
										echo '<a href="' . $arr['url'] . '">' . $arr['menu_name'] . '</a>';
									} else {
										echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $arr['menu_name'] . ' <span class="caret"></span></a>';
									}

									// echo $padding .'    '. $arr['menu_name'];
									doOutputList($arr['sub_menu'], $deep + 1);
								} else {
									if ($deep != 0) {
										echo '<a href="' . $arr['url'] . '">' . $arr['menu_name'] . '</a>';
									} else {
										echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $arr['menu_name'] . ' <span class="caret"></span></a>';
									}
									// echo $padding .'    '. $arr['menu_name'];
								}
								echo "</li>";
							}
							if ($deep != 0) {
								echo "</ul>";
							}
						}

						doOutputList($treeMenu);
						?>

					</ul>
					<ul class="nav navbar-nav navbar-right menu-nav-bottom nav-log-search">
						<li><a href="<?= base_url('dashboard') ?>" id="login-navbar"><span class="glyphicon glyphicon-log-in" style="margin-right:5px"></span>
								<?php if (UserModel::loginData('id', '-1') != '-1') : ?>
									Dashboard
								<?php else : ?>
									Login
								<?php endif; ?></a></li>
					</ul>

					</ul>
				</div>
			</div>
		</div>

	</div>
</nav>