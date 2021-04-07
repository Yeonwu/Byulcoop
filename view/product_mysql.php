<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' || $auth === '판매자') {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = 'https://bmrcoop.run.goorm.io/bmrCoop/index.php?id=home';
	</script>
	";
}

?>
<?php
	$sql = mysqli_connect("localhost:3306", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);	
	$const = "INSERT INTO goods (name, stock, member_price, general_price, type, time) VALUES('{$_POST['pn']}', '{$_POST['nb']}', '{$_POST['mp']}', '{$_POST['up']}', '{$_POST['op']}', NOW())";
	$var = mysqli_query($sql, $const);
	
if ($var===false) {echo '시스템상 오류가 발생했습니다. 관리자에게 문의하세요.';}

?>