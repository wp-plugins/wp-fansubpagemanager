function showHide(id) {
	if ( document.getElementById(id).style.display == "none" ) {
		document.getElementById(id).style.display='';
	} else if ( document.getElementById(id).style.display == "" ) {
		document.getElementById(id).style.display='none';
	}
}
