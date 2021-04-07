<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' ) {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>

<?php
$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$cid = $mysqli -> query("SELECT id FROM `user` WHERE email='{$_POST['email']}'") -> fetch_assoc()['id'];
$sid = $mysqli -> query("SELECT id FROM `user` WHERE email='{$_COOKIE['user_email']}'") -> fetch_assoc()['id'];

$insertQuery = "INSERT INTO `charge_record` (
				`customer_id`,
				`seller_id`,
				`money`,
				`note`)
				
				VALUES (
				'{$cid}',
				'{$sid}',
				'{$_POST['cost']}',
				'{$_POST['etc']}');";

if ( $mysqli -> query( $insertQuery ) ) {
	echo "<script>
			alert('{$_POST['cost']}원 충전이 완료되었습니다.');
			window.location.href = './index.php?id=searchUser&URL=check_face_charge';
		  </script>";
} else {
	echo "<script>
			alert('충전에 실패하였습니다. 충전이 잘 되었는지 확인 후 다시 시도해주세요.');
			window.location.href = './index.php?id=searchUser&URL=check_face_charge';
		  </script>";
}
?>