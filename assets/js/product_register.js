const inputDiv = document.querySelector('#inputDiv');

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
	var path = document.getElementById('upfile').value;
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

function setThumbnail(event) { 
	var reader = new FileReader();
	var img = event.target.parentNode.parentNode.parentNode.parentNode.querySelector('img');
	reader.onload = function(event) { 
		img.setAttribute("src", event.target.result); 
	}; 
	reader.readAsDataURL(event.target.files[0]); 
}

// function sel_item(vals) {
// 	i = document.frm.mysel.length;

// 	for (j = 0; j < i; j++) {
// 		if (document.frm.mysel.options[j].value == vals) {
// 			document.frm.mysel.options[j].selected = true;
// 		}
// 	}
// }