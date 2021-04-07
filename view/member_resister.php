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
<div id="member-resister-container">
	<form method="post" action="./index.php?id=member&type=resister" enctype="multipart/form-data"></form>
	<div class="w3-bar center-container">
		<div id="add-formset-btn" class="w3-button">
			<i class="fas fa-plus"></i>
		</div>
		<div id="remove-formset-btn" class="w3-button">
			<i class="fas fa-minus"></i>
		</div>
		<input id="resister-submit" class="w3-button" value="확인" type="submit" />
		<a class="w3-button" href="./index.php?id=member">
			취소
		</a>
	</div>
	<div id="inputDiv" class="center-container-row">
		<div class="member-resister-formset">
			<div class="w3-display-container">
				<img
					src="https://tseriesracing.files.wordpress.com/2014/12/placeholder-400x300.jpg"
					width="200px"
					height="150px"
					class="resister_img"
				/>
				<div class="filebox">
					<label class="w3-button w3-display-hover w3-black w3-display-middle">
						<input type="file" name="img[]" class="w3-hide" accept="image/*" onchange="setThumbnail(event);">
						업로드
					</label>
				</div>
			</div>
			<div class="member-resister-textboxes">
				<div class="center-container-row">
					<input
						type="text"
						class="member-resister-textbox resister_name"
						placeholder="이름"
					/>
					<select type="text" class="member-resister-textbox resister_grade">
						<option value="">학년</option>
						<option value="6학년">6학년</option>
						<option value="7학년">7학년</option>
						<option value="8학년">8학년</option>
						<option value="9학년">9학년</option>
						<option value="10학년">10학년</option>
						<option value="11학년">11학년</option>
						<option value="12학년">12학년</option>
						<option value="선생님">선생님</option>
					</select>
					<input
						type="text"
						class="member-resister-textbox resister_auth"
						placeholder="권한"
					/>
				</div>
				<input type="email" class="w3-bar resister_email" placeholder="이메일" />
				<input type="date" class="w3-bar resister_birth" placeholder="생년월일" />
			</div>
		</div>
	</div>
	
</div>

<script src="./assets/js/member_resister.js"></script>