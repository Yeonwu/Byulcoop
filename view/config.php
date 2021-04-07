<?php
	
// 개발 서버, 서비스 서버
// DEV       SER 
define ( 'SERVER_TYPE', 'DEV' );

//sell_send.php에서 이메일 보내는거
define ( 'EMAIL_USER_NM', 'oyeonu' );
define ( 'EMAIL_PW', 'dusdn319@');


if ( SERVER_TYPE === 'DEV' ) {
	// APPLICATION VERSION
	define ( 'VERSION', '1.0');
	
	//SERVER URL
	define ('URL', 'bmrcoop.run.goorm.io/bmrCoop');
	
	// DB CONFIG
	define ( 'CONF_DB', array(
		'db_user' => "phpmyadmin", // DB 로그인 아이디
		'db_password' => "1111", // DB 로그인 비밀번호
		'db_name' => "bmr_coop")  // DB 이름
	);

	// DIR CONFIG
	define ( 'CONF_DIR', array(
	'img' => "/workspace/bmrCoop/assets/img/") // 이미지 저장 파일 경로
	);

	// ERROE PRINTING
	define ( 'CONF_ERR_PRINT', true ); // php 에러 출력 여부
	
	//GOOGLE CLIENT ID
	define ( 'GOOGLE_CLIENT_ID', '93864729941-8ohmu805j0etq8bh7u203h2cns9kt7gn.apps.googleusercontent.com' );\
	
	//GOOGLE CLIENT SECRET
	define ( 'GOOGLE_CLIENT_SECRET', '' );
	
} else if ( SERVER_TYPE === 'SER' ) {
	
	// APPLICATION VERSION
	define ( 'VERSION', '1.0');
	
	//SERVER URL
	define ('URL', '');
	
	// DB CONFIG
	define ( 'CONF_DB', array(
		'db_user' => "", // DB 로그인 아이디
		'db_password' => "", // DB 로그인 비밀번호
		'db_name' => "")  // DB 이름
	);

	// DIR CONFIG
	define ( 'CONF_DIR', array(
	'img' => "/byulcoop/www/assets/img/") // 이미지 저장 파일 경로
	);

	// ERROE PRINTING
	define ( 'CONF_ERR_PRINT', false ); // php 에러 출력 여부
	
	//GOOGLE CLIENT ID
	define ( 'GOOGLE_CLIENT_ID', '93864729941-3cre6mln5dh37nqvtd28ap761i85j0q4.apps.googleusercontent.com' );
	
	//GOOGLE CLIENT SECRET
	define ( 'GOOGLE_CLIENT_SECRET', '' );
	
} else {
	
	// ERROE PRINTING
	define ( 'CONF_ERR_PRINT', false ); // php 에러 출력 여부
	
}

if ( CONF_ERR_PRINT ){
	error_reporting(E_ALL);
	ini_set("display_errors", 'on');
}

?>
