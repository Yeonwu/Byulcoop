
<?php

//-----------------------------------------init 
require_once "./view/user_auth.php";
require_once "./view/config.php";

//-----------------------------------------check auth
$auth = getAuth();

if ($auth == "소비자" || $auth == "조합원") {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}


//---------------유저 정보를 넘길 페이지 URL
if(isset($_GET['URL'])) {
	$next_url = $_GET['URL'];
} else {
	echo "
	<script>
		alert('잘못된 접근입니다.');
		window.location.href = './index.php?id=home';
	</script>
	";
}

//---------------검색값 세팅
if(isset($_POST['search_query'])) {
	$search_query = $_POST['search_query'];
} else {
	$search_query = "검색어를 입력해 주세요";
}


function connect_DB() {
	$mysqli = new mysqli("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']); // connect DB
	mysqli_set_charset($mysqli, 'utf8');

	// Check connection
	if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		//return $mysqli -> connect_error;
		exit();
	}
	return $mysqli;
}

function checkClientDevice() {
	if (preg_match("/iPad/", $_SERVER['HTTP_USER_AGENT'])) {
		return 'iPad';
	} else {
		return 'laptop';
	}
}

function search_user($query) {
	
	global $next_url;
	
	$mysqli = connect_DB();
	
	$sql = "
	SELECT
		u.name,
		u.grade,
		u.auth,
		u.email,
		u.image,
		t.query_str
	FROM
		`user` AS u
	JOIN
		(
		SELECT
			CONCAT(NAME, grade, email, auth) AS query_str,
			id
		FROM
			`user`
	) t
	WHERE
		t.id = u.id AND t.query_str LIKE ?";
	
	$stmt = $mysqli -> stmt_init();
	$stmt = $mysqli -> prepare($sql);
	
	$query = '%' . $query . '%';
	$stmt -> bind_param("s", $query);
	
	$stmt -> execute();
	
	$result = $stmt -> get_result();
	
	$printStr = "";
	$i = 0;
	
	while ($row = $result -> fetch_assoc()) {
		
		if($i % 2) $table_impact = "c-theme-light ";
		else $table_impact = "";
		$i++;
		
		$printStr .= "
		<div class='w3-bar center-container {$table_impact}member-user-row w3-row'>
			<!-- <div class='w3-container w3-padding w3-center id w3-col l2'>{$i}</div> -->
			<div class='w3-container w3-padding grade w3-col l2'>{$row['grade']}</div>
			<div class='w3-container email w3-col l4 w3-hide-medium'>{$row['email']}</div>
			<div class='w3-container w3-padding name w3-col l2'>{$row['name']}</div>
			<div class='w3-container w3-padding auth w3-col l2'>{$row['auth']}</div>
			<div class='w3-container w3-padding w3-center w3-col l2'>
				<form action='./index.php?id={$next_url}' method='POST'>
					<input type='hidden' name='name' value='{$row['name']}'>
					<input type='hidden' name='image' value='{$row['image']}'>
					<input type='hidden' name='grade' value='{$row['grade']}'>
					<input type='hidden' name='email' value='{$row['email']}'>
					<input type='hidden' name='auth' value='{$row['auth']}'>
					<label>
						<div class='w3-button c-theme-dark w3-text-white hover-active w3-round'>
							선택
						</div>
						<input type='submit' class='w3-hide' value='선택'>
					</label>
				</form>
			</div>
		</div>
		";
	}
	
	echo $printStr;
	
}

?>

<style>

	#member-table-head {border-bottom: 1px var(--dark-gray) solid;}

</style>

<div id='searchUser-container' class="center-container-column">
	<div id="member-searchBar" class="w3-bar center-container">
		<form action="./index.php?id=searchUser&URL=<?php echo $next_url;?>" method="POST" id="member-searchTxt" class="w3-bar center-container">
			<input
				type="text"
				placeholder="검색어를 입력하세요"
				class="w3-bar custom-input1 w3-padding b-light-gray fb-theme w3-round c-white"
				autocomplete="off"
				name="search_query"
			/>
			<label class="w3-button w3-border-0 w3-text-grey hover-active w3-hover-text-black">
				<input type="submit" class="w3-hide">
				<i class="fas fa-search w3-center"></i>
			</label>
		</form>
	</div>
	<div id="member-table" class="w3-bar">
		<div id="member-table-head" class="w3-bar center-container member-user-row w3-row"> 
			<!-- <div class="w3-col l2 w3-container w3-padding w3-center color-gray-warm">번호</div> -->
			<div class="w3-col l2 w3-container w3-padding color-gray-warm">학년</div>
			<div class="w3-col l4 w3-hide-medium w3-container w3-padding color-gray-warm">이메일</div>
			<div class="w3-col l2 w3-container w3-padding color-gray-warm">이름</div>
			<div class="w3-col l2 w3-container w3-padding color-gray-warm">권한</div>
			<div class="w3-col l2 w3-container w3-padding w3-center color-gray-warm">선택</div>
		</div>
		<p id="member-noResult-message" class="w3-center w3-xlarge w3-hide">
			검색 결과가 없습니다.
		</p>
		<?php search_user($search_query);?>
	</div>
</div>