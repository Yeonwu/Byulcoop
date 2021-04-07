<?php

require_once ("./view/user_auth.php");
require_once ("./view/user_account.php");

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
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');
?>
<div id="choose_name-container" class="center-container-column">
	<!-- 구매자 선택 -->
	<div id="serch_name" class="w3-bar center-container" style="margin-top:-40px; margin-bottom:10px; display:inline-flex;">
		<input autofocus type="text" placeholder="이름을 입력해 주세요" name="name" id="serch_name_txt" autocomplete="off" style="width:700px; height:40px;"/>
		<p id="search_name_Btn" class = "w3-button">
			<i class="fas fa-search w3-center"></i>
		</p>
	</div>
	
	<table class="w3-table">
		
		<tr class="table_first">
			<td class="w3-border w3-container">학년</td>
			<td class="w3-border w3-container">이름</td>
			<td class="w3-border w3-container">잔액</td>
			<td class="w3-border w3-container">이메일</td>
			<td class="w3-border w3-container">선택</td>
		</tr>
		
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM user WHERE deleted = 0 ORDER BY grade, name");
			while($row=mysqli_fetch_assoc($result)){
				
				$account = get_user_account($row['email']);
				echo "<tr>
						<td class='w3-border w3-container'>".$row['grade']."</td>
						<td class='w3-border w3-container'>".$row['name']."</td>
						<td class='w3-border w3-container'>".$account."원</td>
						<td class='w3-border w3-container'>".$row['email']."</td>
						<td class='w3-border w3-container' style='width: 150px'>
							<form action='./index.php?id=check_face' method='post' class='w3-cell'>
								<input name='name' type='hidden' value=".$row['name'].">
								<input name='grade' type='hidden' value=".$row['grade'].">
								<input name='auth' type='hidden' value=".$row['auth'].">
								<input name='image' type='hidden' value=".$row['image'].">
								<input name='email' type='hidden' value=".$row['email'].">
								<input class='w3-button w3-border' type='submit' value='결제'>
							</form>
							<form action='./index.php?id=refund' method='post' class='w3-cell'>
								<input name='name' type='hidden' value=".$row['name'].">
								<input name='grade' type='hidden' value=".$row['grade'].">
								<input name='auth' type='hidden' value=".$row['auth'].">
								<input name='image' type='hidden' value=".$row['image'].">
								<input name='email' type='hidden' value=".$row['email'].">
								<input class='w3-button w3-border' type='submit' value='환불'>
							</form>
						</td>
					  </tr>";
			}
		?>
	</table>
	
	<script src="./assets/js/choose_name.js"></script>
</div>	