<!--
총 매출

매점 협동 조합 판매자 사이트 디자인 34p
 -->

<?php

require_once ("./view/user_auth.php");

$auth = getAuth();
if ( $auth === '소비자' || $auth === '조합원' ) {
	echo "
	<script>
		alert('권한이 없습니다');
		window.location.href = './index.php?id=home';
	</script>
	";
}

?>

<style>
	.checkbox-label {display: flex; align-items:center;width:200px;}
	.checkbox {display: inline-block; width: 17px; height: 17px; position: relative; margin-right: 4px;}
	input:checked+.checkbox {background-color: var(--theme-dark) !important;}
	input+.checkbox>i {display: none;color:var(--white); position: absolute; top:1px; left: 1px;}
	input:checked+.checkbox>i {display: inline;}
	form {margin-bottom: 16px;}
	form {overflow: hidden; transition: height 0.4s;}
	form.closed {height: 49px;}
	form:not(.closed)>*:first-child .la-angle-down {transform: rotate(180deg);}
	#loading {position: absolute; top:0; left:0; background: var(--black-transparent); width: 100%; height: 100%; z-index: 1;}
</style>

<div id="all_result-container" class="relative">
	<div id="loading" class="w3-hide w3-center">
		<div class="w3-padding t-white">
			검색 결과를 불러오는 중입니다.
		</div>
	</div>

	<form class="w3-padding">
		<div class="w3-padding flex">
			<label class="center-container">
				<i class="fas fa-search w3-center t-dark-gray ht-theme hover-pointer"></i>
				<input type="button" class="w3-hide" onclick="handleSubmit();">
			</label>
			<div class="center" style="margin-left: auto;">
				검색 필터
				<i class="margin-l-12 las la-angle-down w3-right ht-theme hover-pointer" onclick="toggleFilter();"></i>
			</div>
		</div>
		<hr>
		<label class="checkbox-label">
			<input type="checkbox" name="type_sales" class="w3-hide">
			<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
			판매기록
		</label>
		<label class="checkbox-label">
			<input type="checkbox" name="type_charge" class="w3-hide">
			<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
			충전기록
		</label>
		<div id="charge-detail" style="padding-left: 36px;" class="w3-hide">
			<label class="checkbox-label">
				<input type="checkbox" name="charge_normal" class="w3-hide" checked>
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				일반충전
			</label>
			<label class="checkbox-label">
				<input type="checkbox" name="charge_coupon" class="w3-hide">
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				쿠폰
			</label>
			<label class="checkbox-label">
				<input type="checkbox" name="charge_etc" class="w3-hide">
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				기타
			</label>
		</div>
		<label class="checkbox-label">
			<input type="checkbox" name="type_refund" class="w3-hide">
			<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
			환불기록
		</label>
		<hr>
		<div>
			<div class="margin-b-8">
				<label class="checkbox-label">
					<input type="checkbox" name="set-start-date" class="w3-hide">
					<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
					시작날짜 설정하기
				</label>
				<label class="checkbox-label">
					<input type="checkbox" name="set-end-date" class="w3-hide">
					<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
					끝날짜 설정하기
				</label>
			</div>
			<div>
				<input name="start-date" class="b-gray fb-theme w3-padding w3-round" type="date" disabled>
				<i class="las la-minus"></i>
				<input name="end-date" class="b-gray fb-theme w3-padding w3-round" type="date" disabled>
			</div>
		</div>
		<hr>
		<div>
			<label class="checkbox-label">
				<input type="checkbox" name="set-seller" class="w3-hide">
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				결제자 설정하기
			</label>
			<input name="seller_name" class="b-gray fb-theme w3-padding w3-round margin-b-8" type="text" disabled>

			<label class="checkbox-label">
				<input type="checkbox" name="set-customer" class="w3-hide">
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				소비자 설정하기
			</label>
			<input name="customer_name" class="b-gray fb-theme w3-padding w3-round margin-b-8" type="text" disabled>

			<label class="checkbox-label">
				<input type="checkbox" name="set-goods" class="w3-hide">
				<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
				상품 설정하기
			</label>
			<input name="goods_name" class="b-gray fb-theme w3-padding w3-round" type="text" disabled>
		</div>
		<hr>
			<div class="margin-b-8">
				<label class="checkbox-label">
					<input type="checkbox" name="set-min-money" class="w3-hide">
					<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
					최소 거래액 설정하기
				</label>
				<label class="checkbox-label">
					<input type="checkbox" name="set-max-money" class="w3-hide">
					<span class="checkbox c-gray hc-dark-gray"><i class="las la-check"></i></span>
					최대 거래액 설정하기
				</label>
			</div>
			<div>
				<input name="min-money" class="b-gray fb-theme w3-padding w3-round" type="number" disabled>
				<i class="las la-minus"></i>
				<input name="max-money" class="b-gray fb-theme w3-padding w3-round" type="number" disabled>
			</div>
		<hr>
		<input name="limit" type="hidden" value="300">
		<input id="set-paging" name="offset" type="hidden" value="0">
	</form>
	
	<div id="search-result">
	</div>
	
