<style>
	.suggest-container {
		width: 100%;
		min-height: 100%;

		display: flex;
		flex-wrap: wrap;
		justify-content: center;
	}

	.suggest-item {
		width: 480px;
		height: 480px;
		margin: 24px;
	}

	.suggest-item-title {
		font-size: inherit;
		width: 100%;
		overflow: hidden;
	}

	.suggest-item-content {
		font-size: inherit;
		line-height: 1.5em;
		height: 9em;
		width: 100%;
		overflow: hidden;
		display: -webkit-box;
		-webkit-line-clamp: 6;
		-webkit-box-orient: vertical;
		word-break: break-word;
		white-space: pre-line;
	}
	
	#add-suggestion {
		width: 10%;
		margin-right: 16px;
		line-height: 36px;
		height: 59px;
	}
	
	.bars {
		height: 24px;
	}
	
	.vote-graph {
		height: 18px;
		width: 100%;
		background-color: var(--theme-light);
		margin-bottom: 8px;
	}
	.vote-graph *{color: #655a5a !important;}
	
	.vote-graph:last-child {
		margin-bottom: 0;
	}
	
</style>

<?php

require_once "./view/user_auth.php";
$auth = getAuth();

?>

<div class="suggest-container">
	<div id="member-searchBar" class="w3-bar center-container z-index-1 color-white-transparent">
		<!-- 투표 추가는 권한 확인해서 버튼 띄우기 -->
		<?php if($_COOKIE['user_email'] == "191033@bmrschool.org" || $auth == "관리자") { ?>
			<a id="add-suggestion" href="./index.php?id=vote_edit" class="center-container w3-button c-theme-dark hover-active w3-round">
				새 투표
			</a>
		<?php } ?>
		
		<form
			action="./index.php?id=suggest_search"
			method="POST"
			id="member-searchTxt"
			class="w3-bar center-container"
			onsubmit="handleSubmit(event)"
		>
			<input
				type="text"
				placeholder="검색어를 입력하세요"
				class="w3-bar custom-input1 w3-padding b-light-gray fb-theme w3-round"
				autocomplete="off"
				name="search_query"
			/>
			<input type="hidden" name="type" value="vote">
			<label class="w3-button w3-border-0 w3-text-grey hover-active w3-hover-text-black">
				<input type="button" class="w3-hide" />
				<i class="fas fa-search w3-center"></i>
			</label>
		</form>
		
	</div>

	<!-- 건의사항 목록 -->
	<!-- 최근 N개만 나오게 하자 -->

<?php
	require_once "./view/user_auth.php";
	$auth = getAuth();
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "SELECT * 
			FROM vote_content 
			ORDER BY created DESC";
	$result = mysqli_query($mysqli, $sql);
?>
	<div id="suggest-content" class="suggest-container">
		<?php 
// 		투표 항목 반복문
		for($i = 0; $i < 15; $i++) { 
			$row = mysqli_fetch_assoc($result);
			if(!$row) {
				break;
			}?>
		<!-- 건의사항 카드 -->
		<div class="suggest-item b-gray w3-padding-large relative flex col">
			<!-- 작성자 이메일 -->
			<!-- 이메일이 나을까 이름이 나을까? -->
			<div class="w3-bar w3-margin-bottom flex row">
				<div>
					<?php
						$nowTime = date("Y-m-d H:i:s");
						$deadline = $row['vote_deadline'];
						if($nowTime > $deadline){
							echo "투표가 마감되었습니다.";
						} else {
							$left = substr($deadline, 0, -9);
							echo "{$left}에 마감됩니다.";
						}
					?>
				</div>
				<div style="margin-left: auto;">
					<?php echo $row['email'] ?>
				</div>
			</div>

			<div class="w3-large b-b-gray">
				<h2 class="suggest-item-title">
					<?php echo $row['title'] ?>
				</h2>
			</div>

			<div class="w3-large">
				<p class="suggest-item-content"><?php echo $row['content'] ?></p>
			</div>
			<div class="c-light-gray w3-round w3-padding margin-b-12" style="margin-top: auto;">
		<?php 
			// result = 투표 사항 반복문
			// result2 = 투표 옵션 반복문
			// result3 = 투표수 반복문
			// row = vote_content 정보
			// row2 = vote_option 정보
			// i = 투표 사항 반복횟수
			// ii = 투표 옵션 반복횟수
			// iii = 투표 옵션 반복횟수. ... 포함시킬지 결정시킴. 
			// sum = 투표수 총합
			// count = 옵션별 투표수
			$sql = "SELECT name 
					FROM vote_option 
					WHERE vote_id = '{$row['id']}'";
			$result2 = mysqli_query($mysqli, $sql);
			$iii = 1;
			$sql = "SELECT 
							COUNT(id)
							AS cnt
							FROM vote_value 
							WHERE vote_id = '{$row['id']}'";
			$result4 = mysqli_query($mysqli, $sql);
			$sum = $result4 -> fetch_assoc()['cnt'];
			if($sum == 0){
				$sum = 1;
			}
			
// 			투표 옵션 반복문
			for($ii = 0; $ii < 4; $ii++) {
				$row2 = mysqli_fetch_assoc($result2);
				if(!$row2) {
					break;
				}
					$sql = "SELECT 
							COUNT(id)
							AS cnt
							FROM vote_value 
							WHERE vote_id = '{$row['id']}' 
							AND value = '{$row2['name']}'";
					$result3 = mysqli_query($mysqli, $sql);
					$count = $result3 -> fetch_assoc()['cnt'];
				
				if($iii > 3){
				?>
					<div class="w3-bar center">
						<i class="las la-ellipsis-v"></i>
					</div>
				<?php
					}else{?>
				<div class="vote-graph">
					<div class="c-theme-dark w3-small padding-l-12" style="width:<?php echo ($count/$sum)*100; ?>%; white-space:nowrap">
						<?php echo $row2['name']." - " .$count. "명"; ?>
					</div>
				</div>
				<?php 
				}
				$iii += 1;
			}?>
				
			</div>

			<div class="w3-margin-bottom w3-bar">
				<!-- 권한 검사 -> 관리자, 팀장만 삭제 버튼 나오게 -->
				<?php if($auth == "관리자" || $auth == "팀장") { ?>
					<a
						class="absolute w3-button ht-theme"
						style="bottom: 0; right:72px;"
						href="./view/vote_delete.php?article_type=vote&article_id=<?php echo $row['id'] ?>"
						onclick="return checkDelete();"
					>
						삭제
					</a>
				<?php } ?>
				
				<a
					class="absolute w3-button ht-theme"
				    style="bottom: 0; right:0;"
					href="./index.php?id=vote_read&article_type=vote&article_id=<?php echo $row['id'] ?>"
				>
					더보기
				</a>

			</div>
		</div>
		<?php 
		}
		?>
	</div>
</div>

<script>
	var httpRequest;
	document.querySelector('input[type="button"]').addEventListener('click', makeRequest);

	function handleSubmit(event) {
		event.preventDefault();
		makeRequest();
	}
	
	function checkDelete() {
		return confirm('삭제하시겠습니까?');
	}

	function makeRequest() {
		let formData = new FormData(document.querySelector('form'));
		httpRequest = new XMLHttpRequest();

		if (!httpRequest) {
			alert('페이지를 로드하지 못했습니다.');
			return false;
		}
		httpRequest.onreadystatechange = respondRequest;
		httpRequest.open('POST', './view/suggest_search.php');
		httpRequest.send(formData);
	}

	function respondRequest() {
		console.log(httpRequest);
		if (httpRequest.readyState === XMLHttpRequest.DONE) {
			if (httpRequest.status === 200) {
				document.querySelector('#suggest-content').innerHTML = httpRequest.responseText;
			} else {
				alert('페이지를 로드하지 못했습니다.');
			}
		}
	}
</script>