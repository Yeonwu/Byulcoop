function addRefund(event) {
	let target = event.target;
	let data = target.querySelectorAll('input');
	let printData = new Array();

	let refund_table = document.querySelector('#refund-refund-table tbody');
	let sales_table = document.querySelectorAll('#refund-sales-table tr:not(.table_first)');

	let row = document.createElement('tr');
	

	data.forEach((x, i) => (printData[i] = x.value));
	
	let nth = printData[5];
	let amoutOfSale = Number(sales_table[nth].children[2].innerText);
	
	if(amoutOfSale <= 0){
		return;
	}
	
	sales_table[nth].children[2].innerText = amoutOfSale - 1;

	row.innerHTML = `
		<td class='w3-border w3-container'>${printData[0]}</td>
		<td class='w3-border w3-container'>${printData[1]}</td>
		<td class='w3-border w3-container'>1</td>
		<td class='w3-border w3-container'>${printData[3]}원</td>
		<td class='w3-border w3-container'>${printData[4]}</td>
		<td class='w3-border w3-container'>
			<button class='w3-button w3-border' onclick='removeRefund(event)'>
				-
				<input type='hidden' name='nth' value='${printData[5]}' />
				<input type='hidden' name='time' value='${printData[0]}' />
				<input type='hidden' name='goods_name' value='${printData[1]}' />
				<input type='hidden' name='goods_num' value='1' />
				<input type='hidden' name='goods_price' value='${printData[3]}' />
				<input type='hidden' name='customer_name' value='${printData[4]}' />
				<input type='hidden' name='pay_num' value='${printData[6]}' />
			</button>
		</td>
	`;
	
	refund_table.appendChild(row);
}

function removeRefund(event) {
	let nth = event.target.querySelector('input').value;
	let sales_table = document.querySelectorAll('#refund-sales-table tr:not(.table_first)');
	let refund_table = document.querySelector('#refund-refund-table tbody');
	
	sales_table[nth].children[2].innerText = Number(sales_table[nth].children[2].innerText) + 1;
	
	refund_table.removeChild(event.target.parentElement.parentElement);
}

function submitRefund() {
	
	if(!confirm("환불하시겠습니까?")) {
		return;
	}
	
	let sendInfoList = new Array();
	
	let refund_table = document.querySelectorAll('#refund-refund-table tbody button');
	
	refund_table.forEach(function(x) {
		let info = x.querySelectorAll('input');
		
		sendInfoList.push({
			goods_name: info[2].value,
			pay_num: info[6].value
		});
		
	});
	
	sendInfoList = JSON.stringify(sendInfoList);
	
	document.querySelector('form input').value = sendInfoList;
	
	document.querySelector('form').submit();
}
