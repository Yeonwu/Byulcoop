<!--

회원 관리 페이지

매점 협동 조합 판매자 사이트 디자인 9,10,11p 참고

-->

<?php

require_once ("./view/user_auth.php"); // getAuth() 함수 불러오기

$auth = getAuth(); // 로그인 한 계정 Auth 저장
if ( $auth === '소비자' || $auth === '조합원') { // auth 체크 후 권한 없을 시 홈화면
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>

<?php

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
				<tr class='w3-container w3-border {$table_impact}member-user-row'>
					<td class='w3-border w3-container w3-hide db_id'>{$row['id']}</td>
					<td class='w3-border w3-container id'>{$i}</td>
					<td class='w3-border w3-container grade'>{$row['grade']}</td>
					<td class='w3-border w3-container email'>{$row['email']}</td>
					<td class='w3-border w3-container name'>{$row['name']}</td>
					<td class='w3-border w3-container auth'>{$row['auth']}</td>
					<td class='w3-container center-container'>
						<form action='./index.php?id=info' method='post'>
							<input type='hidden' name='info_user_id' value='{$row['id']}'>
							<input type='submit' class='w3-button w3-border' value='정보'>
						</form>
					</td>
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
	
	<form id="info-other-form" method="POST" action="./index.php?id=info">
		<input type="hidden" name="info_user_id">
	</form>
	
	<div id="member-table-wrap" class="w3-border w3-card">
		<table id="member-table" class="w3-table">
			<tr id="member-table-head">
				<td class="w3-border w3-container w3-hide">DB번호</td>
				<td class="w3-border w3-container">번호</td>
				<td class="w3-border w3-container">학년</td>
				<td class="w3-border w3-container">이메일</td>
				<td class="w3-border w3-container">이름</td>
				<td class="w3-border w3-container">권한</td>
				<td class="w3-border w3-container">
				</td>
			</tr>
			<p id="member-noResult-message" class="w3-center w3-xlarge">
				검색 결과가 없습니다.
			</p>
			
			<?php printData(); ?>
			
		</table>
	</div>

	<script src="./assets/js/info_other.js"></script>
</div>