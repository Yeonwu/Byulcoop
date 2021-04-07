var IS_OPEN = true;

function setCookie(name, value, day) {
	var date = new Date();
	date.setTime(date.getTime() + day * 60 * 60 * 24 * 1000);
	document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
}

function getCookie(name) {
	var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
	return value? value[2] : null;
}

function deleteCookie(name) {
	var date = new Date();
	document.cookie = name + "= " + "; expires=" + date.toUTCString() + "; path=/";
}

function closeNav() {
	document.querySelector("#body-content").classList.remove('navAbled');
	document.querySelector("#nav-bar").classList.remove('navAbled');
	document.querySelector("#nav-bar i").classList.remove("rotation");
}

function openNav() {
	document.querySelector("#body-content").classList.add('navAbled');
	document.querySelector("#nav-bar").classList.add('navAbled');
	document.querySelector("#nav-bar i").classList.add("rotation");
}

function navClick() {
	switch (IS_OPEN) {
		case true:
			closeNav();
			break;
		case false:
			openNav();
			break;
	}
	IS_OPEN = !IS_OPEN;
	deleteCookie("nav_open");
	setCookie("nav_open", IS_OPEN, 7);
}

function initNav() {
	
	if(getCookie('nav_open') != null) {
		IS_OPEN = (getCookie('nav_open') == 'true');
	}
	// navClick();
	console.log('init');
}

document.querySelector('#navBtn').onclick = navClick;
window.onload = initNav();
