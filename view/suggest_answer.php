<!-- 

건의사항 답변다는 페이지

 -->
<style>
	/* 스타일 태그 옮기지 마 충돌날 수도 있음 */
	#suggest-read-container {
		width: 100%;
	}

	#suggest-read-container > div {
		width: 100%;
	}
	
	#suggest-read-writer textarea,
	#suggest-read-title textarea {
		height: 2.5em;
	}
	
	#suggest-article textarea{
		height: 25em;
		resize: vertical;
	}

	textarea {
		width: 100%;
		border: none;
		resize: none;
	}

	textarea:focus {
		outline: none;
	}
</style>

<!-- 


POST로 전송되는 데이터 목록
-------------------------------------------------
article_id        답변을 해주는 건의사항 글의 id
answer_content    답변내용
user_name         답변자 이름 + 직책 ( ex 교육팀 팀장 홍길동 )
-------------------------------------------------

COOKIE로 전송되는 데이터 목록
-------------------------------------------------
user_email        답변한 사람 이메일
-------------------------------------------------

-->

<div id="suggest-read-container" class="center-container">
	<div class="w3-padding-large">
		<form action="./view/suggest_answer_mysql.php" method="POST" class="flex col">
			<input name="article_id" value="<?php echo $_GET['article_id'];?>" type="hidden" />
			<?php 
				$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
				mysqli_set_charset($mysqli, 'utf8');
				$sql = "SELECT title 
						FROM suggest 
						WHERE id =".$_GET['article_id'];
				$result = mysqli_query($mysqli, $sql);
				$row = mysqli_fetch_assoc($result);
			?>
			<div id="suggest-read-writer" class="w3-large margin-t-12">
				<div>
					답변자명
				</div>
				<textarea
					name="user_name"
					class="b-light-gray fb-theme w3-padding w3-round"
					placeholder="답변자 이름과 직책을 입력해주세요. ex) 교육팀 팀장 홍길동"
				></textarea>
			</div>
			
			<div id="suggest-read-title" class="w3-large margin-t-12 margin-b-12">
				<div>
					답변할 건의사항 제목
				</div>
				<textarea
					name="title"
					class="b-light-gray fb-theme w3-padding w3-round"
					placeholder="<?php echo $row['title'] ?>"
					disabled
				></textarea>
			</div>

			<div id="suggest-article" class="w3-large margin-b-12">
				<div>
					건의사항의 답변내용을 적어주세요.
				</div>
				<textarea
					name="answer_content"
					class="b-light-gray fb-theme w3-padding w3-round"
					placeholder="내용을 입력해주세요"
				></textarea>
			</div>

			<div class="w3-bar">
				<label>
					<div
						id="suggest-comment-submit"
						class="w3-center c-theme-dark w3-button hover-active w3-margin-right w3-round"
					>
						답변하기
					</div>
					<a
						class="w3-center w3-red w3-button hover-active w3-margin-right w3-round"
						href="./index.php?id=suggest"
					>
						취소
					</a>
					<input type="submit" class="w3-hide">
				</label>
			</div>
		</form>
		
	</div>
</div>