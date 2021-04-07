<head>
	<style>
		#label {
			float: left;
			display: inline-flex;
			margin-left: 300px;
			width: 250px;
			height: 250px;
		}

		#check {
			margin-bottom: 110px;
			margin-left: 30px;
		}
		#cancel {
			margin-top: -110px;
			margin-left: 0px;
		}
	</style>
</head>
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
	<?php
$db_conn = mysqli_connect("localhost:3306", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
if(isset($_FILES['upfile']) && $_FILES['upfile']['name'] != "") {
    $file = $_FILES['upfile'];
    $upload_directory = 'data/';
    $ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";
    $allowed_extensions = explode(',', $ext_str);
    $max_file_size = 5242880;
    $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
    // 확장자 체크
    if(!in_array($ext, $allowed_extensions)) {
        echo "업로드할 수 없는 파일 형태입니다.";
    }
    // 파일 크기 체크
    if($file['size'] >= $max_file_size) {
        echo "5MB 까지만 업로드 가능합니다.";
    }
} else {
    echo "";
}

mysqli_close($db_conn);
?>

<div id="products" class="container-row">
	<form
		action="./index.php?id=product_register_mysql"
		name="imgform"
		method="post"
		enctype="multipart/form-data"
		onsubmit="return formSubmit(this);"
	>
		<div id="label" class="w3-display-container">
			<img
				src="https://tseriesracing.files.wordpress.com/2014/12/placeholder-400x300.jpg"
				width="250px"
				height="100%"
				class="resister_img"
			/>
			<div class="filebox">
				<label class="w3-button w3-display-hover w3-black w3-display-middle">
					<input type="file" name="img" class="w3-hide" accept="image/*" onchange="setThumbnail(event);">
					업로드
				</label>
			</div>
		</div>

		<select id="op" name="op" style="margin-top: -200px;" placeholder="종류를 선택하세요">
			<option value="과자">과자</option>
			<option value="아이스크림">아이스크림</option>
			<option value="음료">음료</option>
			<option value="젤리">젤리</option>
			<option value="시즌메뉴">시즌메뉴</option>
			<option value="생활용품">생활용품</option>
		</select>
		<p><input type="text" name="pn" autocomplete="off" id="pn" placeholder="상품 이름"></p>
		<p>
			<input type="text" name="nb" autocomplete="off" id="nb" placeholder="수량">
		</p>
		<p>
			<input type="text" name="mp" autocomplete="off" id="mp" placeholder="조합원가">
		</p>
		<p>
			<input type="text" name="up" autocomplete="off" id="up" placeholder="일반 소비자가">
		</p>
		<input type="submit" class="w3-button w3-border" value="확인" id="check">
		<a id="cancel" class="w3-button w3-border" href="./index.php?id=product">취소</a>
	</form>
</div>
<script src="./assets/js/product_register.js"></script>