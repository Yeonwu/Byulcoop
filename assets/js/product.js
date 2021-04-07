var IS_STOCK_CHANGING = false;
var IS_PRODUCT_CHANGING = false;

function isIncluded(finding, str) {  /*   두 변수 모두 문자열을 받는다. str에 finding이 포함되어있는지 체크 후 포함하면 true, 포함하지 않으면 false를 반환한다.  */ 
	
	const expression = new RegExp(finding);
	return expression.test(str);
}

function getTextVal() {   /*  입력된 검색어를 불러온다(검색창에 입력된 단어 읽어옴)  */
	
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

function deleteProduct(event) {
	
	if ( confirm("정말 삭제하시겠습니까?") ) {
		var product_id = event.target.parentNode.parentNode.querySelector('.product-id').innerText;
		document.querySelector('#deleteForm input[type=hidden]').value = product_id;
		document.querySelector('#deleteForm').submit();
	}
	
}

function adjustProduct(event) {
	
	var product_id = event.target.parentNode.parentNode.querySelector('.product-id').innerText;
	document.querySelector('#adjustForm input[type=hidden]').value = product_id
	document.querySelector('#adjustForm').submit();
	
}

function editStock(event) {
	
	if ( IS_STOCK_CHANGING ) {
		alert("먼저 마치지 않은 재고수정을 마쳐주세요");
		return;
	}
	
	var stockCell = event.target.parentNode;
	var stockNow = Number(stockCell.querySelector('.product-stock').innerText);
	
	IS_STOCK_CHANGING = true;
	
	stockCell.innerHTML = "<input type='number' value='" + stockNow + "'> <i class='fas fa-check custom-button' onclick='submitStock(event)'></i>";
}

function submitStock(event) {
	
	var product_id = event.target.parentNode.parentNode.parentNode.querySelector('.product-id').innerText;
	var product_name = event.target.parentNode.parentNode.parentNode.querySelector('.product-name').innerText;
	var product_stock = event.target.parentNode.parentNode.querySelector('input[type=number]').value;
	
	IS_STOCK_CHANGING = false;
	
	document.querySelectorAll('#stockForm input')[0].value = product_id;
	document.querySelectorAll('#stockForm input')[1].value = product_stock;
	document.querySelectorAll('#stockForm input')[2].value = product_name;
	
	document.querySelector('#stockForm').submit();
}