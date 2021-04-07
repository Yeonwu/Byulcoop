<!-- $_POST['article_id']로 글 아이디 값 넘어오니까 그걸로 띄워 -->

<style>
	/* 스타일 태그 옮기지 마 충돌날 수도 있음 */
	#suggest-read-container {
		width: 100%;
	}
	
	#suggest-read-container>div {
		width: 100%;
	}
	
	#suggest-read-title {
		margin-bottom: 36px;
	}

	#suggest-article {
		margin-bottom: 48px;
	}

	#suggest-write-comment {
		margin-bottom: 36px;
	}

	#suggest-comment-submit {
		align-self: flex-end;
		width: 60px;
		height: 38px;
	}

	#suggest-write-comment > *[contenteditable] {
		line-height: 33px;
		width: calc(100% - 76px);
		padding: 0 12px;
		word-break: break-all;
		border-bottom: 1px solid gray;
	}

	#suggest-write-comment > *[contenteditable]:focus {
		outline: none;
	}

	#suggest-comments {
		display: flex;
		flex-direction: column;
	}

	.suggest-comment > *:nth-child(1) {
		margin-bottom: 4px;
	}
	
	.vote-btn {
		height: 100%;
		line-height: 48px;
		padding: 0 24px;
	}
	
	.vote-bars {
		height: 46px;
		white-space: nowrap;
		overflow: visible;
		color: #655a5a !important;
	}
	
	.selected {
		border: 2px solid var(--theme-third);
	}
	
	.voters div{word-break: keep-all;}
	.selected + .voters {display: block !important;}
</style>
<?php 
	$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
			  mysqli_set_charset($mysqli, 'utf8');
	$sql = "SELECT vote_limit, vote_deadline 
			FROM vote_content 
			WHERE id = {$_GET['article_id']}";
	$result = mysqli_query($mysqli, $sql);
	$row = mysqli_fetch_assoc($result);
	$vote_limit = (int)($row['vote_limit']);

	$nowTime = date("Y-m-d H:i:s");
	$deadline = $row['vote_deadline'];

	// 몇번 투표했는지 채크
	$sql = "SELECT 
			COUNT(id)
			AS cnt
			FROM vote_value 
			WHERE vote_id = '{$_GET['article_id']}'
			AND email = '{$_COOKIE['user_email']}'";
	$result = mysqli_query($mysqli, $sql);
	$vote_number = (int)($result -> fetch_assoc()['cnt']);
	$vote_left = $vote_limit-$vote_number;
	$sql = "SELECT 
				title, 
				created, 
				content 
			FROM vote_content 
			WHERE id =".$_GET['article_id'];
	$result = mysqli_query($mysqli, $sql);
	$row = mysqli_fetch_assoc($result);
?>

