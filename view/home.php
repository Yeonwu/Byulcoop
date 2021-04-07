<!-- 홈 화면 

매점 협동 조합 판매자 사이트 디자인 1, 3페이지 참고

-->

<?php

require_once "./view/user_auth.php";

$auth = getAuth();

?>

<div id="home-container">
	<?php
	if ( $auth === "판매자" || $auth === "팀장" || $auth === "관리자") {
		require "./view/seller_home.php";
	}
	require "./view/product.php";
	?>
</div>
