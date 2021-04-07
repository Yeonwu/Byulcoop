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
<style>
		#label {
			float: left;
			display: inline-flex;
			margin-left: 300px;
			width: 250px;
			height: 250px;
		}

		
</style>

<?php
$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

$goods = $mysqli -> query("SELECT * FROM `goods` WHERE `id` = '{$_POST['product_id']}';") -> fetch_assoc();
?>

<div id="products" class="container-row">
	<form
		action="./index.php?id=product&type=adjust"
		name="imgform"
		method="post"
		enctype="multipart/form-data"
		onsubmit="return formSubmit(this);"
	>
		<div id="label" class="w3-display-container">
			<img
				src="<?php echo "./assets/img/" . $goods['image'];?>"
				width="250px"
				height="100%"
				class="resister_img"
			/>
			<div class="filebox">
				<label class="w3-button w3-display-hover w3-black w3-display-middle">
					<input type="file" name="img" class="w3-hide" accept="image/*" onchange="setThumbnail(event);" value="null">
					업로드
				</label>
			</div>
		</div>
		
		<input type="hidden" name="goods_id" value="<?php echo $goods['id'];?>">
		<select id="op" name="op" style="margin-top: -200px;" placeholder="종류를 선택하세요">
			<?php
			
			$goods_types = ['과자', '아이스크림', '음료', '젤리', '시즌메뉴', '생활용품'];
			for ( $i = 0; $i < 6; $i++ ) {
				if ( $goods['type'] === $goods_types[$i] ) {
					echo "<option value='{$goods_types[$i]}' selected>{$goods_types[$i]}</option>";
				} else {
					echo "<option value='{$goods_types[$i]}'>{$goods_types[$i]}</option>";
				}
			}
			?>
		</select>
		<p><input type="text" name="pn" autocomplete="off" id="pn" placeholder="상품 이름" value="<?php echo $goods['name'];?>"></p>
		<p>
			<input type="hidden" name="nb" autocomplete="off" id="nb" placeholder="수량" value="<?php echo $goods['stock'];?>">
		</p>
		<p>
			<input type="text" name="mp" autocomplete="off" id="mp" placeholder="조합원가" value="<?php echo $goods['member_price'];?>">
		</p>
		<p>
			<input type="text" name="up" autocomplete="off" id="up" placeholder="일반 소비자가" value="<?php echo $goods['general_price'];?>">
		</p>
		<label>
			한도 계산 포함
			<input type="checkbox" name="li" <?php if($goods['limit_include']) echo 'checked';?>/>
		</label>
		<br>
		<div class="margin-t-12">
			<input type="submit" class="w3-button w3-border" value="확인" id="check">
			<a id="cancel" class="w3-button w3-border" href="./index.php?id=product">취소</a>
		</div>
	</form>
</div>
<script src="./assets/js/product_register.js"></script>