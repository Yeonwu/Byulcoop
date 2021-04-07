<?php
 /*
 $_POST['article_id']
 $_POST['article_type'] 'suggest' or 'vote'
 $_POST['content']로 댓글 내용 넘어옴.
 DB에 올린 후 밑에 html로 출력해주면 돼
 댓글에는 올린 사람, 내용, 날짜 있어
 관계형 DB로 어느 글에 단 댓글인지도 저장 필요
 */
	require "./config.php";
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "INSERT INTO comment (article_id, type, content, email)
			VALUES ('{$_POST['article_id']}', '{$_POST['article_type']}', '{$_POST['content']}', '{$_COOKIE['user_email']}');";
	mysqli_query($mysqli, $sql);
?>

<div class="suggest-comment">
	
	<div class="flex">
		<div style="font-weight: bold;">
			<?php echo "{$_COOKIE['user_name']}"; ?>&nbsp;
		</div>
		<div>
			<?php echo "({$_COOKIE['user_email']})"; ?>&nbsp;&nbsp;&nbsp;
		</div>
		<div class="t-dark-gray w3-small" style="align-self: center;">
			<?php echo date("Y-m-d H:i:s") ?>
		</div>
	</div>
	<?php echo $_POST['content']; ?>
</div>
<hr>