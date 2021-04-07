<style>
	#grade {
		width: 100px;
		position: right;
	}
	#name {
		width: 150px;
		position: right;
	}
	#email {
		width: 300px;
		position: right;
	}
	#charge_cost {
		width: 500px;
		height: 40px;
	}
	tr {
		text-align: right;
	}
	.data {
		position: center;
	}
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

$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$sid = $mysqli 
	-> query( "SELECT `id` FROM `user` WHERE email = '{$_COOKIE['user_email']}';" ) 
	-> fetch_assoc()['id'];

$mysqli -> close();

?>

<div id="charge-check-container" class="center-container-column">
	<div id="charge-table-wrap">
		<table id="charge-table" class="w3-table">
			<tr id="charge-table-head">
				<td>
					학년:
					<?php echo $_POST['grade']; ?>
				</td>
				<td>
					이름:
					<?php echo $_POST['name']; ?>
				</td>
				<td>
					이메일:
					<?php echo $_POST['email']; ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="margin-b-12">
		<form action="./index.php?id=charge_check_mysql" method="POST">
			<div class="center">
			<input
				type="number"
				class="charge-check-textbox charge-cost w3-bar custom-input1 w3-padding b-light-gray fb-theme w3-round"
				id="charge_cost"
				name="cost"
				placeholder="충전금액"
			/><br />
			<select onclick="selfInput(event)" style="height:40px;" class="custom-input1 w3-padding b-light-gray fb-theme w3-round">
			<option value="일반충전">일반충전</option>
			<option value="쿠폰" onclick="selfInput(event)">쿠폰</option>
			<option value="직접입력" onclick="selfInput(event)">직접입력</option>
			</select>
			</div>
			<input
				type="text"
				class="charge-check-textbox w3-hide w3-bar custom-input1 w3-padding b-light-gray fb-theme w3-round"
				id="charge_description"
				name="etc"
				placeholder="비고"
				value="일반충전"
			/>
			<script>
			
			function selfInput(event) {
				let select = event.target.parentNode;
				console.log(event.target);
				if(event.target.value == "직접입력") {
					document.querySelector("#charge_description").classList.remove("w3-hide");
					return;
				}
				
				document.querySelector("#charge_description").classList.add("w3-hide");
				document.querySelector("#charge_description").value = event.target.value;
				
			}
			
			</script>
			<?php 
				echo "<input type='hidden' value={$_POST['email']} name='email'>";
			?>
		</form>
	</div>

	<div id="charge-check-container" class="flex col margin-b-12">
		<div id="charge-myInfo-name"></div>
		<div id="charge-myInfo-email"></div>
	</div>

	<div>
		<input
			id="register-submit"
			class="w3-center c-theme-dark w3-button hover-active w3-margin-right w3-round"
			value="결제"
			type="submit"
			onclick="chargeSubmit()"
		/>
		<input
			onclick="location.href='./index.php?id=searchUser&URL=check_face_charge';"
			type="button"
			value="취소"
			class="w3-center w3-red w3-button hover-active w3-margin-right w3-round"
		/>
	</div>
	<script src="./assets/js/charge_check.js"></script>
</div>