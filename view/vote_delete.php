<?php
	require "./config.php";
		$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
				  mysqli_set_charset($mysqli, 'utf8');
		$sql = "DELETE 
				FROM vote_content 
				WHERE id = {$_GET['article_id']}";
		mysqli_query($mysqli, $sql);
		$sql = "DELETE 
				FROM vote_option 
				WHERE vote_id = {$_GET['article_id']}";
		mysqli_query($mysqli, $sql);
	echo "<script>
				alert('성공');
				window.location.href = '../index.php?id=vote';
			 </script>";
?>