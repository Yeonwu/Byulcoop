var interval;

function isIncluded(finding, str) {  /*   두 변수 모두 문자열을 받는다. str에 finding이 포함되어있는지 체크 후 포함하면 true, 포함하지 않으면 false를 반환한다.  */ 
	const expression = new RegExp(finding);
	return expression.test(str);
}

function getTextVal() {   /*  입력된 검색어를 불러온다(검색창에 입력된 단어 읽어옴)  */
	return document.querySelector('#serch_name_txt').value;
}

function updateDisplay() {  /*  검색 후 조건에 맞는 tr만 남기고 나머지는 숨긴다.  */
	const TYPE_EL = 1;  /*  태그가 아닌 text가 childNodes에 들어있어서 만든 체크용 상수  */
	const HEAD_INDEX = 0;  /*  테이블의 head의 인덱스 번호  */
	var rowList = document.querySelectorAll('tr');  /*  tr 배열 아직은 테이블 전체를 담아둠  */
	var rowIndex, cellIndex;
	var nth = 1;
 
	for (rowIndex = 0; rowIndex < rowList.length; rowIndex++) {  /*  rowList를 하나하나 체크하는 반복문  */
		if (rowIndex === HEAD_INDEX) {  /*  만약 지금 체크하고 있는 tr태그가 head라면 다음 순서로 점프  */
			continue;
		}
		if (rowList[rowIndex].nodeType === TYPE_EL) { /*  만약 지금 체크하고 있는 태그가 text가 아닌 tr태그라면  */
			const cellList = rowList[rowIndex].childNodes; /*  td태그 배열 생성  */
			var cellStr = ' '; /*  문자열 담아놓을 변수  */

			for (cellIndex = 0; cellIndex < cellList.length; cellIndex++) { /*  td태그 안의 내용을 cellStr에 더한다  */
				if (cellList[cellIndex].nodeType === TYPE_EL) {
					cellStr += cellList[cellIndex].innerText;
				}
			}

			if (isIncluded(getTextVal(), cellStr)) { /*  만약 검색결과에 포함되어 있으면  */
				rowList[rowIndex].classList.remove('w3-hide'); /*  숨김 해제  */
				if (nth%2) {
					rowList[rowIndex].classList.remove('table-impact');
				} else {
					rowList[rowIndex].classList.add('table-impact');
				}
				nth++;
			} else {
				rowList[rowIndex].classList.add('w3-hide'); /*  아니면 숨김  */
			}
		}
	}
}

interval = setInterval(updateDisplay, 250);
document.querySelector("#search_name_Btn").onclick = updateDisplay;