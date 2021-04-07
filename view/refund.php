<?php
$refund_table;

function checkAuthRefund() {
	require_once ("./view/user_auth.php"); // getAuth() 함수 불러오기

	$auth = getAuth(); // 로그인 한 계정 Auth 저장
	if ( $auth === '소비자' || $auth === '조합원' ) { // auth 체크 후 권한 없을 시 홈화면
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

function printRefundTable() {
	$mysqli = connectDB();

	$sql = "
		SELECT *
		FROM `user`
		WHERE email = '{$_POST['email']}'";

	$user_db = $mysqli -> query($sql) -> fetch_assoc();
	
	$sql = "
		SELECT *
		FROM `user`
		WHERE email = '{$_COOKIE['user_email']}'";

	$seller_db = $mysqli -> query($sql) -> fetch_assoc();

	$today = date("Y-m");

	$sql = "
	SELECT
		DISTINCT(`pay_num`)
	FROM
		`sales_record`
	WHERE
		`customer_id` = {$user_db['id']} AND `time` LIKE '{$today}%'
	ORDER BY
		time DESC";
	
	$paynum_list = $mysqli -> query($sql);
	$return_sales = "";
	$return_refund = "";
	$cnt = 0;
	
	while ($paynum = $paynum_list -> fetch_assoc()) {
		
		$sql = "
		SELECT
			SUM(`goods_num`) AS new_goods_num,
			goods_num AS old_goods_num,
			sr.time,
			g.name,
			sr.goods_price,
			sr.pay_num,
			u.name AS seller

		FROM `sales_record` AS sr

		JOIN `goods` AS g
		
		JOIN `user` AS u
		
		WHERE sr.pay_num = '{$paynum['pay_num']}'
		  AND g.id = sr.goods_id
		  AND sr.seller_id = u.id
		  
		GROUP BY
			`goods_id`
			";
		
		$sales_list = $mysqli -> query($sql);
		
		while ($sales_row = $sales_list -> fetch_assoc()) {
			$return_sales .= "
			<tr>
				<td class='w3-border w3-container'>{$sales_row['time']}</td>
				<td class='w3-border w3-container'>{$sales_row['name']}</td>
				<td class='w3-border w3-container'>{$sales_row['new_goods_num']}</td>
				<td class='w3-border w3-container'>{$sales_row['old_goods_num']}</td>
				<td class='w3-border w3-container'>{$sales_row['goods_price']}원</td>
				<td class='w3-border w3-container'>{$sales_row['seller']}</td>
				<td class='w3-border w3-container'>
					<button class='w3-button w3-border' onclick='addRefund(event)'>
						+
						<input type='hidden' name='time' value='{$sales_row['time']}' />
						<input type='hidden' name='goods_name' value='{$sales_row['name']}' />
						<input type='hidden' name='new_goods_num' value='{$sales_row['new_goods_num']}' />
						<input type='hidden' name='goods_price' value='{$sales_row['goods_price']}' />
						<input type='hidden' name='customer_name' value='{$sales_row['seller']}' />
						<input type='hidden' name='nth' value='{$cnt}' />
						<input type='hidden' name='pay_num' value='{$sales_row['pay_num']}' />
					</button>
				</td>
			</tr>
			";
			$cnt += 1;
		}
		
	}
	
	$mysqli -> close();
	return array($return_sales, $return_refund);
}

function initRefund() {
	checkAuthRefund();
	global $refund_table;
	$refund_table = printRefundTable();
}

initRefund();

?>

<div id='refund-container' class='center-container-column'>
	<div class='display_flex'>
		<div>
			<h3>
				<?php echo $_POST['name'] . "님의 ";?>구매 상품
			</h3>
			<table id="refund-sales-table" class='w3-table my_info'>
				<tr class='table_first'>
					<td class='w3-border w3-container'>날짜</td>
					<td class='w3-border w3-container'>상품명</td>
					<td class='w3-border w3-container'>환불가능수량</td>
					<td class='w3-border w3-container'>구매수량</td>
					<td class='w3-border w3-container'>단가</td>
					<td class='w3-border w3-container'>판매자</td>
					<td class='w3-border w3-container'>추가</td>
				</tr>
				<?php
					echo $refund_table[0];
				?>
			</table>
		</div>
		<div>
			<div class='display_flex'>
				<h3>
					환불할 상품
				</h3>
				<div id='refund_or_no_button'>
					<button class='w3-button w3-border' onclick="submitRefund()">환불</button>
					<button class='w3-button w3-border' onclick="window.location.href = './index.php?id=searchUser&URL=check_face_sell';">취소</button>
				</div>
			</div>
			<table id="refund-refund-table" class='w3-table my_info'>	
				<tr class='table_first'>
					<td class='w3-border w3-container'>날짜</td>
					<td class='w3-border w3-container'>상품명</td>
					<td class='w3-border w3-container'>수량</td>
					<td class='w3-border w3-container'>단가</td>
					<td class='w3-border w3-container'>판매자</td>
					<td class='w3-border w3-container'>제거</td>
				</tr>
				<?php
					echo $refund_table[1];
				?>
			</table>
		</div>
	</div>
	
	<form action="./index.php?id=refund_mysql" method="POST" class="w3-hide">
		<input type="hidden" name="refund_list" />
		
		<input type="hidden" name="customer_email" value="<?php echo $_POST['email']?>" />
	</form>
</div>	
<script src="./assets/js/refund.js"></script>