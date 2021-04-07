<?php
require_once "user_auth.php";
$logged_in = bwf_verify_login();
?>


<div id = "login-container" class = "center-container <?php if($logged_in) echo "w3-hide"; ?>">
	<div id = "login-popup" class = "w3-card center-container-column w3-white">
		
		<p>
			로그인이 필요합니다!
		</p>
		
		<div class="g-signin2" data-onsuccess="onSignIn"></div>
	</div>
	
</div>

<script src="https://apis.google.com/js/platform.js"></script>
<script src="./assets/js/login.js"></script>
<?php if(!$logged_in) { ?>
<script>
	signOut();
</script>
<?php } ?>