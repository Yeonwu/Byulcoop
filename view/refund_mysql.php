<?php
function checkAuthRefund() {
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
}

function connectDB() {
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']); // connect DB
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		//return $mysqli -> connect_error;
		exit();
	}
	return $mysqli;
}

function checkMysqliError($result) {
	if (!$result && !CONF_ERR_PRINT) {
		echo "Query ERROR in Refund Mysql. Please report to Admin";
		exit();
	} else {
		return $result;
	}
}

function getUserInfoByEmail($email, $mysqli) {
	$sql = "
	SELECT *
	FROM `user`
	WHERE email = '{$email}';";
	
	return checkMysqliError($mysqli -> query($sql)) -> fetch_assoc();
}

function getGoodsInfoByName($name, $mysqli) {
	$sql = "
	SELECT *
	FROM `goods`
	WHERE name = '{$name}';";
	
	return checkMysqliError($mysqli -> query($sql)) -> fetch_assoc();
}

function refundSql($mysqli) {
	
	$customer = getUserInfoByEmail($_POST['customer_email'], $mysqli);
	
	$seller = getUserInfoByEmail($_COOKIE['user_email'], $mysqli);
	
	$refundData = json_decode($_POST['refund_list']);
	
	$sales_sql = "
	INSERT INTO `sales_record` (
		`customer_id`,
		`seller_id`,
		`goods_id`,
		`goods_num`,
		`goods_price`,
		`pay_num`
	) VALUES ";

	foreach($refundData as $data) {
		
		$goods = getGoodsInfoByName($data -> goods_name, $mysqli);
		
		$sales_sql .= "
		(
			{$customer['id']},
			{$seller['id']},	
			{$goods['id']},
			-1,";
		
		if($customer['auth'] === "소비자") {
			$sales_sql .= "{$goods['general_price']},";
		} else {
			$sales_sql .= "{$goods['member_price']},";
		}
		
		$sales_sql .="
			{$data -> pay_num}
		), ";
		
		$goods_sql="
		UPDATE `goods`
		SET stock = (stock + 1)
		WHERE id = {$goods['id']}";
		
		checkMysqliError($mysqli -> query($goods_sql));
		
	}
	
	$sales_sql = substr($sales_sql, 0, -2);
	$sales_sql .= ";";
	
	return $sales_sql;
	
}

function initRefundMysql() {
	checkAuthRefund();
	
	echo "Please send the log to the admin if the program doesn't work.<br>";
	echo "Connecting DB...<br>";
	
	$mysqli = connectDB();
	
	echo "Success<br>";
	echo "Buliding Queries...<br>";
	
	$sql = refundSql($mysqli);
	
	echo "Success<br>";
	if(SERVER_TYPE === "DEV") echo "Query : <br>{$sql}<br>";
	echo "Executing Sales Query...<br>";
	
	checkMysqliError($mysqli -> query($sql));
	
	echo "Success<br>";
	echo "Closing DB Connection...<br>";
	
	$mysqli -> close();
	
	echo "Success<br>";
}

initRefundMysql();
?>

<script>
	
	alert('환불되었습니다.');
	window.location.href = "./index.php?id=searchUser&URL=check_face_sell";

</script>