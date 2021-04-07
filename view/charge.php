
<style>
	#pic {width:150px;}
	#grade {width:100px;} 
	#name {width:150px;}
	#email {width:300px;}
	#charge-searchBar{margin-top:-40px; margin-bottom:10px; display:inline-flex;}
	#charge-searchTxt{width:700px; height:40px;}
	#charge-searchBtn{margin-left:2px; padding:4px; width:80px; height:40px;
	text-align:right;}
</style>
<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' ) {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>
<?php
		$sql = mysqli_connect("localhost:3306", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
		mysqli_set_charset($sql, 'utf8');
		$const = "SELECT id,image,grade,name,email FROM user WHERE deleted = 0 ORDER BY grade, name";
		$asi = mysqli_query($sql, $const);
		$list = '';
		require "./view/user_account.php";
		 while ($result = mysqli_fetch_array($asi)) {
			 $account = get_user_account($result['email']);
			 $list = $list."
							<tr>
								<td><img src='./assets/img/{$result['image']}' width='200px' height='150px'></td>
								<td>{$result['grade']}</td>
								<td>{$result['name']}</td>
								<td>{$result['email']}</td>
								<td>{$account}원</td>
								<td>
									<form action='./index.php?id=charge_check' method='POST'>
										<input name='grade' value='{$result['grade']}' type='hidden'>
										<input name='name' value='{$result['name']}' type='hidden'>
										<input name='email' value='{$result['email']}' type='hidden'>
										<input name='customer_id' value='{$result['id']}' type='hidden'>
										<input type='submit' value='충전진행' class='w3-container center-container' onclick='submitToChargeCheck(event)'>
									</form>
								</td>
							</tr>";	
		 }	
?>	
	<div id="charge-container" class="center-container-column" >
		<div id="charge-searchBar" class="w3-bar center-container">
			<input type="text" 
				   placeholder="검색어를 입력하세요" 
				   id="charge-searchTxt" 
				   autocomplete="off" />
			<div type="button" 
				 id="charge-searchBtn" 
				 class = "w3-light-gray">
			<i class="fas fa-search w3-center"></i>
			</div>
		</div>
		
		<table id="charge-table" class="w3-table w3-border">
			<thead>
				<tr>
					<th class="w3-border w3-container" id="pic">소비자 사진</th>
					<th class="w3-border w3-container" id="grade">학년</th>
					<th class="w3-border w3-container" id="name">이름</th>
					<th class="w3-border w3-container" id="email">이메일</th>
					<th class="w3-container center container" id="button">잔액</th>
					<th class="w3-container center container" id="button">충전 진행 버튼</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $list; ?>
			</tbody>
		</table>
	</div>

	<script src="./assets/js/charge.js"></script>