function expand_collapse_accordions(){ // Script del Navegador 
	$("ul.form-section2 .accordion_expanded").show();
	$("ul.form-section").hide();
	//$("a.expandable").click = null;
	// $("a.expandable").click(function(e){
		// var expandable = $(this).parent().find("ul.form-section");
		// $('.expandable').parent().find("ul.form-section").not(expandable).slideUp('slow');
		// $('.accordion_expanded').parent().find("ul.form-section2").not(expandable).slideUp('slow');
		// expandable.slideToggle('slow');
		// e.preventDefault();
	// });
	//set_expand_collapse_click($("a.expandable"));
	// $("a.accordion_expanded").click(function(e){
		// var expandable = $(this).parent().find("ul.form-section2");
		// $('.expandable').parent().find("ul.form-section").not(expandable).slideUp('slow');
		// expandable.slideToggle('slow');
		// e.preventDefault();
	// });
}

function my_click_expand(e){
	var my_accordion = $(this).parent();
	$(my_accordion).parent().not(my_accordion).children().children().not('a').slideUp('slow');
	//Only div and ul are slideToggle
	my_accordion.children().not('a').filter('div').slideToggle('slow');
	my_accordion.children().not('a').filter('ul').slideToggle('slow');
	e.preventDefault();
}

function set_expand_collapse_click(obj){ 
	obj.children('a').click(my_click_expand);
	obj.children().not('a').hide();
}


$(document).ready(
	//it must called after all elements are ready (some are async)
	expand_collapse_accordions()
);