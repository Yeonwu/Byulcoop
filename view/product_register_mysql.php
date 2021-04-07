<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' || $auth === '판매자') {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

if($_FILES['img']['error'] === UPLOAD_ERR_OK) {
	$file = $_FILES['img'];

	$upload_directory = CONF_DIR['img'];
	$ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";

	$allowed_extensions = explode(',', $ext_str);
	$max_file_size = 5242880;
	$ext = substr($file['name'], strrpos($file['name'], '.') + 1);

	// 확장자 체크
	if(!in_array($ext, $allowed_extensions)) {
		echo "업로드할 수 없는 확장자 입니다.";
	}

	// 파일 크기 체크
	if($file['size'] >= $max_file_size) {
		echo "5MB 까지만 업로드 가능합니다.";
	}
	$path = date("YmdHis") . md5(microtime()) . '.' . $ext;

	move_uploaded_file($file['tmp_name'], $upload_directory.$path);

} else {
	echo 'fail</br>';
	var_dump($_FILES['img']);
}


$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}


$insertQuery = "INSERT INTO goods (image,type,name,stock,member_price,general_price,time) VALUES ('{$path}', '{$_POST['op']}', '{$_POST['pn']}', '{$_POST['nb']}', '{$_POST['mp']}', '{$_POST['up']}', NOW())";

if ($result = $mysqli -> query($insertQuery)) {
	echo "<script>window.location.href = './index.php?id=product';</script>";
} else {
	echo "<script>alert('실패');</script>";
}
echo $insertQuery;

$mysqli -> close();
?>