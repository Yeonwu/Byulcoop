<!-- 권한 관련 php 함수 파일 -->

<?php

// 현재 로그인해 있는 계정의 권한을 가져오는 함수

function bwf_verify_login() {
	if(!isset($_COOKIE['verify_token']) || !isset($_COOKIE['user_email'])) {
		return FALSE; // 로그인이 안돼어 있는 경우
	}
	if($_COOKIE['verify_token'] != hash('sha256', $_COOKIE['user_email'])) {
		return FALSE; // 로그인이 되어있지만 정보가 변조된 경우
	}
	return TRUE; // 로그인이 되어있고 정보도 안전한 경우
}

function getAuth() { 
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	  exit();
	}

	if ( isset($_COOKIE['user_email']) ) {
		$user_email = $_COOKIE['user_email'];
		$sql = "SELECT auth FROM `user` WHERE email = '{$user_email}';";
		$user_auth = $mysqli -> query($sql) -> fetch_assoc()['auth'];
		
		if ( $user_auth === NULL ) $user_auth = "소비자";
	} else {
		$user_auth = "소비자";
	}

	$mysqli -> close();

	return $user_auth;
}
?>