<!-- 프로젝트원 전용 홈페이지 -->
<!-- 시간표 들어있음 -->

<?php

require_once ("./view/user_auth.php"); // getAuth() 함수 불러오기

$auth = getAuth(); // 로그인 한 계정 Auth 저장
if ( $auth === '소비자' || $auth === '조합원' ) { // auth 체크 후 권한 없을 시 홈화면
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>

<h>
	시간표
	
	<?php
	
	if ( $auth === '팀장' || $auth === '관리자' ) {
		echo "
				<form method='post' action='./index.php?id=home&type=timetableAdjust' enctype='multipart/form-data'>
				
					<label class='ht-theme hover-pointer'>
						<input type='file' name='img' class='w3-hide' accept='image/*' onchange='handleSubmit();' value='null'>
						시간표 사진 업로드 <i class='las la-upload'></i>
					</label>
					
					<label>
					
				</form>
				
				<script>

					function handleSubmit(event) {
						document.querySelector('form').submit();
					}

				</script>
				
				
				";
	}
	
	?>
	
</h>

<br>

<?php

if ( isset($_GET['type']) ) {
	
	if ( $_GET['type'] === 'timetableAdjust' ) {
		
		if($_FILES['img']['error'] === UPLOAD_ERR_OK) {
			$file = $_FILES['img'];

			$upload_directory = CONF_DIR['img'];
			$ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";

			$allowed_extensions = explode(',', $ext_str);
			$max_file_size = 5242880;
			$ext = substr($file['name'], strrpos($file['name'], '.') + 1);

			// 확장자 체크
			if(!in_array( strtolower($ext), $allowed_extensions)) {
				echo "업로드할 수 없는 확장자 입니다.";
				echo "<script>location.href='./index.php?id=home'</script>";
				exit();
			}

			// 파일 크기 체크
			if($file['size'] >= $max_file_size) {
				echo "5MB 까지만 업로드 가능합니다.";
				echo "<script>location.href='./index.php?id=home'</script>";
				exit();
			}
			$path = date("YmdHis") . md5(microtime()) . '.' . $ext;
			
			move_uploaded_file($file['tmp_name'], $upload_directory.$path);

		} else {
			echo 'fail</br>';
			var_dump($_FILES['img']);
		}
		
		
		$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']); // connect DB
		mysqli_set_charset($mysqli, 'utf8');

		// Check connection
		if ($mysqli -> connect_errno) {
		  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		  exit();
		}
		
		unlink(CONF_DIR['img'] . $mysqli -> query("SELECT * FROM `time_table_img`") -> fetch_assoc()['img'] );
		
		$mysqli -> query("UPDATE `time_table_img` SET img = '{$path}', created = NOW();");
		$mysqli -> close();
		
		echo "<script>location.href='./index.php?id=home'</script>";
		exit();
		
	}
	
}

	
$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']); // connect DB
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}
	
	
$timetable_img = './assets/img/' . $mysqli -> query("SELECT * FROM `time_table_img`") -> fetch_assoc()['img'];
echo "<img src='{$timetable_img}'>";

$mysqli -> close();

?>