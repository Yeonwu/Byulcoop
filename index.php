<?php
ob_start();
require_once "./view/config.php";

$NO_CACHE = FALSE;
if(isset($_COOKIE['version'])) {
	if($_COOKIE['version'] != VERSION) {
		$NO_CACHE = TRUE;
		setcookie('version', VERSION, time() + 86400 * 30, '/');
	} 
} else {
	$NO_CACHE = TRUE;
	echo "new cookie";
	setcookie('version', VERSION, time() + 86400 * 30, '/');
}


$navAbled = 'navAbled';

if (isset($_COOKIE['nav_open'])) {
	if ($_COOKIE['nav_open'] != 'true') {
		$navAbled = '';
	}
}

?>


<!DOCTYPE html>

<html lang = "kr">
	
	<head>
		
		<?php 
		require "./view/templates/top.php"; // html 파일 head 부분 불러오기
		if($NO_CACHE) require "./view/templates/no_cache.html";
		?>
		
	</head>
	
	<!-- <body oncontextmenu='return false' onselectstart='return false' ondragstart='return false'> -->
		
	<body>
		
		<?php
		require "./view/templates/nav.php"; // html 파일 nav 부분 불러오기
		require "./view/login.php"; // 로그인 창 띄우기
		?>
		
		<div id="body-content" class="<?php echo $navAbled; ?>">
		<?php

		if( isset($_GET['id']) ) {
			require "./view/{$_GET['id']}.php"; // id값에 맞는 content 불러오기 (ex - info.php)
			  
		}
		else {
			  
			require "./view/home.php"; // id값이 비어있으면 home 불러오기
			  
		}
			
		?>
		</div>
		
		<?php	
		require "./view/templates/bottom.php"; // footer 등 불러오기
		?>
	</body>
	
</html>
<?php

ob_end_flush();

?>