<style>
	/* 스타일 태그 옮기지 마 충돌날 수도 있음 */
	#suggest-read-container {
		width: 100%;
	}

	#suggest-read-container > div {
		width: 100%;
	}
	
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

<?php

/*

이 페이지는 이미 작성된 글을 수정 or 새로운 글을 작성하는 페이지
get으로 article_id가 넘어오면 수정, 없으면 새로운 글으로.
form으로 받은 데이터도 마찬가지로 하면 됨.

*/

?>

<div id="suggest-read-container" class="center-container">
	<div class="w3-padding-large">
		<form action="./view/suggest_edit_mysql.php" method="POST">
			<?php if(isset($_GET['article_id'])){ 
			$mysqli = mysqli_connect("localhost", CONF_DB['db_user'], CONF_DB['db_password'], CONF_DB['db_name']);
					  mysqli_set_charset($mysqli, 'utf8');
			$sql = "SELECT 
					title, 
					email, 
					created, 
					content 
					FROM suggest
					WHERE id =".$_GET['article_id'];
			$result = mysqli_query($mysqli, $sql);
			$row = mysqli_fetch_assoc($result);
			?>
			<input name="article_id" value="<?php echo $_GET['article_id'];?>" type="hidden" />
			<?php }
			else{
				$row = array("title" => "", "content"=> "", "created" => "", "email"=> "");
			}
			?>
			<div id="suggest-info">
				<div>
					<?php echo $row['created']; ?>
				</div>
				<div>
					<?php echo $row['email']; ?>
				</div>
			</div>
			
			<div id="suggest-read-title" class="w3-large margin-t-12 margin-b-12">
				<div>
					건의사항 제목
				</div>
				<textarea
					name="title"
					class="b-light-gray fb-theme w3-padding w3-round"
					placeholder="제목을 입력해주세요"
				><?php echo $row['title'] ?></textarea>
			</div>

			<div id="suggest-article" class="w3-large margin-b-12">
				<div>
					매점 협동조합에게 건의하고 싶은 사항을 적어주세요
				</div>
				<textarea
					name="content"
					class="b-light-gray fb-theme w3-padding w3-round"
					placeholder="내용을 입력해주세요"
				><?php echo $row['content'] ?></textarea>
				<!-- textarea에 공백이나 엔터 들어가면 레이아웃 깨지니까 이렇게 공백 없이 출력해야 돼 -->
				<!-- 잘못된 예시: <textarea>hello   </textarea>-->
			</div>

			<div class="w3-bar">
				<label>
					<div
						id="suggest-comment-submit"
						class="w3-center c-theme-dark w3-button hover-active w3-margin-right w3-round"
					>
						건의하기
					</div>
					<a
						class="w3-center w3-red w3-button hover-active w3-margin-right w3-round"
						href="./index.php?id=suggest"
					>
						취소
					</a>
				</label>
			</div>
		</form>
	</div>
</div>
<script>

	document.querySelector('#suggest-comment-submit').addEventListener('click', function() {
		if (document.getElementsByName('title')[0].value.length > 20) {
			alert('제목은 최대 20자입니다.');
			return;
		}
		document.querySelector('form').submit();
	});

</script>