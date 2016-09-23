function showLogin() {
	document.getElementById("backgroundCover").style.zIndex = 1;
	document.getElementById("backgroundCover").style.transition = "opacity .3s";
	document.getElementById("backgroundCover").style.opacity = "0.9";
	document.getElementById("loginForm").style.zIndex = 1;
	document.getElementById("loginForm").style.transition = "opacity .3s";
	document.getElementById("loginForm").style.opacity = "1.0";
}

function showCreate() {
	document.getElementById("backgroundCover").style.zIndex = 1;
	document.getElementById("backgroundCover").style.transition = "opacity .3s";
	document.getElementById("backgroundCover").style.opacity = "0.9";
	document.getElementById("createForm").style.zIndex = 1;
	document.getElementById("createForm").style.transition = "opacity .3s";
	document.getElementById("createForm").style.opacity = "1.0";
}


function hideLogin() {
	document.getElementById("backgroundCover").style.transition = "all .3s";
	document.getElementById("loginForm").style.transition = "all .3s";
	document.getElementById("createForm").style.transition = "all .3s";
	document.getElementById("backgroundCover").style.opacity = "0";
	document.getElementById("loginForm").style.opacity = "0";
	document.getElementById("createForm").style.opacity = "0";

	document.getElementById("backgroundCover").style.zIndex = -1;
	document.getElementById("loginForm").style.zIndex = -1;
	document.getElementById("createForm").style.zIndex = -1;
}