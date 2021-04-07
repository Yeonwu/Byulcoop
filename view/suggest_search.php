<!-- type은 vote인지 suggest인지 -->
<!-- search_query에 담긴 문자열을 기준으로 제목, 내용을 검색해서 게시물을 출력 -->
<!-- 뭐로 감싸지 말고 그냥 suggest-item 여러개 생으로 출력하면 됌 -->
<!-- SQL에 title = "뭐뭐뭐" 말고 title LIKE "%뭐뭐뭐%" 이렇게 써 -->
<!-- MYSQL LIKE 이라고 검색하면 자료 더 많이 나와 -->
<!-- 나도 알거덩요! LIKE 쓰면 포함된 값 가져오는거잖아 -->

<!-- type이 suggest일때: 바로 아래 div태그를 while문으로 출력 -->
<?php 
require "./config.php";
$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
		  mysqli_set_charset($mysqli, 'utf8');
if($_POST['type'] == 'suggest'){
	$sql = "SELECT * 
			FROM suggest
			WHERE title 
			LIKE '%{$_POST['search_query']}%' 
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

	<?php if($result -> num_rows == 0) { ?>
		<div class="center w3-xlarge t-gray">
			검색 결과가 없습니다
		</div>
	<?php }?>

<!-- --------------------------------------------------------------------------------------- -->

<?php } else { 
	$sql = "SELECT * 
			FROM vote_content 
			WHERE title 
			LIKE '%{$_POST['search_query']}%' 
			ORDER BY created DESC";
	$result = mysqli_query($mysqli, $sql); ?>
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
				
				<a
					class="absolute w3-button ht-theme"
				    style="bottom: 0; right:72px;"
					href="./view/vote_delete.php?article_type=vote&article_id=<?php echo $row['id'] ?>"
				   	onclick="return checkDelete();"
				>
					삭제
				</a>
				
				<a
					class="absolute w3-button ht-theme"
				    style="bottom: 0; right:0;"
					href="./index.php?id=vote_read2&article_type=vote&article_id=<?php echo $row['id'] ?>"
				>
					더보기
				</a>

			</div>
		</div>
		<?php 
		}
		?>
	</div>
<?php } ?>