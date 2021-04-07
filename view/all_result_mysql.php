<?php

require_once "./config.php";
require_once "./user_auth.php";
$auth = getAuth();
if($auth == "소비자" or $auth == "조합원") {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

$DISPLAY_SALES = isset($_POST['type_sales']);
$DISPLAY_CHARGE = isset($_POST['type_charge']);
$DISPLAY_REFUND = isset($_POST['type_refund']);

$FILTER_START_DATE = isset($_POST['set-start-date']);
if($FILTER_START_DATE) {$start_date = $_POST['start-date'];}

$FILTER_END_DATE = isset($_POST['set-end-date']);
if($FILTER_END_DATE) {$end_date = $_POST['end-date'];}


$FILTER_SELLER = isset($_POST['set-seller']);
if($FILTER_SELLER) {$seller = $_POST['seller_name'];}

$FILTER_CUSTOMER = isset($_POST['set-customer']);
if($FILTER_CUSTOMER) {$customer = $_POST['customer_name'];}

$FILTER_GOODS = isset($_POST['set-goods']);
if($FILTER_GOODS) {$goods = $_POST['goods_name'];}

$FILTER_MIN_MONEY = isset($_POST['set-min-money']);
if($FILTER_MIN_MONEY) {$min_money = $_POST['min-money'];}

$FILTER_MAX_MONEY = isset($_POST['set-max-money']);
if($FILTER_MAX_MONEY) {$min_money = $_POST['max-money'];}

$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$sql = '';
$sum_sql = '';

if($DISPLAY_SALES || $DISPLAY_REFUND) {
	$sum_sql = "
		SELECT
			'판매' AS `type`,
			SUM(sr.goods_num * sr.goods_price) AS sum
		FROM `sales_record` AS sr
		JOIN `user` AS c 
			ON sr.customer_id = c.id
		JOIN `user` AS s
			ON sr.seller_id = s.id
		JOIN `goods` AS g
			ON sr.goods_id = g.id
		WHERE 
			";
	$sql = "
		SELECT
			'판매' AS `type`,
			c.name AS `customer`,
			s.name AS `seller`,
			g.name AS `goods`,
			sr.time,
			FORMAT((sr.goods_num * sr.goods_price), 0) AS `total_money`,
			FORMAT(sr.goods_num, 0),
			FORMAT(sr.goods_price, 0)
		FROM `sales_record` AS sr
		JOIN `user` AS c 
			ON sr.customer_id = c.id
		JOIN `user` AS s
			ON sr.seller_id = s.id
		JOIN `goods` AS g
			ON sr.goods_id = g.id
		WHERE 
			";
	
	$filter = '';
	if($DISPLAY_REFUND && $DISPLAY_SALES) {
		$filter .= '1 = 1';
	} else if($DISPLAY_SALES){
		$filter .= "sr.goods_num > 0";
	} else {
		$filter .= "sr.goods_num < 0";
	}
	
	if($FILTER_START_DATE) $filter .= " AND DATE_FORMAT(sr.time, '%Y-%m-%d') >= '{$start_date}'";
	if($FILTER_END_DATE) $filter .= " AND DATE_FORMAT(sr.time, '%Y-%m-%d') <= '{$end_date}'";
	if($FILTER_SELLER) $filter .= " AND s.name = '{$seller}'";
	if($FILTER_CUSTOMER) $filter .= " AND c.name = '{$customer}'";
	if($FILTER_GOODS) $filter .= " AND g.name = '{$goods}'";
	if($FILTER_MIN_MONEY) $filter .= " AND (sr.goods_num * sr.goods_price) >= '{$min_money}'";
	if($FILTER_MAX_MONEY) $filter .= " AND (sr.goods_num * sr.goods_price) <= '{$max_money}'";
	
	$sql .= $filter;
	$sum_sql .= $filter;
}

if($DISPLAY_CHARGE) {
	if($sql != '') {
		$sql .= " UNION ";
		$sum_sql .= " UNION ";
	}
	$sum_sql .= "
		SELECT 
			'충전' AS `type`,
			SUM(cr.money) AS sum
		FROM `charge_record` AS cr
		JOIN `user` AS c 
			ON cr.customer_id = c.id
		JOIN `user` AS s
			ON cr.seller_id = s.id
		WHERE 
			1 = 1";
	$sql .= "
		SELECT 
			'충전' AS `type`,
			c.name AS `customer`,
			s.name AS `seller`,
			cr.note AS `goods`,
			cr.time,
			FORMAT(cr.money, 0) AS `total_money`,
			'' AS `goods_num`,
			'' AS `goods_price`
		FROM `charge_record` AS cr
		JOIN `user` AS c 
			ON cr.customer_id = c.id
		JOIN `user` AS s
			ON cr.seller_id = s.id
		WHERE 
			1 = 1
	";
	$filter = '';
	
	if($FILTER_START_DATE) $filter .= " AND DATE_FORMAT(cr.time, '%Y-%m-%d') >= '{$start_date}'";
	if($FILTER_END_DATE) $filter .= " AND DATE_FORMAT(cr.time, '%Y-%m-%d') <= '{$end_date}'";
	if($FILTER_SELLER) $filter .= " AND s.name = '{$seller}'";
	if($FILTER_CUSTOMER) $filter .= " AND c.name = '{$customer}'";
	if($FILTER_MIN_MONEY) $filter .= " AND cr.money > '{$min_money}'";
	if($FILTER_MAX_MONEY) $filter .= " AND cr.money < '{$max_money}'";
	
	$normal = isset($_POST['charge_normal']);
	$coupon = isset($_POST['charge_coupon']);
	$etc = isset($_POST['charge_etc']);
	
	$filter .= " AND ( 1 = 0";
	if($normal) $filter .= " OR cr.note = '일반충전'";
	if($coupon) $filter .= " OR cr.note = '쿠폰'";
	if($etc) $filter .= " OR NOT(cr.note = '일반충전' OR cr.note = '쿠폰')";
	$filter .= " )";
	
	$sql .= $filter;
	$sum_sql .= $filter;
}

$sql = "{$sql} ORDER BY `time` DESC";

$result = $mysqli -> query($sql);
$sum_result = $mysqli -> query($sum_sql);
$sum_total = $sum_result -> fetch_assoc()['sum'] + $sum_result -> fetch_assoc()['sum'];


if(($DISPLAY_SALES || $DISPLAY_REFUND) && $DISPLAY_CHARGE) {
	$table_head = ['#', '종류', '소비자', '결제자', '상품/충전종류', '결제시간', '총 결제 금액', '상품 개수', '상품 개당 가격'];
} else if($DISPLAY_SALES || $DISPLAY_REFUND) {
	$table_head = ['#', '종류', '소비자', '결제자', '상품', '결제시간', '총 결제 금액', '상품 개수', '상품 개당 가격'];
} else {
	$table_head = ['#', '종류', '소비자', '결제자', '충전종류', '결제시간', '총 결제 금액'];
}

$page_num = (int)(($result -> num_rows) / $_POST['limit']);
if($result -> num_rows % $_POST['limit']) {
	$page_num += 1;
}

?>

<!-- <pre>
	<?php //echo $sum_sql;?>
</pre> -->
<label id="set-paging">
	<div>
		페이지 번호
	</div>
	<select name="offset" class="b-gray fb-theme w3-padding w3-round" onchange="handleSubmit();">
		<?php for($i = 1; $i <= $page_num; $i++) { ?>
			<option value="<?php echo $i - 1; ?>"<?php if($i == $_POST['offset'] + 1) echo 'selected'; ?>><?php echo $i; ?></option>
		<?php } ?>
	</select>
</label>

<div style="padding: 10px 10px;">
	<?php echo "총 {$result -> num_rows}개의 검색결과"?>
</div>

<table class="w3-paddig w3-table w3-striped w3-bordered">
	<tr>
		<?php foreach($table_head as $head) { ?>
			<th><?php echo $head;?></th>
		<?php } ?>
	</tr>
	<tr class="c-white">
		<?php foreach($table_head as $head) { ?>
			<?php if($head == '종류') { ?>
				<th>총계</th>
			<?php } else if($head == '총 결제 금액') { ?>
				<th><?php echo number_format($sum_total);?></th>
			<?php } else { ?>
				<th></th>
			<?php } ?>
			
		<?php } ?>
	</tr>
	<?php 
	$i = $_POST['offset'];
	$start = $_POST['offset'] * $_POST['limit'];
	if($_POST['offset'] != 0) $start += 1;
	$end = ($_POST['offset'] + 1) * $_POST['limit'];
	while($row = $result -> fetch_assoc()) { 
		$i++;
		if($i < $start) continue;
		if($i > $end) break;
	?>
			<tr>
				<td><?php echo $i;?></td>
				<?php foreach($row as $col) { ?>
					<?php if((($DISPLAY_SALES || $DISPLAY_REFUND) && $DISPLAY_CHARGE) || $col != '') { ?>
						<td><?php echo $col; ?></td>
					<?php } ?>
				<?php }?>
			</tr>
	<?php } ?>
</table>
<div class="w3-bar w3-button ht-theme" onclick="nextPage();">다음 페이지 보기</div>