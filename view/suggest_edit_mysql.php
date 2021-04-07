<?php
	require "./config.php";
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "SELECT name 
			FROM user 
			WHERE email = '{$_COOKIE['user_email']}'";
	$result = mysqli_query($mysqli, $sql);
	$row = mysqli_fetch_assoc($result);
	if(isset($_POST['article_id'])){ 
		$sql = "UPDATE suggest
				SET 
				title = '{$_POST['title']}', 
				content = '{$_POST['content']}' 
				WHERE id = {$_POST['article_id']}";
	}
	else{
		$sql = "INSERT INTO suggest (title, content, email, writer)
				VALUES ('{$_POST['title']}', '{$_POST['content']}', '{$_COOKIE['user_email']}', '{$row['name']}');";
	}
	mysqli_query($mysqli, $sql);
	if(mysqli_affected_rows($mysqli) == 0) {
		echo "<script>
				alert('오류가 발생했습니다. 관리자에게 문의해주세요.');
				window.location.href = '../index.php?id=suggest';
			 </script>";
	} else {
		echo "<script>
				alert('성공');
				window.location.href = '../index.php?id=suggest';
			 </script>";
	}
?>