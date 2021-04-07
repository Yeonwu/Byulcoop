<?php

require_once ("./view/user_auth.php");

$auth = getAuth();

?>
<?php 

if( isset($_GET['type']) ) {
	switch ($_GET['type']) {
		case 'adjust':
			$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			mysqli_set_charset($mysqli, 'utf8');

			// Check connection
			if ($mysqli -> connect_errno) {
			  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			  exit();
			}
			
			// upload file
			$isThereImg = true;
			if( isset($_FILES['img']) && $_FILES['img']['name'] != "" &&  $_FILES['img']['error'] === UPLOAD_ERR_OK ) {
				$file = $_FILES['img'];

				$upload_directory = CONF_DIR['img'];
				$ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx";

				$allowed_extensions = explode(',', $ext_str);
				$max_file_size = 5242880;
				$ext = substr($file['name'], strrpos($file['name'], '.') + 1);

				// 확장자 체크
				if(!in_array($ext, $allowed_extensions)) {
					echo "업로드할 수 없는 확장자 입니다.";
				}

				// 파일 크기 체크
				if($file['size'] >= $max_file_size) {
					echo "5MB 까지만 업로드 가능합니다.";
				}
				$path = date("YmdHis") . md5(microtime()) . '.' . $ext;

				move_uploaded_file($file['tmp_name'], $upload_directory.$path);
			} else {
				$isThereImg = false;
			}
			
			$limit_include = isset($_POST['li']) ? 1 : 0;
			if ($isThereImg) {
				unlink(
						CONF_DIR['img'] . $mysqli 
						   -> query("SELECT image 
									 FROM `goods` 
									 WHERE name = '{$_POST['pn']}';")
						   -> fetch_assoc()['image']
					   );
				
				$mysqli -> query("UPDATE `goods`
								  SET
								  name = '{$_POST['pn']}',
								  type = '{$_POST['op']}',
								  image = '{$path}',
								  stock = '{$_POST['nb']}',
								  member_price = '{$_POST['mp']}',
								  general_price = '{$_POST['up']}',
								  limit_include = '{$limit_include}'
								  WHERE id = {$_POST['goods_id']};");
			} else {
				$mysqli -> query("UPDATE `goods`
								  SET
								  name = '{$_POST['pn']}',
								  type = '{$_POST['op']}',
								  stock = '{$_POST['nb']}',
								  member_price = '{$_POST['mp']}',
								  general_price = '{$_POST['up']}',
								  limit_include = '{$limit_include}'
								  WHERE id = {$_POST['goods_id']};");
			}
			break;
		case 'delete':
			$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			mysqli_set_charset($mysqli, 'utf8');

			// Check connection
			if ($mysqli -> connect_errno) {
			  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			  exit();
			}
			
			$target_product = $_POST['product_id'];
			unlink(
					CONF_DIR['img'] . $mysqli 
					   -> query("SELECT image 
								 FROM `goods` 
								 WHERE id = {$target_product};")
					   -> fetch_assoc()['image']
			);
				  
			$deleteQuery = "UPDATE `goods` SET deleted = 1 WHERE id='{$target_product}';";
			
			$mysqli -> query($deleteQuery);
			$mysqli -> close();
			break;
		case 'stock':
			
			// connet mysql
			$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			mysqli_set_charset($mysqli, 'utf8');

			// Check connection
			if ($mysqli -> connect_errno) {
			  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			  exit();
			}
			
			// get product name & stock for update
			$target_product = $_POST['product_id'];
			$updated_stock = (int) $_POST['product_stock'];
			
			// get old stock data for message
			$old_stock = (int) $mysqli
				-> query("SELECT stock 
						  FROM `goods` 
						  WHERE id = '{$target_product}';")
				-> fetch_assoc()['stock'];
			
			// update stock
			$update_query = "UPDATE `goods`
							 SET stock = '{$updated_stock}'
							 WHERE id = '{$target_product}';";
			
			if ( $mysqli -> query( $update_query ) ) {
				echo "<script>
				alert({$updated_stock} - {$old_stock} + '개의 {$_POST['product_nm']} 재고를 수정하였습니다.');
				window.location.href = './index.php?id=product';
				</script>";
			} else {
				echo "<script>
				alert('실패');
				window.location.href = './index.php?id=product';
				</script>";
			}
			
			// get user id
			$user_id = $mysqli -> query("SELECT `id` FROM `user` WHERE `email` = '{$_COOKIE['user_email']}'")
							   -> fetch_assoc()['id'];
			
			// set $updated_stock - $old_stock
			$stock_sub = $updated_stock - $old_stock;
			
			// make a record in stock_record
			$insert_query = "INSERT INTO `stock_record` 
							 VALUES(
							 	NULL,
							 	{$user_id},
							 	NOW(),
							 	{$target_product},
							 	{$stock_sub});";
			echo $insert_query;
			$mysqli -> query($insert_query);
			$mysqli -> close();
		default:
			break;
	}
}



	$sql = mysqli_connect("localhost:3306", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($sql, 'utf8');
	$const = "SELECT * FROM goods WHERE deleted = 0 ORDER BY type, general_price";
	$var = mysqli_query($sql, $const);
	$list ='';
	if($auth === "관리자" || $auth === "팀장") {
		$list = "
		<div class='product-content w3-display-container'>
			<a title='상품추가' href='./index.php?id=product_register'  class='w3-button w3-card w3-gray w3-xlarge w3-display-middle'>
				+
			</a>
		</div>";
		
		while ($result = mysqli_fetch_array($var)) {
			if($result['limit_include']) {
				$limit = "<i class='las la-check w3-text-green'></i>";
			} else {
				$limit = "<i class='las la-times w3-text-red'></i>";
			}
			$list = $list."
				<div class='product-content center-container-column w3-round c-white'>

					<div class='w3-display-container'>
						<img src='./assets/img/{$result['image']}' class='w3-display-middle' width='200px' height='200px'>
						<i title='상품삭제' class='fas fa-times custom-button w3-display-topleft' onclick='deleteProduct(event)'></i>
						<i title='상품정보 수정' class='far fa-edit custom-button w3-display-topright' onclick='adjustProduct(event)'></i>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							상품명  
						</div>
						<div class='w3-twothird'>
							<span class='product-name'>{$result['name']}</span>
						</div>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							상품종류
						</div>
						<div class='w3-twothird'>
							<span class='product-type'>{$result['type']}</span>
						</div>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							재고량
						</div>
						<div class='w3-twothird'>
							<span class='product-stock'>{$result['stock']}</span>
							<i title='재고량 수정' class='far fa-edit custom-button' onclick='editStock(event)'></i>
						</div>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							조합원가
						</div>
						<div class='w3-twothird'>
							<span class='product-price-member'>{$result['member_price']}원</span>
						</div>
					</div>

					<div class='w3-row w3-bar'>
						<div class='w3-third'>
							일반가
						</div>
						<div class='w3-twothird'>
							<span class='product-general-price'>{$result['general_price']}원</span>
						</div>
					</div>
					
					<div class='w3-row w3-bar'>
						<div class='w3-third'>
							한도 포함
						</div>
						<div class='w3-twothird'>
							{$limit}
						</div>
					</div>

					<div class='product-id w3-hide'>{$result['id']}</div>
				</div>";
		}
	} else {
		while ($result = mysqli_fetch_array($var)) {
			$list = $list."
				<div class='product-content center-container-column w3-round c-white'>

					<div class='w3-display-container'>
						<img src='./assets/img/{$result['image']}' class='w3-display-middle' width='200px' height='200px'>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							상품명
						</div>
						<div class='w3-twothird'>
							<span class='product-name'>{$result['name']}</span>
						</div>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							재고량
						</div>
						<div class='w3-twothird'>
							<span class='product-stock'>{$result['stock']}</span>
						</div>
					</div>

					<div class='w3-row w3-bar w3-border-bottom'>
						<div class='w3-third'>
							조합원가
						</div>
						<div class='w3-twothird'>
							<span class='product-price-member'>{$result['member_price']}원</span>
						</div>
					</div>

					<div class='w3-row w3-bar'>
						<div class='w3-third'>
							일반가
						</div>
						<div class='w3-twothird'>
							<span class='product-general-price'>{$result['general_price']}원</span>
						</div>
					</div>

					<div class='product-id w3-hide'>{$result['id']}</div>
				</div>";
		}
	}
	
?>

<div id="product-container">
	
	<?php
	if($auth === "관리자" || $auth === "팀장"){
		echo "<form id='deleteForm' action='./index.php?id=product&type=delete' method='POST'>
			<input type='hidden' name='product_id'>
		</form>

		<form id='adjustForm' action='./index.php?id=product_adjust' method='POST'>
			<input type='hidden' name='product_id'>
		</form>

		<form id='stockForm' action='./index.php?id=product&type=stock' method='POST'>
			<input type='hidden' name='product_id'>
			<input type='hidden' name='product_stock'>
			<input type='hidden' name='product_nm'>
		</form>";
	}
	?>
	
	<div id='product-topbar' class="center-container">
		<input 
			type="text" 
			placeholder="검색어를 입력하세요" 
			name="value" 
			autocomplete="off" 
			class="w3-bar custom-input1 w3-padding b-light-gray fb-theme w3-round"
		/>
	</div>
	
	<div id="product-content-wrap">
		<?php echo $list; ?>
	</div>
	
</div>
<?php
if($auth === "관리자" || $auth === "팀장") {
	echo '<script src="./assets/js/product.js"></script>';
} else {
	echo '<script src="./assets/js/product_home.js"></script>';
}
?>