</div>

<script>
	
	function linkCheckbox(input, checkbox) {
		checkbox.addEventListener('change', function() {
			if(checkbox.checked == true) {
				input.disabled = false;
			} else {
				input.disabled = true;
			}
		});
	}
	
	function get(str) {return document.querySelector(str);}
	function getAll(str) {return document.querySelectorAll(str);}
	
	function toggleFilter(option = false) {
		if(!option) {
			get('form').classList.toggle('closed');
		} else if(option == 'open') {
			get('form').classList.remove('closed');
		} else {
			get('form').classList.add('closed');
		}
	}
	
	function setLink() {
		linkCheckbox(get('*[name="start-date"]'), get('*[name="set-start-date"]'));
		linkCheckbox(get('*[name="end-date"]'), get('*[name="set-end-date"]'));
		linkCheckbox(get('*[name="seller_name"]'), get('*[name="set-seller"]'));
		linkCheckbox(get('*[name="customer_name"]'), get('*[name="set-customer"]'));
		linkCheckbox(get('*[name="goods_name"]'), get('*[name="set-goods"]'));
		linkCheckbox(get('*[name="min-money"]'), get('*[name="set-min-money"]'));
		linkCheckbox(get('*[name="max-money"]'), get('*[name="set-max-money"]'));
		
		let charge = get('*[name="type_charge"]');
		let chargeType = get('#charge-detail');
		charge.addEventListener('change', function() {
			if(charge.checked == true) {
				chargeType.classList.remove('w3-hide');
			} else {
				chargeType.classList.add('w3-hide');
			}
		});
	}
	
	function handleSubmit() {
		event.preventDefault();
		if(!(get('*[name="type_sales"]').checked || get('*[name="type_charge"]').checked || get('*[name="type_refund"]').checked)) {
			
		}
		makeRequest();
	}
	
	var httpRequest;

	function makeRequest() {
		let formData = new FormData(document.querySelector('form'));
		httpRequest = new XMLHttpRequest();

		if (!httpRequest) {
			alert('페이지를 로드하지 못했습니다.');
			return false;
		}
		get('#loading').classList.toggle('w3-hide');
		httpRequest.onreadystatechange = respondRequest;
		httpRequest.open('POST', './view/all_result_mysql.php');
		httpRequest.send(formData);
	}

	function respondRequest() {
		<?php if(CONF_ERR_PRINT) { ?>
		console.log(httpRequest);
		<?php }?>
		if (httpRequest.readyState === XMLHttpRequest.DONE) {
			if (httpRequest.status === 200) {
				let table = document.querySelector('#search-result');
				table.innerHTML = httpRequest.responseText;
				
				let paging = document.querySelector("form #set-paging");
				paging.remove();
				
				paging = table.querySelector("#set-paging");
				document.querySelector('form').appendChild(paging);
				
				toggleFilter('close');
				document.body.scrollTop = 0;
  				document.documentElement.scrollTop = 0;
				get('#loading').classList.toggle('w3-hide');
			} else {
				alert('페이지를 로드하지 못했습니다.');
				console.log(httpRequest.responseText);
			}
		}
	}
	
	function nextPage() {
		get('form').classList.remove('closed');
		window.location.href = '#set-paging';
	}
	
	setLink();
	
</script>
