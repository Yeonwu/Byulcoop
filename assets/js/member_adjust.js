function adjustSubmit() {
	const idList = document.querySelectorAll(".user_id");
	const nameList = document.querySelectorAll(".user_nm");
	const gradeList = document.querySelectorAll(".user_grade");
	const authList = document.querySelectorAll(".user_auth");
	const emailList = document.querySelectorAll(".user_email");
	const birthList = document.querySelectorAll(".user_birth");
	const imgList = document.querySelectorAll("input[type='file']");
	
	const form = document.querySelector("form");
	
	var sendInfoList = new Array();
	var numberOfData;
	
	var i = 0;
	
	for (i=0; i<nameList.length; i++) {
		var sendInfo = {
			user_id : idList[i].value,
			user_name : nameList[i].value,
			user_grade : gradeList[i].value,
			user_auth : authList[i].value,
			user_email : emailList[i].value,
			user_birth : birthList[i].value,
			user_img : i,
			img_updated: true
		};
		
		sendInfoList.push(sendInfo);
		
		var sendImg = imgList[i].cloneNode();
		form.appendChild(sendImg);
		if (imgList[i].value == "") {
			sendInfo.img_updated = false;
		}
	}
	
	numberOfData = sendInfoList.length;
	sendInfoList = JSON.stringify(sendInfoList);
	
	form.childNodes[1].value = numberOfData;
	form.childNodes[3].value = sendInfoList;
	form.submit();
	
}

function setThumbnail(event) { 
	var reader = new FileReader();
	var img = event.target.parentNode.parentNode.parentNode.parentNode.querySelector('img');
	reader.onload = function(event) { 
		img.setAttribute("src", event.target.result); 
	}; 
	reader.readAsDataURL(event.target.files[0]); 
}