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
		height: 15em;
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
	
	.vote-item textarea{
		height: 2.5em;
		padding-left: 36px !important;
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
		<form action="./view/vote_edit_mysql.php" method="POST">
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
			
			<div id="suggest-read-title" class="w3-large margin-t-12 margin-b-12 c-light-gray w3-padding w3-round">
				<div>
					투표 제목
				</div>
				<textarea
					name="title"
					class="b-gray fb-theme w3-padding w3-round"
					placeholder="제목을 입력해주세요"
				><?php echo $row['title'] ?></textarea>
			</div>

			<div id="suggest-article" class="w3-large margin-b-12 c-light-gray w3-padding w3-round">
				<div>
					투표 글 내용
				</div>
				<textarea
					name="content"
					class="b-gray fb-theme w3-padding w3-round"
					placeholder="내용을 입력해주세요"
				><?php echo $row['content'] ?></textarea>
			</div>
			
			<div id="vote-list" class="w3-large margin-b-12 c-light-gray w3-padding w3-round">
				<div>
					투표 항목
				</div>
				<div class="vote-item relative">
					<div class="w3-button t-dark-gray ht-theme absolute" data-action="remove" style="top:1px;left:-5px;">
						<i class="las la-trash-alt"></i>
					</div>
					<textarea
						name="vote-item[]"
						class="b-gray fb-theme w3-padding w3-round"
						placeholder="내용을 입력해주세요"
					></textarea>
				</div>
				<div class="vote-item relative">
					<div class="w3-button t-dark-gray ht-theme absolute" data-action="remove" style="top:1px;left:-5px;">
						<i class="las la-trash-alt"></i>
					</div>
					<textarea
						name="vote-item[]"
						class="b-gray fb-theme w3-padding w3-round"
						placeholder="내용을 입력해주세요"
					></textarea>
				</div>
				<div class="vote-item relative">
					<div class="w3-button t-dark-gray ht-theme absolute" data-action="remove" style="top:1px;left:-5px;">
						<i class="las la-trash-alt"></i>
					</div>
					<textarea
						name="vote-item[]"
						class="b-gray fb-theme w3-padding w3-round"
						placeholder="내용을 입력해주세요"
					></textarea>
				</div>
				<div class="vote-item relative">
					<div class="w3-button t-dark-gray ht-theme absolute w3-bar w3-left-align" data-action="add" style="top:1px;left:-5px;">
						<i class="las la-plus"></i>
					</div>
					<textarea
						class="b-gray fb-theme w3-padding w3-round"
						placeholder="내용을 입력해주세요"
					></textarea>
				</div>
			</div>
			
			<div class="w3-large margin-b-12 c-light-gray w3-padding w3-round">
				<div>
					투표 설정
				</div>
				<div class="margin-b-12">
					<span class="margin-r-12">마감 날짜</span>
					<input name="end-date" class="b-gray fb-theme w3-padding w3-round" type="date">
				</div>
				<div>
					<span class="margin-r-12">한 사람당 투표수</span>
					<input name="vote-per-head" class="b-gray fb-theme w3-padding w3-round" style="width: 80px" type="number">
				</div>
			</div>

			<div class="w3-bar">
				<label>
					<div
						id="suggest-comment-submit"
						class="w3-center c-theme-dark w3-button hover-active w3-margin-right w3-round"
					>작성하기</div>
					<a
						class="w3-center w3-red w3-button hover-active w3-margin-right w3-round"
						href="./index.php?id=vote"
					>취소</a>
				</label>
			</div>
		</form>
	</div>
</div>

<script>
	document.querySelector('#suggest-comment-submit').addEventListener('click', function(event) {
		let form = document.querySelector('form');
		let data = new FormData(form);
		
		if (data.get('title').length > 20) {
			alert('제목은 최대 20자입니다.');
			return;
		}
		if (data.getAll('vote-item[]').length < 2) {
			alert('투표 항목을 2개 이상 입력해주세요.');
			return;
		}
		
		if (confirm('투표는 수정하실 수 없습니다. 작성하시겠습니까?')) {
			form.submit();
		}
	});
	
	let voteList = document.querySelector('#vote-list');
	voteList.addEventListener('click', deleteVoteItem);
	
	function deleteVoteItem(event) {
		let btn = event.target.closest('.w3-button');
		if (!btn) return;
		if (!voteList.contains(btn)) return;
		
		if (btn.dataset.action == "remove") {
			btn.parentNode.remove();
		} else if (btn.dataset.action == "add") {
			let newList = document.createElement('div');
			newList.classList.add('vote-item');
			newList.classList.add('relative');
			newList.innerHTML = `
					<div class="w3-button t-dark-gray ht-theme absolute" data-action="remove" style="top:1px;left:-5px;">
						<i class="las la-trash-alt"></i>
					</div>
					<textarea
						name="vote-item[]"
						class="b-gray fb-theme w3-padding w3-round"
						placeholder="내용을 입력해주세요"
					></textarea>`;
			voteList.insertBefore(newList, btn.parentNode);
		}
	}

</script>