<div id="suggest-read-container" class="center-container">
	<div class="w3-padding-large">
		<div id="suggest-info">
			<div>작성일 <?php echo $row['created']; ?></div>
			<div>마감일 <?php echo $deadline; ?></div>
		</div>

		<div id="suggest-read-title">
			<h2><?php echo $row['title']; ?></h2>
		</div>

		<div id="suggest-article">
			<p class="w3-large"><?php echo $row['content']; ?></p>
		</div>
		
		<div class="w3-padding w3-round c-light-gray">
			<div id="vote-bar" class="w3-bar w3-margin-bottom">
				<!-- php로 계산해서 몇 명 투표했는지, 퍼센트 계산해서 width값 설정하기 -->
				<!-- 여기부터 -->
			<?php	
				$sql = "SELECT name 
						FROM vote_option 
						WHERE vote_id = '{$_GET['article_id']}'";
				$result2 = mysqli_query($mysqli, $sql);
				$sql = "SELECT 
						COUNT(id)
						AS cnt
						FROM vote_value 
						WHERE vote_id = '{$_GET['article_id']}'";
				$result4 = mysqli_query($mysqli, $sql);
				$sum = $result4 -> fetch_assoc()['cnt'];
				if($sum == 0){
					$sum = 1;
				}
				while($row2 = mysqli_fetch_assoc($result2)){
				
				if(!$row2) {
					break;
				}
					$sql = "SELECT 
							COUNT(id)
							AS cnt
							FROM vote_value 
							WHERE vote_id = '{$_GET['article_id']}' 
							AND value = '{$row2['name']}'";
					$result3 = mysqli_query($mysqli, $sql);
					$count = $result3 -> fetch_assoc()['cnt'];

					?>
				<div class="w3-bar c-theme-light margin-b-12 flex col">
					<div class="w3-left hover-active c-theme-dark w3-padding-large vote-bars"
						 style="width: <?php echo ($count/$sum)*100; ?>%;"
						 data-vote-to="<?php echo $row2['name']; ?>">
						<?php echo $row2['name']." - " .$count. "명"; ?>
						<!-- 투표한 항목 이름 - 투표한 사람수 -->
					</div>
					<div class="voters w3-bar c-white w3-padding w3-hide">
						<div class="w3-border-bottom margin-b-12">
							투표자
						</div>
						<div>
							오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우, 오연우
						</div>
					</div>
				</div>
				<?php 
				
			}?>
				
			</div>
			<div>
				<?php if($nowTime > $deadline){ ?>
					투표가 마감되었습니다.
				<?php } else { $left = substr($deadline, 0, -9); ?>
					<?php echo $left;?> 에 마감됩니다.
				<?php } ?>
			</div>
			<div>
				<?php if($vote_left <= 0) { ?>
					더 이상 투표하실 수 없습니다.
				<?php } else { ?>
					앞으로 <?php echo $vote_left; ?>번 더 투표하실 수 있습니다.
					<a id="vote-submit" class="ht-theme hover-pointer">
						투표하기
					</a>
				<?php } ?>
			</div>
		</div>
		
		<div>
			<h3>
				댓글
			</h3>
		</div>

		<div id="suggest-write-comment" class="flex">
			<div
				id="suggest-comment-submit"
				class="center w3-large c-theme-dark w3-button hover-active w3-margin-right w3-round"
			>
				<i class="las la-paper-plane"></i>
			</div>
			<div class="w3-text-grey" contenteditable>
				댓글 내용을 입력하세요
			</div>
		</div>

		<div id="suggest-comments">
			<!-- 댓글 출력 -->
			<?php 
				$sql = "SELECT 
							u.name, 
							c.email,
							c.created, 
							c.content 
						FROM comment AS c
						JOIN user AS u
						ON u.email = c.email
						WHERE article_id =".$_GET['article_id']." AND type = 'vote' ORDER BY created DESC";
				$result = mysqli_query($mysqli, $sql);
				while($row = mysqli_fetch_assoc($result)){ ?>
					<div class="suggest-comment">
						<div class="flex">
							<div style="font-weight: bold;">
								<?php echo "{$row['name']}"; ?>&nbsp;
							</div>
							<div>
								<?php echo "({$row['email']})"; ?>&nbsp;&nbsp;&nbsp;
							</div>
							<div class="t-dark-gray w3-small" style="align-self: center;">
								<?php echo $row['created'] ?>
							</div>
						</div>
						<?php echo $row['content']; ?>
					</div>
				<hr>
				<?php } ?>
		</div>
	</div>

	<script>
		function main() {
			let submitBtn = document.querySelector('#suggest-comment-submit');
			let writeComment = document.querySelector('#suggest-write-comment>*[contenteditable]');

			let voteBars = document.querySelectorAll('.vote-bars');
			
			let selectedBtn = undefined;
			
			function updateVoteBar(info) {
				console.log(info);
				let json = JSON.parse(info);
				
				for(let i = 0; i < json.howMuch.length; i++) {
					voteBars[i].innerText = json.voteTo[i] + ' - ' + json.howMuch[i] + '명';
					voteBars[i].style.width = json.ratio[i] + '%';
				}
			}
			
			function sendVoteRequest() {
				if (selectedBtn == undefined) {
					alert('먼저 투표할 항목을 클릭하여 선택해주세요.');
					return;
				}
				if (!confirm('투표 후 변경하실 수 없습니다. 투표하시겠습니까?')) {
					return;
				}
				let content = selectedBtn.dataset.voteTo;
				window.location.href = './view/vote_do_mysql.php?article_id=<?php echo $_GET['article_id'];?>&vote=' + content;
				// let httpRequest;
				// httpRequest = new XMLHttpRequest();
				// if (!httpRequest) {
				// 	alert('페이지를 로드하지 못했습니다. httpRequest');
				// 	return false;
				// }
				// httpRequest.onreadystatechange = respondRequest;
				// httpRequest.open('GET', './view/vote_do_mysql.php?article_id=<?php //echo $_GET['article_id'];?>&vote=' + content);
				// httpRequest.send();

				// function respondRequest() {
				// 	console.log(httpRequest);
				// 	if (httpRequest.readyState === XMLHttpRequest.DONE) {
				// 		if (httpRequest.status === 200) {
				// 			updateVoteBar(httpRequest.responseText);
				// 		} else {
				// 			alert('페이지를 로드하지 못했습니다. 404');
				// 			console.log(httpRequest.responseText);
				// 		}
				// 	}
				// }
			}
			
			document.querySelector('#vote-submit').addEventListener('click', sendVoteRequest);
			
			voteBars.forEach(function(x) {
				x.addEventListener('click', function() {
					selectedBtn = x;
					voteBars.forEach((y) => y.classList.remove('selected'));
					x.classList.add('selected');
				});
			});
			
			// 댓글 부분..
			
			function emptyWriteComment() {
				writeComment.addEventListener(
					'click',
					function () {
						writeComment.innerText = '';
						writeComment.classList.remove('w3-text-grey');
					},
					{ once: true }
				);
			}
			
			
			emptyWriteComment();
			
			submitBtn.addEventListener('click', function () {
				var httpRequest;
				event.preventDefault();
				

				if (writeComment.classList.contains('w3-text-grey') || writeComment.innerText.replace(/\s/gi, '') == '') {
					alert('댓글 내용을 입력하세요');
					
					writeComment.innerText = '댓글 내용을 입력하세요';
					writeComment.classList.add('w3-text-grey');
					emptyWriteComment();
					return;
				}
				
				let formData = new FormData();
				formData.append('content', writeComment.innerText);
				formData.append('article_id', <?php echo $_GET['article_id'];?>);
				formData.append('article_type', 'vote');
				
				httpRequest = new XMLHttpRequest();

				if (!httpRequest) {
					alert('페이지를 로드하지 못했습니다.');
					return false;
				}
				httpRequest.onreadystatechange = respondRequest;
				httpRequest.open('POST', './view/suggest_upload_comment.php');
				httpRequest.send(formData);

				function respondRequest() {
					console.log(httpRequest);
					if (httpRequest.readyState === XMLHttpRequest.DONE) {
						if (httpRequest.status === 200) {
							let a = document.querySelector('#suggest-comments');
							a.innerHTML = httpRequest.responseText + a.innerHTML;
						} else {
							alert('페이지를 로드하지 못했습니다.' + httpRequest.status);
						}
					}
				}
				
				writeComment.innerText = '댓글 내용을 입력하세요';
				writeComment.classList.add('w3-text-grey');
				emptyWriteComment();
			});
		}

		main();
	</script>
</div>