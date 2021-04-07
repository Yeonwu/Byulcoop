<!--
나의 정보 페이지 ( 판매 내역 )

매점 협동 조합 판매자 사이트 디자인 5p 참고
-->
<?php
require_once "./view/user_account.php";
require_once "./view/user_auth.php";

$auth = getAuth();
$info_user_email;

// 권한 확인 -> 권한 없거나 다른 사람 정보를 보고자하는 요청이 없으면 $info_user_email에 자기 이메일 대입
// 권한이 있고 다른 사람 정보를 보고자 하는 요청이 있으면 $info_user_email에 다른 사람 이메일 대입
if(($auth === "관리자" || $auth === "팀장") && isset($_POST['email'])) {
	
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	$info_user_email = $_POST['email'];
	$info_user_name = $_POST['name'];
} else {
	$info_user_email = $_COOKIE['user_email'];
}

// info.php의 충전, 구매 환불 기록 테이블 생성후 문자열로 리턴
function print_user_record() {
	
	// 13줄에서 대입해 놓은 $info_user_email 이용
	global $info_user_email;
	
	$returnStr = "";

	
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	$user_id = $mysqli -> query("SELECT id 
								 FROM `user`
								 WHERE email = '{$info_user_email}';") 
					   -> fetch_assoc()['id'];
	
	// sales_record와 charge_record의 컬럼 수가 다르므로 아이디와 테이블 종류를 가져와서 시간순으로 정렬한 후
	// while문으로 테이블별로 id를 이용해 정보를 가공한다.
	$queryForOrder = "SELECT id, time, 'charge_record' 
					  AS 'table_name' 
					  FROM `charge_record` 
					  WHERE customer_id = {$user_id}
					  UNION 
					  SELECT id, time, 'sales_record' 
					  AS 'table_name' 
					  FROM `sales_record`
					  WHERE customer_id = {$user_id}
					  ORDER BY time;";
	
	$orderedResult = $mysqli -> query( $queryForOrder );
	
	$user_money = 0;
	
	while ( $orderRow = $orderedResult -> fetch_assoc() ) {
		
		if ( $orderRow['table_name'] === 'charge_record' ) {
			
			$result = $mysqli -> query("SELECT * 
										FROM `charge_record` 
										WHERE id = {$orderRow['id']}");
			$row = $result -> fetch_assoc();
			$update_user_money = $user_money + $row['money'];
			
			$returnStr = "
			<tr class='my-info-charge'>
				<td class='w3-container'>충전</td>
				<td class='w3-container'>{$row['time']}</td>
				<td class='w3-container'>충전</td>
				<td class='w3-container'>{$row['note']}</td>
				<td class='w3-container'>{$row['money']}원</td>
				<td class='w3-container'>{$user_money}원 >> {$update_user_money}원</td>
			</tr>" . $returnStr;
			
			$user_money = $update_user_money;
			
		} else if ( $orderRow['table_name'] === 'sales_record' ) {
			
			$result = $mysqli -> query("SELECT 
										sr.*,
										g.name AS goods_name 
										FROM `sales_record` 
										AS sr 
										JOIN goods 
										AS g 
										ON g.id = sr.goods_id 
										WHERE sr.id = {$orderRow['id']}"); // sr : sales_record  g : goods
			
			$row = $result -> fetch_assoc();
			
			$price_sum = $row['goods_num'] * $row['goods_price'];
			
			$update_user_money = $user_money - $price_sum;
			
			$price_sum = abs($price_sum);
			
			if($row['goods_num'] < 0) {
				$print_type = "환불";
				$background = "my-info-refund";
				$row['goods_num'] = abs($row['goods_num']);
			} else {
				$print_type = "구매";
				$background = "my-info-sale";
			}
			
			$returnStr = "
			<tr class='{$background}'>
				<td class='w3-container'>{$print_type}</td>
				<td class='w3-container'>{$row['time']}</td>
				<td class='w3-container'>{$row['goods_name']}</td>
				<td class='w3-container'>{$row['goods_num']}</td>
				<td class='w3-container'>{$price_sum}원</td>
				<td class='w3-container'>{$user_money}원 >> {$update_user_money}원</td>
			</tr>" . $returnStr;
			
			$user_money = $update_user_money;
			
		}
		
	}
	
	$mysqli -> close();
	
	return $returnStr;
	
}


?>


<div id="info-container">
	<h2 style="margin-top: 0px;">
		<?php
		if(isset($info_user_name)) {
			echo $info_user_name . "님의 정보";
		} else {
			echo "나의 정보";
		}
		?>
	</h2>
	<div class="my_info">
		<div class="w3-padding">
			<?php
			// user_account.php 참고
			?>
			<p>잔액: <?php echo get_user_account($info_user_email);?>원</p>
			<p>오늘 소비량: <?php echo get_sell($info_user_email, 'day');?>원</p>
			<p>이번주 소비량: <?php echo get_sell($info_user_email, 'weekday');?>원</p>
			<p>이번달 소비량: <?php echo get_sell($info_user_email, 'month');?>원</p>
			<p>누적 소비량: <?php echo get_sell($info_user_email, 'all');?>원</p>
			<p>누적 충전량: <?php echo get_charge($info_user_email, 'all');?>원</p>
		</div>
	</div>
	
	<h2>
		<?php
		if(isset($info_user_name)) {
			echo $info_user_name . "님의 충전 및 소비 기록";
		} else {
			echo "나의 충전 및 소비 기록";
		}
		?>
	</h2>
	<div id="info-pagination" class="w3-center">
		<div class="w3-bar w3-border">
			<div class="w3-bar-item w3-button" onclick="pageBefore()">&laquo;</div>
			<span>
			</span>
			<div class="w3-bar-item w3-button" onclick="pageNext()">&raquo;</div>
		</div>
	</div>
	<div class="my_info">
		<table class="w3-table w3-striped">
			<tr>
				<th class="w3-container">종류</th>
				<th class="w3-container">날짜</th>
				<th class="w3-container">내용</th>
				<th class="w3-container">수량 / 비고</th>
				<th class="w3-container">총액</th>
				<th class="w3-container">전 >> 후</th>
			</tr>
			
			<?php 
			echo print_user_record();
			?>
			
		</table>
	</div>
	<script src="./assets/js/info.js"></script>
</div>