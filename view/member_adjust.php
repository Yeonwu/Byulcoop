<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' || $auth === '판매자') {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>
<div id='member-resister-adjust'>
	<div class='w3-bar center-container'>
		<div class='w3-button' onclick='adjustSubmit()'>확인</div>
		<a class='w3-button' href='./index.php?id=member'>
			취소
		</a>
		<form action='./index.php?id=member&type=adjust' method='POST' enctype="multipart/form-data">
			 <input type='hidden' name='numberOfData'>
			 <input type='hidden' name='userInfo'>
		</form>
	</div>
	<div class='center-container-column'>
			<?php
				$info_len = (int)$_POST['numberOfData'];
				
				$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
				mysqli_set_charset($mysqli, 'utf8');
				
				// Check connection
				if ($mysqli -> connect_errno) {
				  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
				  exit();
				}
				
				for($i = 0; $i < $info_len; $i++) {
					$user_id = json_decode($_POST['userInfo']);
					$result = $mysqli -> query("SELECT * FROM `user` WHERE id='{$user_id[$i]}';");
					$row = $result -> fetch_assoc();
					
					$user_name = "{$row['name']}";
					$user_img =  "./assets/img/{$row['image']}";
					$user_grade = "{$row['grade']}";
					$user_auth = "{$row['auth']}";
					$user_email = "{$row['email']}";
					$user_birth = "{$row['birth']}";
					
					echo "
					<div class='member-resister-formset'>
					<div class='w3-display-container'>
						<img
							src='{$user_img}'
							width='200px'
							height='150px'
							class='resister_img'
						/>
						<div class='filebox'>
							<label class='w3-button w3-display-hover w3-black w3-display-middle'>
								<input type='file' name='img[]' class='w3-hide' accept='image/*' onchange='setThumbnail(event);'>
								업로드
							</label>
						</div>
					</div>
					<div class='member-resister-textboxes'>
						<div class='center-container-row'>
							<input type='hidden' class='user_id' value='{$user_id[$i]}'>
							<input type='text' class='member-resister-textbox user_nm' placeholder='이름' value= '{$user_name}' />
							<select type='text' class='member-resister-textbox user_grade'>
								<option value='{$user_grade}'>{$user_grade}(기본)</option>
								<option value='6학년'>6학년</option>
								<option value='7학년'>7학년</option>
								<option value='8학년'>8학년</option>
								<option value='9학년'>9학년</option>
								<option value='10학년'>10학년</option>
								<option value='11학년'>11학년</option>
								<option value='12학년'>12학년</option>
								<option value='선생님'>선생님</option>
							</select>
							<input type='text' class='member-resister-textbox user_auth' placeholder='권한' value= '{$user_auth}'/>
						</div>
						<input type='text' class='w3-bar user_email' placeholder='이메일' value= '{$user_email}'/>
						<input type='date' class='w3-bar user_birth' placeholder='생년월일' value= '{$user_birth}'/>
					</div>
				</div>";
				}
			?>
	</div>
</div>

<script src='./assets/js/member_adjust.js'></script>