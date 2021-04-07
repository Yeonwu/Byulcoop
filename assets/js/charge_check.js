function getCookie(cookieName) {
	//출처: https://kkotkkio.tistory.com/64 [KKOTKKIO'S CAVE]
	cookieName = cookieName + '=';
	var cookieData = document.cookie;
	var start = cookieData.indexOf(cookieName);
	var cookieValue = '';
	if (start != -1) {
		start += cookieName.length;
		var end = cookieData.indexOf(';', start);
		if (end == -1) end = cookieData.length;
		cookieValue = cookieData.substring(start, end);
	}
	return unescape(cookieValue);
}


function chargeSubmit() {
	document.querySelector('form').submit();
}

document.querySelector("#charge-myInfo-name").innerText = "승인자 이름 : " + getCookie('user_name');
document.querySelector("#charge-myInfo-email").innerText = "승인자 이메일 : " + getCookie('user_email');
