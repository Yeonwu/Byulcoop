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
	
	.suggest-answer {
		word-break: keep-all;
		white-space: pre-line;
	}
</style>
<?php
require_once "./view/user_auth.php";

$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
		  mysqli_set_charset($mysqli, 'utf8');
$sql = "SELECT * 
		FROM suggest
		WHERE id =".$_GET['article_id'];
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
$auth = getAuth();

$SHOW_EDIT = FALSE;
$SHOW_ANSWER = FALSE;
$SHOW_DEL = FALSE;

if($auth == "관리자" || $auth == "팀장") {
	$SHOW_EDIT = FALSE;
	$SHOW_ANSWER = TRUE;
	$SHOW_DEL = TRUE;
}

if($row['email'] == $_COOKIE['user_email']) {
	$SHOW_EDIT = TRUE;
	$SHOW_DEL = TRUE;
}

?>
<div id="suggest-read-container" class="center-container">
	<div class="w3-padding-large relative">
		<?php if ($SHOW_EDIT || $SHOW_ANSWER || $SHOW_DEL) { ?>
			<i class="las la-ellipsis-v w3-xlarge ht-theme hover-pointer w3-right" onclick="toggleMenu()"></i>
			<div id="suggest-toggle-menu" style="width:110px; top:40px; right:20px;" class="flex col absolute w3-card w3-hide">
				<!-- 버튼 3개 있어. 수정, 답변, 삭제. 권한 맞춰서 표시해줘 -->
				<?php if($SHOW_EDIT) { ?>
					<a
						style="border-bottom: 1px var(--light-gray) solid"
						class="w3-button ht-theme center"
						href="./index.php?id=suggest_edit&article_type=suggest&article_id=<?php echo $row['id'] ?>"
					>
						<i class="las la-pen"></i> 수정하기
					</a>
				<?php } ?>
				<?php if($SHOW_ANSWER) { ?>
					<a
						style="border-bottom: 1px var(--light-gray) solid"
						class="w3-button ht-theme center"
						href="./index.php?id=suggest_answer&article_id=<?php echo $row['id'] ?>"
					>
						<i class="las la-scroll"></i> 답변하기
					</a>
				<?php } ?>
				<?php if($SHOW_DEL) { ?>
					<a
						class="w3-button ht-theme center"
						href="./view/suggest_delete.php?article_type=suggest&article_id=<?php echo $row['id'] ?>"
						onclick="return checkDelete();"
					>
						<i class="las la-trash-alt"></i> 삭제하기
					</a>
				<?php } ?>
			</div>
		<?php }?>
		<div id="suggest-info">
			<p>
				<?php echo '작성자: '.$row['writer'].'님'; ?>
			</p>
			<p class="w3-large">
				<?php echo substr($row['created'], 0, 10); ?>
			</p>
		</div>

		<div id="suggest-read-title" class="b-b-gray">
			<h2>
				<?php echo $row['title'] ?>
			</h2>
		</div>
		

		<div id="suggest-article" class="margin-b-48">
			<pre class="w3-large"><?php echo $row['content'] ?></pre>
		</div>

		<div class="margin-b-48">
			<h3 class="b-b-gray">
				답변
			</h3>
			<?php if($row['answer']){ ?>
			<!-- 답변 있을 경우 -->
			<pre class="suggest-answer w3-large"><?php echo $row['answer'] ?></pre>
			<pre><?php echo "[".$row['answer_writer']."]" ?></pre>
			<?php } else { ?>
			<!-- 답변 없을 경우 -->
			<pre class="w3-large t-dark-gray">답변을 준비하고 있습니다. 조금만 기다려주세요.</pre>
			<?php } ?>

		</div>
		
		<div>
			<h3 class="b-b-gray">
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
						WHERE article_id =".$_GET['article_id']." AND type = 'suggest' ORDER BY created DESC";
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
		let menuVisible = false;
		function toggleMenu() {
			if(menuVisible) {
				document.querySelector('#suggest-toggle-menu').classList.add('w3-hide');
			} else {
				document.querySelector('#suggest-toggle-menu').classList.remove('w3-hide');
			}
			menuVisible = !menuVisible;
		}
		function checkDelete() {
			return confirm('삭제하시겠습니까?');
		}
		function main() {
			let submitBtn = document.querySelector('#suggest-comment-submit');
			let writeComment = document.querySelector('#suggest-write-comment>*[contenteditable]');

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
				formData.append('article_type', 'suggest');

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
							alert('페이지를 로드하지 못했습니다.');
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