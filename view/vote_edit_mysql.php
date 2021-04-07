<?php
	/*
	
	POST로 넘어오는 값
	-------------------------------------
	title            투표 제목
	content          투표 글 내용
	vote-item        투표할 항목 (배열 형태)
	end-date         투표 마감 날짜
	vote-per-head    한 사람당 투표 수
	-------------------------------------
	*/

	require "./config.php";
	$end_date = $_POST['end-date'] . " 23:59:59";
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "INSERT INTO vote_content (title, content, email, vote_limit, vote_deadline)
			VALUES ('{$_POST['title']}', '{$_POST['content']}', '{$_COOKIE['user_email']}', '{$_POST['vote-per-head']}', '{$end_date}');";
	mysqli_query($mysqli, $sql);
	$sql = "SELECT id 
			FROM vote_content 
			WHERE title = '{$_POST['title']}' 
			ORDER BY created DESC";
	$result = mysqli_query($mysqli, $sql);
	$row = mysqli_fetch_assoc($result);

	$i = 0;
	$sql = "INSERT
				 INTO vote_option (name, vote_id) VALUES ";
	while($_POST['vote-item'][$i]){
		$sql .= "('{$_POST['vote-item'][$i]}', '{$row['id']}'),";
		$i += 1;
	}
	$sql = substr($sql, 0, -1);
	$sql .= ";";
	mysqli_query($mysqli, $sql);
	echo "<script>
				alert('성공');
				window.location.href = '../index.php?id=vote';
			 </script>";
?>