/*
-------------------------------------------------------------------------
검색 관련 함수  -  다른 페이지에서도 많이 쓸 것 같아서 주석 달아 놓겠습니다 참고하세요
-------------------------------------------------------------------------
*/
var interval;

function isIncluded(finding, str) {  /*   두 변수 모두 문자열을 받는다. str에 finding이 포함되어있는지 체크 후 포함하면 true, 포함하지 않으면 false를 반환한다.  */ 
	const expression = new RegExp(finding);
	return expression.test(str);
}

function getTextVal() {   /*  입력된 검색어를 불러온다(검색창에 입력된 단어 읽어옴)  */
	return document.querySelector('#member-searchTxt').value;
}

function updateDisplay() {  /*  검색 후 조건에 맞는 tr만 남기고 나머지는 숨긴다.  */
	const TYPE_EL = 1;  /*  태그가 아닌 text가 childNodes에 들어있어서 만든 체크용 상수  */
	var rowList = document.querySelectorAll('.member-user-row');  /*  tr 배열 아직은 테이블 전체를 담아둠  */
	var rowIndex, cellIndex;
	var nth = 1;
	let noResult = true;
	
	for (rowIndex = 0; rowIndex < rowList.length; rowIndex++) {  /*  rowList를 하나하나 체크하는 반복문  */
		
		if (rowList[rowIndex].nodeType === TYPE_EL) { /*  만약 지금 체크하고 있는 태그가 text가 아닌 tr태그라면  */
			var cellStr = rowList[rowIndex].innerText; /*  innerText 담아놓을 변수  */

			if (isIncluded(getTextVal(), cellStr)) { /*  만약 검색결과에 포함되어 있으면  */
				noResult = false;
				rowList[rowIndex].classList.remove('w3-hide'); /*  숨김 해제  */
				rowList[rowIndex].children[1].innerText = nth;
				if (nth%2) {
					rowList[rowIndex].classList.add('table-impact');
				} else {
					rowList[rowIndex].classList.remove('table-impact');
				}
				nth++;
			} else {
				rowList[rowIndex].classList.add('w3-hide'); /*  아니면 숨김  */
			}
		}
	}
	
	if(noResult) {
		document.querySelector("#member-noResult-message").classList.remove('w3-hide');
	} else {
		document.querySelector("#member-noResult-message").classList.add('w3-hide');
	}
	
}

document.querySelector('#member-searchTxt').onkeyup = function() {
	updateDisplay();
};

/*
-------------------------------------------------------------------------
검색 관련 함수 끝
-------------------------------------------------------------------------
*/
/* 체크박스관련 함수 */
function selectAllRows() {
	const ROW_LIST = document.querySelectorAll(".member-user-row");
	const TYPE_EL = 1;
	
	var rowIndex, row;
	
	const CHANGE_TO = document.querySelector('#member-table-head input').checked;
	for(rowIndex = 2; rowIndex < ROW_LIST.length; rowIndex++) {
		
		row = ROW_LIST[rowIndex];
		
		const HIDDEN = row.classList.contains('w3-hide');
		if (!HIDDEN){
			row.querySelector('input[type="checkbox"]').checked = CHANGE_TO;
		}
	}
}

var checkBoxList = document.querySelectorAll('#member-table input');

for (var i = 1; i<checkBoxList.length; i++) {
	var checkbox = checkBoxList[i];
	checkbox.onclick = function () {
		document.querySelector('#member-table-head input').checked = false;
	}
}

document.querySelector('#member-table-head input').onclick = selectAllRows;

function getChecked() {
	var checkBoxList = document.querySelectorAll('#member-table input');
	var idList = document.querySelectorAll('#member-table .db_id');
	var returnList = new Array();
	
	checkBoxList.forEach(function(checkbox, index) {
		if ((checkbox.checked) && (index !== 0)) {
			returnList.push(idList[index-1].innerText);
		}
	})
	
	return returnList;
}

/*-------------------------------------------------------------------------*/

/*사용자 정보 관리 함수*/

function deleteUser() {
	if ( confirm('정말 삭제하시겠습니까?') ) {
		var deleteList = getChecked();
		var form = document.querySelectorAll('#deleteUserInfo input');
		form[0].value = deleteList.length;
		form[1].value = JSON.stringify(deleteList);

		form[0].parentNode.submit();
	}
}

function adjustUser() {
	var adjustList = getChecked();
	var form = document.querySelectorAll('#adjustUserInfo input');
	
	form[0].value = adjustList.length;
	form[1].value = JSON.stringify(adjustList);
	
	form[0].parentNode.submit();
}

function editUserAuth(event) {
	var editList = getChecked();
	var form = document.querySelectorAll('#userAuth input');
	var authTo = {
		'소비자로 변경' : '소비자',
		'조합원으로 변경' : '조합원',
		'판매자로 변경' : '판매자',
		'팀장으로 변경' : '팀장',
		'관리자로 변경' : '관리자'
	};
	
	form[0].value = editList.length;
	form[1].value = JSON.stringify(editList);
	form[2].value = authTo[event.target.innerText]
	
	form[0].parentNode.submit();
}

function editUserGrade(event) {
	var editList = getChecked();
	var form = document.querySelectorAll('#userGrade input');
	var gradeTo = {
		'6학년으로 변경' : '6학년',
		'7학년으로 변경' : '7학년',
		'8학년으로 변경' : '8학년',
		'9학년으로 변경' : '9학년',
		'10학년으로 변경' : '10학년',
		'11학년으로 변경' : '11학년',
		'12학년으로 변경' : '12학년'
	};
	
	form[0].value = editList.length;
	form[1].value = JSON.stringify(editList);
	form[2].value = gradeTo[event.target.innerText]
	
	form[0].parentNode.submit();
}
