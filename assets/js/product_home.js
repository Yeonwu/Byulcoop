function isIncluded(finding, str) {
	const expression = new RegExp(finding);
	return expression.test(str);
}

function getTextVal() {
	/*  입력된 검색어를 불러온다(검색창에 입력된 단어 읽어옴)  */

	return document.querySelector('#product-topbar input').value;
}

function updateDisplay() {
	const TYPE_EL = 1;
	var rowList = document.querySelectorAll('.product-content');
	var rowIndex, cellIndex;

	if (rowList === null) {
		clearInterval(interval);
		return;
	}

	for (rowIndex = 0; rowIndex < rowList.length; rowIndex++) {
		if (rowList[rowIndex].nodeType === TYPE_EL) {
			const cellList = rowList[rowIndex].querySelectorAll('[class^=product-]');
			var cellStr = ' ';

			for (cellIndex = 0; cellIndex < cellList.length; cellIndex++) {
				if (cellList[cellIndex].nodeType === TYPE_EL) {
					cellStr += cellList[cellIndex].innerText;
				}
			}

			if (isIncluded(getTextVal(), cellStr)) {
				rowList[rowIndex].classList.remove('w3-hide');
			} else {
				rowList[rowIndex].classList.add('w3-hide');
			}
		}
	}
}

interval = setInterval(updateDisplay, 1);