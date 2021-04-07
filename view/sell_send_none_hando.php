<!-- 판매창에서 DB로 데이터 넘겨주는 파일 -->
<!-- 조건 체크함 -->


<?php
require_once "./view/user_auth.php";

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' ) {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}
	require_once "./view/user_account.php";


// 초반작업 끝










	// 데이터베이스에 접근한다.
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');

	// 소비자 이메일을 key 값으로 이용하여 다른 값들을 가져온다.
	$result = mysqli_query($mysqli, "SELECT * FROM user WHERE email='".$_POST['customer']."'");
	$customer = mysqli_fetch_assoc($result);

	// 판매자 이메일을 key 값으로 이용하여 다른 값들을 가져온다.

	$result = mysqli_query($mysqli, "SELECT * FROM user WHERE email='".$_POST['seller']."'");
	$seller = mysqli_fetch_assoc($result);

	// 제이슨 데이터를 변수에 담는다.
	$data = json_decode($_POST['json_data']);
	
	// 변수 정의

	// 반복을 위함
	$i = 0;
	// 돈 정의
	$money = 0;

	// 제고가 충분한지 확인한다.
	while($i < $_POST['length']) {
		$result = mysqli_query($mysqli, "SELECT stock 
										 FROM goods 
										 WHERE name='" . $data[$i] -> name . "'");
		$row = mysqli_fetch_assoc($result);
		if($row['stock'] < $data[$i] -> num) {
			echo "<script>
				alert('재고가 부족합니다');
				window.location.href = './index.php?id=choose_name';
			 </script>";
			exit;
		}
		else {
			$price = (int) mb_substr($data[$i] -> price, 0, -1);
			$money = $data[$i] -> num * $price + $money;
			$i = $i + 1;
		}
	}

	// 돈이 충분한지 확인한다.
	if(get_user_account($_POST['email']) < $money) {
		echo "<script>
				alert('잔액이 부족합니다');
				window.location.href = './index.php?id=choose_name';
			 </script>";
			exit;
	}

	// 일주일 구매한도를 초과했는지 확인한다.
	// $week_sales = get_sell($_POST['email'], 'weekday');
	// if(date('w') == 0 || date('w') == 6 || date('w') == 5) {
	// 	$week_sales = 0;
	// }
	// if($week_sales >= 10000 && $customer['grade'] !== "선생님") {
	// 	echo "<script>
	// 			alert('일주일 구매 한도를 초과했습니다.{$week_sales}');
	// 			window.location.href = './index.php?id=choose_name';
	// 		 </script>";
	// 		exit;
	// }

	// 날짜+'4자리난수'를 이용해 18자리의 결제번호를 생성하고 $pay_num변수에 저장한다.
	$randomNum = mt_rand(1000, 9999);
	$date=date("YmdHis");
	$pay_num=$date.$randomNum;
	
	$i=0;
	while($i<$_POST['length']) {
		$result = mysqli_query($mysqli, "SELECT id, stock 
										 FROM goods 
										 WHERE name='" . $data[$i] -> name . "'");
		$row=mysqli_fetch_assoc($result);
		
		$price_in_int = (int) mb_substr($data[$i] -> price, 0, -1);
		
		$sql = "INSERT INTO sales_record 
				(customer_id, seller_id, goods_id, goods_num, goods_price, pay_num) 
				VALUES(" . $customer['id'] . ", " . $seller['id'] . ", " . $row['id'] . ", " . $data[$i] ->num . ", " . $price_in_int . ", '" . $pay_num . "')";
		
		mysqli_query($mysqli, $sql);
		
		$stock = $row['stock'] - $data[$i] -> num;
		
		$sql = "UPDATE goods SET stock = " . $stock . " WHERE id = " . $row['id'];
		
		mysqli_query($mysqli, $sql);
		

		$i=$i+1;
	}
	echo "<script>
				alert('결제완료. 남은금액:" . get_user_account($_POST['email']) . "원. 사용금액: " . $money . "원');
				window.location.href = './index.php?id=choose_name';
			 </script>";

	// 메일 보내는 작업

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require ('./assets/PHPMailer/src/Exception.php');
	require ('./assets/PHPMailer/src/PHPMailer.php');
	require ('./assets/PHPMailer/src/SMTP.php');

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


	$STMPMAIL = "oyeonu@naver.com";
	$FROM = "별쿱매점";
	if(SERVER_TYPE === "DEV") {
		$FROM .= "(개발서버)";
	}
	$TO = $customer['email'];
	$TITLE = "" . date("Y") . "년" . date("m") . "월" . date("d") . "일 매점 주문 영수증";
	$CONTENT = "";
	if(SERVER_TYPE === "DEV") {
		$CONTENT .= "<h3>개발서버에서 보낸 테스트메일입니다. 무시하셔도 상관없습니다.</h3>";
	}
	$CONTENT .= "<p>주문번호 : {$pay_num}</p>";
	$CONTENT .= "<p>주문상품목록<p>";
	$CONTENT .= "<ul>";

	$i = 0;
	while ( $i < $_POST['length'] ) {
		$CONTENT .= "<li>{$data[$i] -> name} : {$data[$i] -> num}개</li>";
		$i++;
	}
	$CONTENT .= "</ul>";

	$CONTENT .= "<p>주문 일시 : " . date("Y.m.d H:i") . "</p>";
	$CONTENT .= "<p>구매자 이름 : {$customer['name']}</p>";
	$CONTENT .= "<p>판매자 이름 : {$seller['name']}</p>";
	$CONTENT .= "<p>잔액 : " . get_user_account($customer['email']) . "</p>";
	$CONTENT .= "<p>별쿱을 이용해 주셔서 감사합니다.</p>";
	mailer($FROM, $STMPMAIL, $TO, $TITLE, $CONTENT);
	exit;
?>/