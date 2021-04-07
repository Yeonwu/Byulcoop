const inputDiv = document.querySelector('#inputDiv');
var formset

function addFormset() {
	var formset = document.createElement('div');
	formset.classList.add('member-resister-formset');
	formset.innerHTML = `<div class="member-resister-formset">
			<div class="w3-display-container">
				<img
					src="https://tseriesracing.files.wordpress.com/2014/12/placeholder-400x300.jpg"
					width="200px"
					height="150px"
					class="resister_img"
				/>
				<div class="filebox">
					<label class="w3-button w3-display-hover w3-black w3-display-middle">
						<input type="file" name="img[]" class="w3-hide" accept="image/*" onchange="setThumbnail(event);">
						업로드
					</label>
				</div>
			</div>
			<div class="member-resister-textboxes">
				<div class="center-container-row">
					<input
						type="text"
						class="member-resister-textbox resister_name"
						placeholder="이름"
					/>
					<select type="text" class="member-resister-textbox resister_grade">
						<option value="">학년</option>
						<option value="6학년">6학년</option>
						<option value="7학년">7학년</option>
						<option value="8학년">8학년</option>
						<option value="9학년">9학년</option>
						<option value="10학년">10학년</option>
						<option value="11학년">11학년</option>
						<option value="12학년">12학년</option>
						<option value="선생님">선생님</option>
					</select>
					<input
						type="text"
						class="member-resister-textbox resister_auth"
						placeholder="권한"
					/>
				</div>
				<input type="email" class="w3-bar resister_email" placeholder="이메일" />
				<input type="date" class="w3-bar resister_birth" placeholder="생년월일" />
			</div>
		</div>`;

	inputDiv.appendChild(formset);
}

function removeFormset() {
	var removeNode = inputDiv.lastChild;
	inputDiv.removeChild(removeNode);
}

function imgFormOK(path) {
	function formSubmit(f) {
		// 업로드 할 수 있는 파일 확장자를 제한합니다.

		var extArray = new Array(
			'hwp',
			'xls',
			'doc',
			'xlsx',
			'docx',
			'pdf',
			'jpg',
			'gif',
			'png',
			'txt',
			'ppt',
			'pptx'
		);

		if (path == '') {
			alert('파일을 선택해 주세요.');

			return false;
		}

		var pos = path.indexOf('.');

		if (pos < 0) {
			alert('확장자가 없는파일 입니다.');

			return false;
		}

		var ext = path.slice(path.indexOf('.') + 1).toLowerCase();

		var checkExt = false;

		for (var i = 0; i < extArray.length; i++) {
			if (ext == extArray[i]) {
				checkExt = true;

				break;
			}
		}

		if (checkExt == false) {
			alert('업로드 할 수 없는 파일 확장자 입니다.');

			return false;
		}

		return true;
	}
}

function resisterSubmit() {
	const nameList = document.querySelectorAll('.resister_name');
	const gradeList = document.querySelectorAll('.resister_grade');
	const authList = document.querySelectorAll('.resister_auth');
	const emailList = document.querySelectorAll('.resister_email');
	const birthList = document.querySelectorAll('.resister_birth');
	const imgList = document.querySelectorAll("input[type='file']");

	const form = document.querySelector('form');

	var sendInfoList = new Array();

	var numberOfData;
	var input;
	var i = 0;

	for (i = 0; i < nameList.length; i++) {
		var sendInfo = {
			user_name: nameList[i].value,
			user_grade: gradeList[i].value,
			user_auth: authList[i].value,
			user_email: emailList[i].value,
			user_birth: birthList[i].value,
			user_img: i,
		};
		
		var sendImg = imgList[i].cloneNode();

		sendInfoList.push(sendInfo);
		//if ( imgFormOK(sendImg.value) ) {
			form.appendChild(sendImg);
		//}
	}

	numberOfData = sendInfoList.length;
	sendInfoList = JSON.stringify(sendInfoList);

	input = document.createElement('input');
	input.setAttribute('type', 'hidden');
	input.setAttribute('name', 'numberOfData');
	input.value = `${numberOfData}`;
	form.appendChild(input);

	input = document.createElement('input');
	input.setAttribute('type', 'hidden');
	input.setAttribute('name', 'data');
	input.value = `${sendInfoList}`;
	form.appendChild(input);

	form.submit();

}

function setThumbnail(event) { 
	var reader = new FileReader();
	var img = event.target.parentNode.parentNode.parentNode.parentNode.querySelector('img');
	reader.onload = function(event) { 
		img.setAttribute("src", event.target.result); 
	}; 
	reader.readAsDataURL(event.target.files[0]); 
}

document.querySelector('#resister-submit').onclick = resisterSubmit;
document.querySelector('#add-formset-btn').onclick = addFormset;
document.querySelector('#remove-formset-btn').onclick = removeFormset;