<!-- 
nav 바
-->

<?php
require_once "./view/user_account.php";
require_once "./view/user_auth.php";

$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
mysqli_set_charset($mysqli, 'utf8');

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

if ( isset($_COOKIE['user_email']) ) {
	$user = $mysqli -> query("SELECT *
							  FROM user
							  WHERE email = '{$_COOKIE['user_email']}'") -> fetch_assoc();
	if($user['grade'] === "선생님") {
		$user_nav_name = "{$user['name']} {$user['auth']}";
	} else {
		$user_nav_name = "{$user['grade']} {$user['name']} {$user['auth']}님";
	}
	$week_sell = get_sell($user['email'], 'weekday');
} else {
	$user_nav_name = "방문자님";
}

$navAbled = 'navAbled';
$rotation = 'rotation';

if (isset($_COOKIE['nav_open'])) {
	if ($_COOKIE['nav_open'] != 'true') {
		$navAbled = '';
		$rotation = '';
	}
}

$logged_in = bwf_verify_login();
if($logged_in) {
	$user_img = "./assets/img/user_alt.png";
} else {
	$user_img = $_COOKIE['user_img'];
}

?>

<div id="nav-container" class="relative">
	<div id="nav-bar" class="w3-sidebar w3-bar-block <?php echo $navAbled; ?>">
		<div class="w3-bar-item relative">
			<a id="nav-title" href="./index.php?id=home">
				<img src="./assets/img/logo_black.png" style="width: 24px; height: 24px; margin-top: 5px;">
			</a>
			<span id="navBtn" class="center-container w3-circle custom-button">
				<i class="las la-times <?php echo $rotation; ?>"></i>
				<i class="las la-bars"></i>
			</span>
		</div>
		
		<?php
		
		if ( isset($_COOKIE['user_email']) ) {
			$user_email = $_COOKIE['user_email'];
			$sql = "SELECT auth FROM `user` WHERE email = '{$user_email}';";
			$user_auth = $mysqli -> query($sql) -> fetch_assoc()['auth'];
		
			setcookie('auth', $user_auth, time()+86400);
		} else {
			$user_auth = NULL;
		}
		
		$guest =  [NULL, '소비자', '조합원', '판매자', '팀장', '관리자'];
		$sobi =   ['소비자', '조합원', '판매자', '팀장', '관리자'];
		$johab =  ['조합원', '판매자', '팀장', '관리자'];
		$seller = ['판매자', '팀장', '관리자'];
		$team =   ['팀장', '관리자'];
		$admin =  ['관리자'];
		?>
		
		<?php if(in_array($user_auth, $guest)) { ?>
			<a id="home" href="./index.php?id=home" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-home"></i>
				<span>홈</span>
			</a>
			<a id="use" href="./index.php?id=use" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-info-circle"></i>
				<span>사용메뉴얼</span>
			</a>
			<a id="product" href="./index.php?id=product" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-ice-cream"></i>
				<span>상품</span>
			</a>
		<?php } ?>
		<?php if(in_array($user_auth, $sobi)) { ?>
		<?php }?>
		<?php if(in_array($user_auth, $johab)) { ?>
			<a id="suggest" href="./index.php?id=suggest" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-upload"></i>
				<span>건의사항</span>
			</a>

			<a id="vote" href="./index.php?id=vote" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-vote-yea"></i>
				<span>투표</span>
			</a>
		<?php } ?>
		<?php if(in_array($user_auth, $seller)) { ?>
			<a id="all_result" href="./index.php?id=all_result" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-chart-bar"></i>
				<span>총매출</span>
			</a>
			<a id="choose_name" href="./index.php?id=searchUser&URL=check_face_sell" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-shopping-cart"></i>
				<span>판매</span>
			</a>
			<a id="charge" href="./index.php?id=searchUser&URL=check_face_charge" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-donate"></i>
				<span>충전</span>
			</a>
			<a id="info_other" href="./index.php?id=searchUser&URL=info" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-user-tag"></i>
				<span>회원정보</span>
			</a>
		<?php } ?>
		<?php if(in_array($user_auth, $team)) { ?>
			<a id="member" href="./index.php?id=member" class="w3-bar-item nav-linkBtn">
				<i class="w3-large las la-user-edit"></i>
				<span>회원관리</span>
			</a>
		<?php } ?>
		<?php if(in_array($user_auth, $admin)) { ?>
		<?php } ?>
		<div style="height:30px;"></div>
	</div>
	<div id="top-nav-bar" class="flex rev-row w3-padding">
		<a class="custom-button w3-medium center" href="#" onclick="signOut();">
			Sign out
		</a>
		<?php if(isset($user)) { ?>
			<div id="myInfo-container" class="w3-bar-item center relative">
				<div id="info" class="w3-display-container hover-pointer" onclick="topNavToggle(event);">
					<img src="<?php echo $user_img; ?>" class="w3-circle">
				</div>
				<div id="top-nav-info" class="flex col absolute w3-padding w3-hide">
					<div id="myInfo-email" class="w3-medium">
						<?php echo $user_nav_name; ?>
					</div>
					<div class="w3-medium">
						<?php echo $user['email']; ?>
					</div>
					<hr>
					<div id="myInfo-email" class="w3-medium">
						잔액: <?php echo get_user_account($user['email']);?>원
					</div>
					<div id="myInfo-email" class="w3-medium">
						이번주 소비량: <?php echo $week_sell ;?>원
						<?php if($week_sell >= 9000) { ?>
						<i class="las la-exclamation w3-red"></i>
						<?php } ?>
					</div>
					<hr>
					<a href="./index.php?id=info" class="ht-theme w3-right-align">나의 정보</a>
				</div>
			</div>
		<?php } ?>
		<!-- <div id="page-name" class="center" style="margin-right:auto;"></div> -->
	</div>
</div>
<script defer src="./assets/js/nav.js"></script>
<script>
	
	function topNavToggle(event) {
		let menu = document.querySelector('#top-nav-info');
		if(menu.classList.contains('w3-hide')) {
			console.log('show');
			menu.classList.remove('w3-hide');
		} else {
			menu.classList.add('w3-hide');
		}
		event.stopPropagation();
	}
	
	document.body.addEventListener('click', function() {
		let menu = document.querySelector('#top-nav-info');
		if (!menu.classList.contains('w3-hide')) {
			console.log('hide');
			menu.classList.add('w3-hide');
		}
	});

</script>
<?php 
$mysqli -> close();
?>