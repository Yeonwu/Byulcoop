<?php
	require "./config.php";
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "UPDATE suggest
			SET 
			answer = '{$_POST['answer_content']}', 
			answer_writer = '{$_POST['user_name']}' 
			WHERE id = {$_POST['article_id']}";
	mysqli_query($mysqli, $sql);
	echo "
		<script>
			alert('성공');
			window.location.href = '../index.php?id=suggest_read&article_type=suggest&article_id={$_POST['article_id']}';
		</script>";


	$sql = "
		SELECT 
			* 
		FROM 
			`suggest` 
		WHERE
			id= {$_POST['article_id']}
	";
	$article = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
	if(mysqli_affected_rows($mysqli) == 0) exit;

	$STMPMAIL = "oyeonu@naver.com";
	$FROM = "별쿱매점";
	if(SERVER_TYPE === "DEV") {
		$FROM .= "(개발서버)";
	}
	$TO = $article['email'];
	$TITLE = "{$article['writer']}님께서 건의하신 '{$article['title']}에 대한 답변이 달렸습니다.'";
	$CONTENT = "";
	if(SERVER_TYPE === "DEV") {
		$CONTENT .= "<h3>개발서버에서 보낸 테스트메일입니다. 무시하셔도 상관없습니다.</h3>";
	}
	$CONTENT .= "<h6>{$article['answer']}</h6>";
	$CONTENT .= "<p>[{$article['answer_writer']}]</p>";
	$CONTENT .= "<a href='".URL."/index.php?id=suggest_read&article_type=suggest&article_id={$article['id']}'>별쿱 사이트로 이동</a>";
	$CONTENT .= "<script>
			alert('성공');
			window.location.href = '../index.php?id=suggest_read&article_type=suggest&article_id={$_POST['article_id']}';
		</script>";
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require '../assets/PHPMailer/src/Exception.php';
	require '../assets/PHPMailer/src/PHPMailer.php';
	require '../assets/PHPMailer/src/SMTP.php';

	function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
	{
		if ($type != 1) $content = nl2br($content);
		// type : text=0, html=1, text+html=2
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$mail->IsSMTP();
		   $mail->SMTPDebug = 2;
		$mail->SMTPSecure = "ssl";
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.naver.com";
		$mail->Port = 465;
		$mail->Username = EMAIL_USER_NM; // ---------------------------------------------
		$mail->Password = EMAIL_PW; // ------------------------------------------
		$mail->CharSet = 'UTF-8';
		$mail->From = $fmail;
		$mail->FromName = $fname;
		$mail->Subject = $subject;
		$mail->AltBody = ""; // optional, comment out and test
		$mail->msgHTML($content);
		$mail->addAddress($to);
		if ($cc)
			$mail->addCC($cc);
		if ($bcc)
			$mail->addBCC($bcc);
		if ($file != "") {
			foreach ($file as $f) {
				  $mail->addAttachment($f['path'], $f['name']);
			}
		}
		if ( $mail->send() ) echo "성공";
		else echo "실패";
	}
	mailer($FROM, $STMPMAIL, $TO, $TITLE, $CONTENT);
?>