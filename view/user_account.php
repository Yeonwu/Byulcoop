<!-- 회원 계좌 관련 php 함수 파일 -->

<?php
// sales_record 테이블에서 특정 회원의 소비량의 총합을 가져오는 함수
// get_sell('대상 이메일', '범위', '특정 날짜')
// 대상 이메일: 회원의 이메일
// 범위: 'day'(오늘), 'week'(이번주), 'weekday'(주말 제외한 이번주), 'month'(이번달), 'year'(이번년도) 가능.
// 특정 날짜: YYYY-MM-DD 형식의 날짜를 넣으면 그 날의 소비량을 가져옴.

function get_sell($user_email = -1, $range, $date = 0 ) {
	 
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
		
	if ( $user_email === -1 ) {
		$user_email = $_COOKIE['user_email'];
	}
	
	$user_id = $mysqli -> query("SELECT id 
								 FROM `user` 
								 WHERE email = '{$user_email}';")
					   -> fetch_assoc()['id'];
	
	if ($date === 0) {
		switch ( $range ) {
			case 'day':
				$date = date('Y-m-d');
				break;

			case 'month':
				$date = date('Y-m');
				break;
				
			case 'week':
				$date = 'week';
				break;
				
			case 'weekday':
				$date = 'weekday';
				break;

			case 'all':
				$date = "2";
				break;

			default:
				$date = date('Y-m-d');
				break;

		}
	}
	
	
	if ( $date === 'week' ) {
		
		$sell_result = $mysqli -> query("SELECT id, time, goods_num, goods_price 
										 FROM `sales_record` 
										 WHERE customer_id = {$user_id};");
		
		$sum = 0;
		
		if($sell_result) {
			$thisWeek = date('W', strtotime(date('Y-m-d')));

			while ( $row = $sell_result -> fetch_assoc() ) {

				if ( date('W', strtotime($row['time'])) === $thisWeek ) {
					$sum += ( (int)$row['goods_num'] ) * $row['goods_price'];
				}

			}
		}
		
		if($sum < 0) $sum = 0;
		
	} else if ( $date === 'weekday' ) {
		
		$sell_result = $mysqli -> query("SELECT sr.id, sr.time, sr.goods_num, sr.goods_price 
										 FROM `sales_record` AS sr
										 JOIN `goods` AS g
										 ON g.id = sr.goods_id
										 WHERE sr.customer_id = {$user_id} AND g.limit_include = TRUE;");
		
		$sum = 0;
		
		if($sell_result) {
			$thisWeek = date('W', strtotime(date('Y-m-d')));

			while ( $row = $sell_result -> fetch_assoc() ) {

				$tmpday = date('w', strtotime($row['time']));

				$isWeekday = ($tmpday == 0 || $tmpday == 6) ? 0 : 1;

				if ( date('W', strtotime($row['time'])) === $thisWeek && $isWeekday) {
					$sum += ( (int)$row['goods_num'] ) * $row['goods_price'];
				}

			}
		}
		
		if($sum < 0) $sum = 0;
		
	} else {
		
		$sell_result = $mysqli -> query("SELECT goods_num, goods_price 
										 FROM `sales_record` 
										 WHERE time REGEXP '^{$date}'
										 AND customer_id = {$user_id}");
		
		$sum = 0;
		
		if($sell_result) {
			while ( $row = $sell_result -> fetch_assoc() ) {
				$sum += ( (int)$row['goods_num'] ) * $row['goods_price'];
			}
		}
		
		if($sum < 0) $sum = 0;
		
	}
	
	$mysqli -> close();
	
	return $sum;
}


// charge_record 테이블에서 특정 회원의 소비량의 총합을 가져오는 함수
// get_charge('대상 이메일', '범위', '특정 날짜')
// 대상 이메일: 회원의 이메일
// 범위: 'day'(오늘), 'month'(이번달), 'year'(이번년도) 가능.
// 특정 날짜: YYYY-MM-DD 형식의 날짜를 넣으면 그 날의 소비량을 가져옴.

function get_charge($user_email = -1, $range, $date = 0) {
	 
	   
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}
	
	if ( $user_email === -1 ) {
		$user_email = $_COOKIE['user_email'];
	}
	
	$user_id = $mysqli -> query("SELECT id 
								 FROM `user` 
								 WHERE email = '{$user_email}';")
					   -> fetch_assoc()['id'];
	
	
	if ( $date === 0 ) {
		switch ( $range ) {
			case 'day':
				$date = date('Y-m-d');
				break;

			case 'month':
				$date = date('Y-m');
				break;

			case 'all':
				$date = "2";
				break;

			default:
				$date = "0000-00-00";
				break;

		}
	}
	
	$sell_result = $mysqli -> query("SELECT money 
									 FROM `charge_record` 
									 WHERE time REGEXP '^{$date}'
									 AND customer_id = '{$user_id}'");
	
	$sum = 0;
	
	if($sell_result) {
		while ( $row = $sell_result -> fetch_assoc() ) {
			$sum += (int)$row['money'];
		}
	}
	
	$mysqli -> close();
	
	return $sum;
}


// 특정 회원의 잔액을 불러오는 함수
// get_user_account('대상 이메일', '특정 날짜')
// 대상 이메일: 회원의 이메일
// 특정 날짜: YYYY-MM-DD 형식으로 넣으면 그 날의 잔액 불러옴. 기본값은 오늘.
function get_user_account($user_email = -1, $date = 0) {
	
	$user_money_sum = get_charge($user_email, 'all', $date) - get_sell($user_email, 'all', $date);
	
	return $user_money_sum;
}
?>