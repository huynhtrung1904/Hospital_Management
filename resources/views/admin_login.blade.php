<!DOCTYPE html>
<head>
<title>Page quản lý admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link rel="stylesheet" href="public/BackEnd/css/bootstrap.min.css" >
<!-- //bootstrap-css -->
<!-- Custom CSS -->
<link href="public/BackEnd/css/style.css" rel='stylesheet' type='text/css' />
<link href="public/BackEnd/css/style-responsive.css" rel="stylesheet"/>
<!-- font CSS -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- font-awesome icons -->
<link rel="stylesheet" href="public/BackEnd/css/font.css" type="text/css"/>
<link href="public/BackEnd/css/font-awesome.css" rel="stylesheet">
<!-- //font-awesome icons -->
<script src="js/jquery2.0.3.min.js"></script>
</head>
<body>
<div class="log-w3">
<div class="w3layouts-main">
	<h2>Đăng nhập</h2>
	<?php
	$message = Session::get("message");
	if ($message) {
		echo '<div class="alert alert-danger alert-dismissable" style="max-width: 100%; display: inline-block; margin-top: 10px;">';
		echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
		echo $message;
		echo '</div>';
		Session::put('message', null);
	}
	?>
		<form action="{{ route('dashboard') }}" method="post">
			@csrf
			<!-- {{ csrf_field() }}   -->
			<input type="text" class="ggg" name="admin_email" placeholder="E-MAIL" required="">
			<input type="password" class="ggg" name="admin_password" placeholder="PASSWORD" required="">
			<span><input type="checkbox" />Nhớ đăng nhập</span>
			<h6><a href="#">Quên mật khẩu?</a></h6>
				<div class="clearfix"></div>
				<input type="submit" value="Đăng nhập" name="login">
		</form>
</div>
</div>
<script src="public/BackEnd/js/bootstrap.js"></script>
<script src="public/BackEnd/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="public/BackEnd/js/scripts.js"></script>
<script src="public/BackEnd/js/jquery.slimscroll.js"></script>
<script src="public/BackEnd/js/jquery.nicescroll.js"></script>
<script src="public/BackEnd/js/jquery.scrollTo.js"></script>
</body>
</html>
