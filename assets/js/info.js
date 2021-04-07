var page = 0;
var limit;

function showNthPage() {
	const listNum = 9;
	console.log(page);
	document.querySelectorAll('.my_info tr').forEach(function (row, index) {
		if ((index >= listNum * page + 1 && index <= listNum * (page + 1) + 1) || index == 0) {
			row.classList.remove('w3-hide');
		} else {
			row.classList.add('w3-hide');
		}
	});
}

function changePage(event) {
	var pageTo = Number(event.target.innerText) - 1;
	document.querySelectorAll('.page-button')[page].classList.remove('c-theme-dark');
	document.querySelectorAll('.page-button')[pageTo].classList.add('c-theme-dark');
	page = pageTo;
	showNthPage();
}

function pageBefore() {
	page--;
	if (page < 0) {
		page = 0;
		return;
	}
	document.querySelectorAll('.page-button')[page + 1].classList.remove('c-theme-dark');
	document.querySelectorAll('.page-button')[page].classList.add('c-theme-dark');
	showNthPage();
}

function pageNext() {
	page++;
	if (page > limit) {
		page = limit;
		return;
	}
	document.querySelectorAll('.page-button')[page - 1].classList.remove('c-theme-dark');
	document.querySelectorAll('.page-button')[page].classList.add('c-theme-dark');
	showNthPage();
}

function init() {
	var len = document.querySelectorAll('.my_info tr').length;
	limit = Math.floor(len / 9);
	var tmp = `<div class="w3-bar-item w3-button page-button c-theme-dark" onclick="changePage(event)">1</div>`;

	for (var i = 2; i <= limit + 1; i++) {
		var btn = `<div class="w3-bar-item w3-button page-button" onclick="changePage(event)">${i}</div>`;
		tmp += btn;
	}

	document.querySelector('#info-pagination span').innerHTML = tmp;
	showNthPage();
}

init();