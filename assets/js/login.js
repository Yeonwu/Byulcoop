function getCookie(name) {
	let matches = document.cookie.match(
		new RegExp('(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)')
	);
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

// 쿠키 저장, 삭제함수
function setCookie(name, value, options = {}) {
	options = {
		path: '/',
		// 필요한 경우, 옵션 기본값을 설정할 수도 있습니다.
		...options,
	};

	if (options.expires instanceof Date) {
		options.expires = options.expires.toUTCString();
	}

	let updatedCookie = encodeURIComponent(name) + '=' + encodeURIComponent(value);

	for (let optionKey in options) {
		updatedCookie += '; ' + optionKey;
		let optionValue = options[optionKey];
		if (optionValue !== true) {
			updatedCookie += '=' + optionValue;
		}
	}
	document.cookie = updatedCookie;
}

function deleteCookie(name) {
	setCookie(name, '', {
		'max-age': -1,
	});
}

// 로그인 했을때 작동
function onSignIn(googleUser) {
	let profile = googleUser.getBasicProfile();
	let id_token = googleUser.getAuthResponse().id_token;
	
	if(getCookie('verify_token') == null) {
		let xhr = new XMLHttpRequest();
		xhr.open('POST', './view/verify_login.php');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function () {
			if(xhr.readyState === XMLHttpRequest.DONE) {
				console.log(xhr.responseText);
				let json = JSON.parse(xhr.responseText);
				if (json.success == true) {
					setCookie('verify_token', json.verify_token, 1);
					setCookie('user_name', profile.getName(), 1);
					setCookie('user_img', profile.getImageUrl(), 1);
					setCookie('user_email', profile.getEmail(), 1);

					if(document.querySelector('#info img') == null) location.reload();

					document.querySelector('#info img').src = getCookie('user_img');
					document.querySelector('#login-container').classList.add('w3-hide');
				} else {
					alert('다시 로그인 해주세요');
				}
			}
		};
		xhr.send('idtoken=' + id_token);
	} else {
		setCookie('user_name', profile.getName(), 1);
		setCookie('user_img', profile.getImageUrl(), 1);
		setCookie('user_email', profile.getEmail(), 1);
		document.querySelector('#info img').src = getCookie('user_img');
		document.querySelector('#login-container').classList.add('w3-hide');
	}
}

// 로그아웃 했을때 작동
function signOut() {
	var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		console.log('User signed out.');
		deleteCookie('user_name');
		deleteCookie('user_img');
		deleteCookie('user_email');
		deleteCookie('verify_token');
	});
	
	document.querySelector('#info img').src = '#';
	document.querySelector('#login-container').classList.remove('w3-hide');
}