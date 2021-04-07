<!-- 

해야 할 것

line 136: 건의사항 표에 작성자 이메일 대신 이름 나오도록
line 140: 작성일 형식에 맞춰서 출력
line 144: 답변 여부에 따라 아이콘 표시 (지금은 번갈아서 나오게 되어있음. 수정해도 됌.)

-->
<style>
	.suggest-container {
		width: 100%;
		min-height: 100%;

		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		justify-content: center;
	}

	.suggest-item {
		width: 100%;
		display: flex;
	}
	
	.suggest-item:nth-child(2) {
		border-top: 1px var(--dark-gray) solid;
	}
	
	.suggest-item:last-child {
		border-bottom: 1px var(--light-gray) solid;
	}
	
	.suggest-item>*:nth-child(1) {
		display: inline-block;
		width: 50%;
		overflow:hidden;
		text-overflow:ellipsis;
		white-space:nowrap;
		flex-shrink: 1;
		line-height: 43px;
		padding-left: 16px;
	}
	
	.suggest-item>* {
		flex-shrink: 0;
	}
	
	.suggest-item>*:nth-child(2) {
		margin-left: auto;
	}
	
	.suggest-item:nth-child(2n+3) {
		background-color: var(--theme-light);
	}
	
	#add-suggestion {
		width: 10%;
		margin-right: 16px;
		line-height: 36px;
		height: 59px;
	}
	
	.bar-1 {
		width: 10%;
		min-width: 90px;
	}
</style>

<div class="suggest-container">
	<!-- 검색기능: suggest_search.php 참고 -->

	<div id="member-searchBar" class="w3-bar center-container z-index-1 color-white-transparent">
		<a id="add-suggestion" href="./index.php?id=suggest_edit" class="center-container c-theme-dark w3-button hover-active w3-round">
			건의하기
		</a>
		
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
			<input type="hidden" name="type" value="suggest">
			<label class="w3-button w3-border-0 w3-text-grey hover-active w3-hover-text-black">
				<input type="submit" class="w3-hide">
				<i class="fas fa-search w3-center"></i>
			</label>
		</form>
	</div>

	<?php
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "SELECT * 
			FROM suggest
			ORDER BY created DESC";
	$result = mysqli_query($mysqli, $sql);
	?>
	<!-- 건의사항 목록 -->
	<!-- 최근 N개만 나오게 하자 -->
	<div id="suggest-content" class="suggest-container">
		<div class="suggest-item">
			<div>건의사항 제목</div>
			<div class="center w3-padding w3-margin-right bar-1">작성자</div>
			<div class="center w3-margin-right bar-1">작성일</div>
			<div class="center bar-1">
				<div style="margin-right:7px;" class="w3-button hover-default">답변</div>
			</div>
		</div>
		<?php for($i = 0; $i < 15; $i++) { $row = mysqli_fetch_assoc($result); if(!$row){break;}?>
		<!-- 건의사항 카드 -->
		<div class="suggest-item">
			<!-- 작성자 이메일 -->
			<a
				class="ht-theme"
				href="./index.php?id=suggest_read&article_type=suggest&article_id=<?php echo $row['id'] ?>"
			>
				<?php echo $row['title'] ?>
			</a>
			<!-- 이거 이름으로 바꾸기 -->
			<div class="center w3-padding w3-margin-right bar-1">
				<?php echo $row['writer'] ?>
			</div>
			
			<div class="center w3-margin-right bar-1">
				<?php echo substr($row['created'], 5, 2)."월 ".substr($row['created'], 8, 2)."일" ?>
			</div>

			<div class="center bar-1">
				<div class="w3-button w3-large w3-margin-right hover-default">
					<?php if($row['answer']) { ?>
					<i class="las la-check w3-text-green"></i>
					<?php } else { ?>
					<i class="las la-minus"></i>
					<?php }?>
				</div>
				<!-- 작성자가 언제든지 작성한 글 수정하게 해도 괜찮나 물어보자 -->
			</div>
		</div>
		<?php }?>
	</div>
</div>

<script>
	var httpRequest;
	document.querySelector('input[type="button"]').addEventListener('click', makeRequest);

	function checkDelete() {
		return confirm('삭제하시겠습니까?');
	}
	
	function handleSubmit(event) {
		event.preventDefault();
		makeRequest();
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
				console.log(httpRequest.responseText);
			}
		}
	}
</script>