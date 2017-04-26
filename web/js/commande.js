$( document ).ready(function() {
	$('.arrowUpDown').click(function(event){
		event.preventDefault();
		var classArrowUp = "btn round arrowUpDown fa fa-chevron-up clickable";
		var classArrowDown = "btn round arrowUpDown fa fa-chevron-down clickable";
		var dataTarget = $(this).data("target")
		var classActuelle = $(this).attr('class');
		if(classActuelle == classArrowUp || classActuelle == (classArrowUp + ' collapsed')){
			$(this).removeClass(classArrowUp);
			$(this).addClass(classArrowDown);
		}else if( classActuelle == classArrowDown || classActuelle == (classArrowDown + ' collapsed')){
			$(this).removeClass(classArrowDown);
			$(this).addClass(classArrowUp);
		}
	});
});