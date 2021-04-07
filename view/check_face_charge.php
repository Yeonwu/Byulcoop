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

if ($_POST['image'] == "") {
	$image = "user_alt.png";
} else {
	$image = $_POST['image'];
}

?>
<div id="check_face-container" class="center-container">
	<div>
		<img alt='이미지를 로드하지 못했습니다.' src="./assets/img/<?php echo $image; ?>" height=400px width=300px>
		<div id='Grade_Name'>
			<p id='Grade_Name_txt'><?php echo "{$_POST['grade']} / {$_POST['name']} / {$_POST['auth']}"; ?></p>
		</div>
		<div id='YN'>
			<form action='./index.php?id=charge_check' method='post'>
				<input autofocus class='w3-button w3-border' id='CFP' type='submit' value='확인' style='width:100%; outline:none;'>
				<input name='name' type='hidden' value='<?php echo $_POST['name']; ?>'>
				<input name='grade' type='hidden' value='<?php echo $_POST['grade']; ?>'>
				<input name='email' type='hidden' value='<?php echo $_POST['email']; ?>'>
			</form>
		<a class="w3-button w3-border" href="./index.php?id=searchUser&URL=check_face_charge">
			취소
		</a>
		</div>
	</div>
</div>