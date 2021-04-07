<!--판매 페이지-->
<?php

require_once "./view/user_auth.php";

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
<style>
</style>
<div id="sell-container" class="flex col">
	<!-- 상품목록 -->
	<?php
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');
	require_once "./view/user_account.php";
	?>
	
	<h1 id="product-type-snack" style="margin-top:0px;" class="sell_tag">
		과자
	</h1>
	<div class="sell_grid">
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='과자' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/".$row['image']." style='width:100%'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>
	</div>
	
	<br>
	
	<h1 id="product-type-icecream" class="sell_tag">
		아이스크림
	</h1>
	<br>
	<div class="sell_grid">
	
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='아이스크림' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/".$row['image']." style='width:100%'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>
		
	</div>
	<br>
	<h1 class="sell_tag" id="product-type-drink">
		음료
	</h1>
	<br>
	<div class="sell_grid">
		
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='음료' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/".$row['image']." style='width:100%'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>
				
	</div>
	<br>
	<h1 id="product-type-jelly" class="sell_tag">
		젤리
	</h1>
	<br>
	<div class="sell_grid">
	
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='젤리' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/".$row['image']." style='width:100%'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>	
	</div>
	
	<br>
	<h1 id="product-type-season" class="sell_tag">
		시즌메뉴
	</h1>
	<br>
	<div class="sell_grid">
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='시즌메뉴' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/{$row['image']} style='width:100%'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>
	</div>
	
	<br>
	
	<h1 id="product-type-daily_item" class="sell_tag">
		생활용품
	</h1>
	<br>
	<div class="sell_grid last">
		<?php
			$result = mysqli_query($mysqli, "SELECT * FROM goods WHERE type='생활용품' AND deleted = 0");
			while($row=mysqli_fetch_assoc($result)){
			if($_POST['auth']=='소비자')
			{$price=$row['general_price'];}
				else {$price=$row['member_price'];}
			echo "<div class='w3-display-container'>
    				<img src=./assets/img/{$row['image']} style='width:100%;'>			
					<span class='w3-tag w3-display-topleft'>".$row['stock']."</span>
        			<div class='sell_bt w3-display-hover'>
    					<button class='w3-button w3-black' onclick='addSellList(event)'>추가</button>   
         		 	</div>
					<p>".$row['name']."</p>
					<p>".$price."원</p>	
				</div>";}
		?>
	</div>
	
	<!-- 상품종류 선택 -->
	<div id="choose_type">
		<a href="#product-type-snack" class="w3-button w3-black" onclick="productTypeChange(event)">과자</a>
		<a href="#product-type-icecream" class="w3-button w3-black" onclick="productTypeChange(event)">아이스크림</a>
		<a href="#product-type-drink" class="w3-button w3-black" onclick="productTypeChange(event)">음료</a>
		<a href="#product-type-jelly" class="w3-button w3-black" onclick="productTypeChange(event)">젤리</a>
		<a href="#product-type-season" class="w3-button w3-black" onclick="productTypeChange(event)">시즌메뉴</a>
		<a href="#product-type-daily_item" class="w3-button w3-black" onclick="productTypeChange(event)">생활용품</a>
	</div>
	<!-- 우측 주문판 -->
	<div id="buy_info" class="w3-card-2 w3-container">
		<form action="./index.php?id=sell_send" method="post">

			<div class="w3-display-contianer" style="">
				<input type="hidden" name="json_data">
				<input type="hidden" name="length">

				<!-- ?학년 ???의 주문목록 -->
				<div class="w3-display-topmiddle w3-bar w3-container absolute">
					<div class="w3-button ht-theme absolute" style="top: 0; right: 0;" onclick="toggleBuyInfo();">
						<i class="las la-angle-down"></i>
					</div>
					<div>	
						<?php
							echo "<p>".$_POST['grade']." ".$_POST['name']."의 주문목록</p>
								  (잔금: " . get_user_account($_POST['email']) . "원)";
						?>
					</div>

					<!-- 주문메뉴 -->
					<table id="buy_info_container" class="w3-table" draggable="true">
						<td style="width: 140px;">총액<span id="sell-list-sum" class="w3-margin-left">0원</span></td>
						<td style="width: px;"></td>
						<td style="width: px;"></td>
						<td style="width: px;"></td>
					</table>
				</div>

				<!-- 확인 취소 -->
				<div class="w3-display-bottommiddle w3-bar">
					<div id="buy_limitOnOff">
						<p style="line-height: 34px;">
							<label class="switch">
								<input type="checkbox" name="limitOn" />
								<span class="slider round"></span>
							</label>
							구매한도 확인 안함
						</p>
					</div>
					
					<div id="buy_ck" class="w3-bar">
						<input class="w3-bar-item w3-button w3-border" type="button" value="확인" onclick="submitSell()">
						<a class="w3-bar-item w3-button w3-border" href='./index.php?id=searchUser&URL=check_face_sell'>취소</a>
					</div>
				</div>
			
			</div>
			<?php
			echo  "	<input type='hidden' name='customer' value='" . $_POST['email'] . "'>
					<input type='hidden' name='seller' value='" . $_COOKIE['user_email'] . "'>
					<input name='email' type='hidden' value=" . $_POST['email'] . ">";
			?>
		</form>
	</div>
	
	
	<script src="./assets/js/sell.js"></script>
	<script>
		function toggleBuyInfo() {
			document.querySelector('#buy_info').classList.toggle('closed');
		}
	</script>
</div>
