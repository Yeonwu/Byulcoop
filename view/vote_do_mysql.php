<?php 

	// 이전 코드는 주석처리 해서 맨 밑에 있음.
	// $_COOKIE['user_email']
	// $_GET['article_id']: 투표한 게시물 아이디
	// $_GET['vote']: 투표한 항목의 이름

	require "./config.php";
	error_reporting(E_ALL);
	ini_set("display_errors", 'on');

	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');

	// 게시물의 최대 투표수와 마감일을 변수에 저장
	$sql = "SELECT vote_limit, vote_deadline 
			FROM vote_content 
			WHERE id = {$_GET['article_id']}";
	$result = mysqli_query($mysqli, $sql);
	$row = mysqli_fetch_assoc($result);
	$vote_limit = (int)($row['vote_limit']);
	$deadline = $row['vote_deadline'];

	// 마감일 지났는지 채크하기 위해 변수저장
	$nowTime = date("Y-m-d H:i:s");

	// 몇번 투표했는지 채크
	$sql = "SELECT 
			COUNT(id)
			AS cnt
			FROM vote_value 
			WHERE vote_id = '{$_GET['article_id']}'
			AND email = '{$_COOKIE['user_email']}'";
	$result = mysqli_query($mysqli, $sql);
	$vote_number = (int)($result -> fetch_assoc()['cnt']);

	
	
	// 중복 투표인지 채크
	$sql = "SELECT 
			COUNT(id)
			AS cnt
			FROM vote_value 
			WHERE vote_id = '{$_GET['article_id']}'
			AND email = '{$_COOKIE['user_email']}'
			AND value = '{$_GET['vote']}'";
	$result = mysqli_query($mysqli, $sql);
	$vote_already = (int)($result -> fetch_assoc()['cnt']);

	// 

	
	
	// 정보 저장
	$sql = "INSERT 
			INTO vote_value (vote_id, value, email)
			VALUES ('{$_GET['article_id']}', '{$_GET['vote']}', '{$_COOKIE['user_email']}');";
	$tmp = $vote_limit - $vote_number - 1;
	if($nowTime > $deadline){
		echo "<script>
				alert('투표실패! 이미 마감된 투표입니다.');
				window.location.href = '../index.php?id=vote_read&article_type=vote&article_id={$_GET['article_id']}';
			  </script>";
	} else if($vote_number >= $vote_limit){
		echo "<script>
				alert('투표실패! 한 사람당 최대 {$vote_limit}번까지 투표할 수 있습니다. 남은 투표횟수: {$tmp}');
				window.location.href = '../index.php?id=vote_read&article_type=vote&article_id={$_GET['article_id']}';
			  </script>";
	} else if($vote_already > 0){
		echo "<script>
				alert('투표실패! {$_GET['vote']}에 이미 투표하셨습니다.');
				window.location.href = '../index.php?id=vote_read&article_type=vote&article_id={$_GET['article_id']}';
			  </script>";
	} else {
		mysqli_query($mysqli, $sql);
	}
	echo "<script>
			alert('투표성공! {$_GET['vote']}에 투표되었습니다. 남은 투표횟수: {$tmp}');
			window.location.href = '../index.php?id=vote_read&article_type=vote&article_id={$_GET['article_id']}';
		 </script>";
	
	//$voteTo에 투표 항목 이름들
	//$howMuch에 몇명이나 투표했는지
	//$ratio에 비율

	// $voteTo = ['어니언', '오리지널', '치즈'];
	// $howMuch = ['10', '21', '5'];
	// $ratio = ['28', '57', '14'];

	// 이거 말고 다른 echo는 하면 안됌.
	// 디버깅 할 때는 콘솔창에 출력되니까 참고해서 코드 작성해
?>