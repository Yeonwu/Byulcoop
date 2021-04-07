<!--

회원 관리 페이지

매점 협동 조합 판매자 사이트 디자인 9,10,11p 참고

-->

<?php

require_once ("./view/user_auth.php"); // getAuth() 함수 불러오기

$auth = getAuth(); // 로그인 한 계정 Auth 저장
if ( $auth === '소비자' || $auth === '조합원' || $auth === '판매자') { // auth 체크 후 권한 없을 시 홈화면
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>

<?php

function onResister() {
	 
	 

	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']); // connect DB
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	
	//이미지 업로드
	
	$len = $_POST['numberOfData'];
	$imgPath = array();
	if($len <= 0) array_push($imgPath, '');
	
	for($key = 0; $key < $len; $key++) {
		
		if( $_FILES['img']['error'][$key] !== UPLOAD_ERR_OK) break;
		
		if(isset($_FILES['img'][$key]) && $_FILES['img']['name'][$key] != "" || true) {
			$file = $_FILES['img'];
			
			$upload_directory = CONF_DIR['img'];
			
			$ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";
			$allowed_extensions = explode(',', $ext_str);
			
			// 파일 확장자 가져오기
			$ext = substr($file['name'][$key], strrpos($file['name'][$key], '.') + 1);
			
			// 확장자 체크
			if(!in_array($ext, $allowed_extensions)) {
				echo "업로드할 수 없는 확장자 입니다.";
			}
			
			
			// 파일 크기 체크
			$max_file_size = 5242880;
			
			if($file['size'][$key] >= $max_file_size) {
				echo "5MB 까지만 업로드 가능합니다.";
			}
			
			// 이미지 이름 생성
			$path = date("YmdHis") . md5(microtime()) . '.' . $ext;
			
			// 업로드
			move_uploaded_file($file['tmp_name'][$key], $upload_directory.$path);
			
			// 파일 이름 저장 ( DB에 저장 용도 )
			array_push($imgPath, $path);
			
		} else {
			echo 'fail</br>';
			echo $_FILES['img'][$key].'</br>';
			echo $_FILES['img']['name'][$key].'</br>';
		}
		
	}
	
	
	//DB정보 업로드
	$insertQuery = "
	INSERT INTO `user` (
		`id`, 
		`name`, 
		`grade`, 
		`email`, 
		`school_code`, 
		`auth`, 
		`birth`,  
		`image`, 
		`time`
	) VALUES ";
	
	$len = $_POST['numberOfData'];
	$dataList = json_decode($_POST['data']);
	$alertMessage = "성공 : {$len}명의 유저 정보를 등록했습니다.\n";
	
	for ( $i = 0; $i < $len; $i++ ) {
		
		
		// 동명이인 확인
		$result = $mysqli -> query("SELECT * 
									FROM `user` 
									WHERE `name`='".$mysqli -> real_escape_string($dataList[$i] -> user_name)."';");
		
		if ($mysqli -> affected_rows > 0) {
			$alertMessage = $alertMessage . "경고 : 이름이 {$dataList[$i] -> user_name}인 사람이 여러명입니다. 확인해주세요.\n";
		}
		
		// 이메일 중복 확인
		$result = $mysqli -> query("SELECT * 
									FROM `user` 
									WHERE `email`='".$mysqli -> real_escape_string($dataList[$i] -> user_email)."';");
		
		if ($mysqli -> affected_rows > 0) {
			$alertMessage = "실패 : 이메일이 {$mysqli -> real_escape_string($dataList[$i] -> user_email)}인 사람이 여러명입니다. 확인해주세요.\n";
			break;
		}
		
		$result -> free_result();
		
		// 학번 생성
		$school_code = substr( $dataList[$i] -> user_email, 0, -14 );
		
		// 쿼리에 데이터 추가
		$insertQuery = $insertQuery . 
		"(
		NULL, 
		'".$mysqli -> real_escape_string($dataList[$i] -> user_name)."', 
		'".$mysqli -> real_escape_string($dataList[$i] -> user_grade)."', 
		'".$mysqli -> real_escape_string($dataList[$i] -> user_email)."',
		'".$mysqli -> real_escape_string($school_code)."', 
		'".$mysqli -> real_escape_string($dataList[$i] -> user_auth)."', 
		'".$mysqli -> real_escape_string($dataList[$i] -> user_birth)."',
		'".$mysqli -> real_escape_string($imgPath[$i])."', 
		NULL
		),";	
	}
	
	$insertQuery = substr( $insertQuery, 0, -1 ); // 마지막 쉼표 제거
	$insertQuery = $insertQuery.";"; // 세미콜론 붙이기
	
	if ( preg_match_all( "/^실패/", $alertMessage ) ) {
		$result = -1;
	} else {
		$result = $mysqli -> query($insertQuery);
	}
	
	//Perform query
	if ( $result ) {
		echo "<script>
				alert(`{$alertMessage}`); 
				window.location.href = './index.php?id=member';
			 </script>";
	} else {
		echo "<script>
				alert('Query Failed. 관리자에게 캡쳐 후 문의');
				//window.location.href = './index.php?id=member';
			 </script>";
	}
	$mysqli -> close();
	
}

function onDelete(){

	// DB에 연결
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	$deleteQuery = "UPDATE `user` 
					SET deleted = TRUE
					WHERE ";
	
	$len = $_POST['numberOfData'];
	$dataList = json_decode($_POST['userInfo']);
	$alertMessage = "";
	
	for ( $i = 0; $i < $len; $i++ ) {
		
		$result = $mysqli -> query("SELECT * 
									FROM `user` 
									WHERE id='".$mysqli -> real_escape_string($dataList[$i])."';");
		
		
		// 있는 사람인지 체크
		if ($mysqli -> affected_rows === 0) {
			$alertMessage = $alertMessage . "실패 : 번호가 {$dataList[$i]}인 유저가 없습니다. 캡쳐 후 관리자에게 문의하세요.\n";
		}
		$result -> free_result();
		
		// 쿼리에 삭제할 유저 아이디 추가
		$deleteQuery = $deleteQuery . "id = " . $mysqli -> real_escape_string($dataList[$i]) . " OR ";
		
		// 이미지 삭제
		if ( !$img = unlink(CONF_DIR['img'].$result -> fetch_assoc()['image']) ) {
			$alertMessage = $alertMessage . "실패 : 유저 이미지를 삭제하지 못했습니다. 파일명: '{$img}' 캡쳐 후 관리자에게 문의하세요";
		}
	}
	
	$deleteQuery = substr( $deleteQuery, 0, -3 ); // 마지막 OR 제거
	$deleteQuery = $deleteQuery . ";"; // 세미콜론 붙이기
	
	// Perform query
	if ($result = $mysqli -> query($deleteQuery)) {
		$alertMessage .= "성공 : {$len}명의 유저 정보를 삭제했습니다.\n";
		echo "<script>
				alert(`{$alertMessage}`);
				window.location.href = './index.php?id=member';
			 </script>";
	} else {
		echo "<script>
				alert('실패');
				//window.location.href = './index.php?id=member';
			 </script>" . $deleteQuery;
	}
	
	$mysqli -> close();
	
}

function onAdjust(){  // onResister와 구조 같음
	 
	// DB에 연결
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	$len = $_POST['numberOfData'];
	$dataList = json_decode($_POST['userInfo']);
	$imgPath = array();
	
	
	for($key = 0; $key < $len; $key++) {
		if ( $_FILES['img']['error'][$key] !== UPLOAD_ERR_OK || !$dataList[$key] -> img_updated) {
			array_push($imgPath, "noChange");
			continue;
		}
		
		if(isset($_FILES['img']['name'][$key]) && $_FILES['img']['name'][$key] != "") {
			$file = $_FILES['img'];
			

			$upload_directory = CONF_DIR['img'];
			$ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";

			$allowed_extensions = explode(',', $ext_str);
			$max_file_size = 5242880;
			$ext = substr($file['name'][$key], strrpos($file['name'][$key], '.') + 1);

			// 확장자 체크
			if(!in_array($ext, $allowed_extensions)) {
				echo "업로드할 수 없는 확장자 입니다.";
			}

			// 파일 크기 체크
			if($file['size'][$key] >= $max_file_size) {
				echo "5MB 까지만 업로드 가능합니다.";
			}
			$path = date("YmdHis") . md5(microtime()) . '.' . $ext;

			move_uploaded_file($file['tmp_name'][$key], $upload_directory.$path);
			array_push($imgPath, $path);

		} else {
			echo 'fail</br>';
			echo $_FILES['img'][$key].'</br>';
			echo $_FILES['img']['name'][$key].'</br>';
		}
		
	}
	
	
	$alertMessage = "성공 : {$len}명의 유저 정보를 수정했습니다.\n";
	
	for ( $i = 0; $i < $len; $i++ ) {
		
		$school_code = substr( $dataList[$i] -> user_email, 0, -14 );
		
		// 이메일 중복 확인
		$result = $mysqli -> query("SELECT * 
									FROM `user` 
									WHERE `email`='".$mysqli -> real_escape_string($dataList[$i] -> user_email)."';");
		
		if ($mysqli -> affected_rows > 1) {
			$alertMessage = "실패 : 이메일이 {$mysqli -> real_escape_string($dataList[$i] -> user_email)}인 사람이 여러명입니다. 확인해주세요.\n";
			continue;
		}
		
		$updateQuery = "UPDATE `user` SET ";
		
		if ( $imgPath[$i] !== "noChange" ) {
			unlink(CONF_DIR['img'] . $mysqli 
				   -> query("SELECT image 
							 FROM `user` 
							 WHERE email='" . $mysqli -> real_escape_string($dataList[$i] -> user_email) . "';") 
				   -> fetch_assoc()['image']);
			
			$updateQuery = $updateQuery . "image = '".$mysqli -> real_escape_string($imgPath[$i])."', ";
		}
		
		$updateQuery = $updateQuery . "
		name = '".$mysqli -> real_escape_string($dataList[$i] -> user_name)."',
		grade = '".$mysqli -> real_escape_string($dataList[$i] -> user_grade)."', 
		email = '".$mysqli -> real_escape_string($dataList[$i] -> user_email)."',
		school_code = '".$mysqli -> real_escape_string($school_code)."', 
		auth = '".$mysqli -> real_escape_string($dataList[$i] -> user_auth)."', 
		birth = '".$mysqli -> real_escape_string($dataList[$i] -> user_birth)."'";
		
		$updateQuery = $updateQuery . "WHERE id = ".$mysqli -> real_escape_string($dataList[$i] -> user_id).";";
		
		$result = $mysqli -> query($updateQuery);
		if ($mysqli -> affected_rows === 0) {
			$alertMessage = $alertMessage . "경고 : 이름이 {$dataList[$i] -> user_name}인 유저의 정보를 수정할 수 없습니다. 확인해주세요.\n";
		}
	
	}
	
	echo "<script>
			alert(`{$alertMessage}`);
			window.location.href = './index.php?id=member';
		 </script>";
	
	$mysqli -> close();
}

function onEditAuth(){
	 
	   
	// DB에 연결
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	
	$len = $_POST['numberOfData'];
	$authTo = $_POST['authTo'];
	$dataList = json_decode($_POST['userInfo']);
	$alertMessage = "성공 : {$len}명의 유저 정보를 수정했습니다.\n";
	
	
	for ( $i = 0; $i < $len; $i++ ) {
		
		$updateQuery = "UPDATE `user` SET
		auth = '".$mysqli -> real_escape_string($authTo)."'
		WHERE id = ".$mysqli -> real_escape_string($dataList[$i])."";
		
		$result = $mysqli -> query($updateQuery);
	
	}
	
	echo "<script>
			alert(`{$alertMessage}`); 
			window.location.href = './index.php?id=member';
		 </script>";
	
	$mysqli -> close();
}

function onEditGrade(){
	 
	   
	// DB에 연결
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	
	$len = $_POST['numberOfData'];
	$gradeTo = $_POST['gradeTo'];
	$dataList = json_decode($_POST['userInfo']);
	$alertMessage = "성공 : {$len}명의 유저 정보를 수정했습니다.\n";
	
	
	for ( $i = 0; $i < $len; $i++ ) {
		
		$updateQuery = "UPDATE `user` SET
		grade = '".$mysqli -> real_escape_string($gradeTo)."'
		WHERE id = ".$mysqli -> real_escape_string($dataList[$i])."";
		
		$result = $mysqli -> query($updateQuery);
	
	}
	
	echo "<script>
			alert(`{$alertMessage}`); 
			window.location.href = './index.php?id=member';
		 </script>";
	
	
	$mysqli -> close();
}


if(isset($_GET["type"])) {
	$pageType = $_GET["type"];

	if(!empty($pageType)) {
		switch($pageType) {
			case "resister":
				onResister();
				break;
			case "delete":
				onDelete();
				break;
			case "adjust":
				onAdjust();
				break;
			case "auth":
				onEditAuth();
				break;
			case "grade":
				onEditGrade();
				break;
			default:
				break;
		}
	}
}

function printData() {  

	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	if(isset($_GET['show'])) {
		$selectQuery = "
			SELECT * FROM `user` 
			WHERE deleted = TRUE 
			ORDER BY grade, name;";
	} else {
		$selectQuery = "
			SELECT * FROM `user` 
			WHERE deleted = FALSE 
			ORDER BY grade, name;";
	}
	
	
	$result = $mysqli -> query($selectQuery);
	$len = $mysqli -> affected_rows;

	if ($len === 0){
		echo("Error description: " . $mysqli -> error);
		return;
	}


	for ( $i = 1; $i <= $len; $i++ ) {
		
		$row = $result -> fetch_assoc();
		if($i % 2) $table_impact = "table-impact ";
		else $table_impact = "";
		echo "
				<tr class='w3-container {$table_impact}member-user-row w3-hide'>
					<td class='w3-container w3-hide db_id'>{$row['id']}</td>
					<td class='w3-container id'>{$i}</td>
					<td class='w3-container grade'>{$row['grade']}</td>
					<td class='w3-container email'>{$row['email']}</td>
					<td class='w3-container name'>{$row['name']}</td>
					<td class='w3-container auth'>{$row['auth']}</td>
					<td class='w3-container center-container'><input type='checkbox' /></td>
				</tr>
			";
	}

	$result -> free_result();
	$mysqli -> close();

}

?>

<div id="member-container" class="center-container-column">
	<div id="member-searchBar" class="w3-bar center-container">
		<input
			type="text"
			placeholder="검색어를 입력하세요"
			id="member-searchTxt"
			class="custom-input1 w3-card"
			autocomplete="off"
		/>
	</div>
	<div id="member-btns" class="w3-bar w3-card">
		
		<?php
			if(!isset($_GET['show'])) {
				echo "
				<a href='./index.php?id=member_resister' class='w3-bar-item'>
					<button class='w3-button w3-large'>
						사용자 등록
					</button>
				</a>

				<a class='w3-bar-item' onclick='deleteUser()'>
					<button class='w3-button w3-large'>
						사용자 삭제
					</button>
					<form id='deleteUserInfo' action='./index.php?id=member&type=delete' method='POST'>
						<input type='hidden' name='numberOfData' />
						<input type='hidden' name='userInfo' />
					</form>
				</a>

				<a class='w3-bar-item' onclick='adjustUser()'>
					<button class='w3-button w3-large'>
						사용자 정보 수정
					</button>
					<form id='adjustUserInfo' action='./index.php?id=member_adjust' method='POST'>
						<input type='hidden' name='numberOfData' />
						<input type='hidden' name='userInfo' />
					</form>
				</a>

				<div class='w3-bar-item w3-dropdown-hover'>

					<button class='w3-button w3-large'>
						사용자 권한 수정
					</button>

					<div class='w3-dropdown-content w3-bar-block w3-border'>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserAuth(event)'>소비자로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserAuth(event)'>조합원으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserAuth(event)'>판매자로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserAuth(event)'>팀장으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserAuth(event)'>관리자로 변경</div>
					</div>

					<form id='userAuth' action='./index.php?id=member&type=auth' method='POST'>
						<input type='hidden' name='numberOfData' />
						<input type='hidden' name='userInfo' />
						<input type='hidden' name='authTo' />
					</form>
				</div>

				<div class='w3-bar-item w3-dropdown-hover'>
					<button class='w3-button w3-large'>
						사용자 학년 수정
					</button>
					<div class='w3-dropdown-content w3-bar-block w3-border'>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>6학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>7학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>8학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>9학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>10학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>11학년으로 변경</div>
						<div class='w3-bar-item w3-button w3-large' onclick='editUserGrade(event)'>12학년으로 변경</div>
					</div>
					<form id='userGrade' action='./index.php?id=member&type=grade' method='POST'>
						<input type='hidden' name='numberOfData' />
						<input type='hidden' name='userInfo' />
						<input type='hidden' name='gradeTo' />
					</form>
				</div>";
			}
		?>
		
		<div class="w3-bar-item w3-large">
			<?php
				if(isset($_GET['show'])) {
					echo "
					<a class='w3-button' href='./index.php?id=member'>
						삭제되지 않은 회원 보기
					</a>";
				} else {
					echo "
					<a class='w3-button' href='./index.php?id=member&show=deleted'>
						삭제된 회원 보기
					</a>";
				}
			?>
		</div>
		
	</div>
	
	<div id="member-table-wrap" class="w3-border w3-card">
		<table id="member-table" class="w3-table">
			<tr id="member-table-head" class='color-gray-warm'>
				<td class="w3-container w3-hide">DB번호</td>
				<td class="w3-container">번호</td>
				<td class="w3-container">학년</td>
				<td class="w3-container">이메일</td>
				<td class="w3-container">이름</td>
				<td class="w3-container">권한</td>
				<td class="w3-container w3-center">
					<input type="checkbox" />
				</td>
			</tr>
			<p id="member-noResult-message" class="w3-center w3-xlarge w3-hide">
				검색 결과가 없습니다.
			</p>
			<?php printData(); ?>
		</table>
	</div>

	<script src="./assets/js/member.js"></script>
</div>