/**
 *  NOTE: queryable layers use their title to query 
 */


showDownloadListDialog = function(data) {
	data = $.trim(data);
	resources = $.parseJSON(data);
	if(resources != null && resources != "") {
		$("#downloadDialog").html('');
		//$("#downloadDialog").html('<strong style="color: yellow;"> Descargas: </strong> <br/><br/>');
		$("#downloadDialog").append('<ul>');
		for(var i = 0; i < resources.length; i++) {
			// GRID_NAME comes in the list, but it has just one element on the array "resources[i]"
			//   ... so, (resources[i].length > 1) means a resource with label and link.
			if (typeof(resources[i]) == "object") {
				$("#downloadDialog").append('<li><a href="' + resources[i][1] + '" target="_blank">' + resources[i][0] + '</a></li>');
			} else {
				// GRID_NAME is a string, instead of Object
				$("#downloadDialog").append('<div class="grid_name" style="font-weight:bold; color:yellow; margin-top: 5px"> ' + resources[i] + '</div>');
			}
		}
		//TODO Add other mapsheet series
		$("#downloadDialog").append('</ul>');
	} else {
		$("#downloadDialog").html('<strong style="color: yellow;"> ' + 'Non se atoparon follas para a descarga neste punto. </strong> <br/><br/>');
	}
	$("#downloadDialog").mCustomScrollbar().dialog("open");
	$("#downloadDialog").mCustomScrollbar("update");
}

function getDownloadDialogClickFunction(){

	/**
	 *  "DOWNLOABLE_GROUPNAME" is the group name where all layers queryable for resources are placed
	 */
	var DOWNLOADABLE_GROUPNAME = "Mallas de Descarga";

	var myclick = function(evt) {
		var mallaLayers = map.getLayersBy("group", DOWNLOADABLE_GROUPNAME);
		var mallaNames = "";
		for (var i = 0; i < mallaLayers.length; i++){
			if (mallaLayers[i].getVisibility()){
				mallaNames = mallaNames + mallaLayers[i].name+ ",";
			}
		}
		
		lonLat = map.getLonLatFromPixel(evt.xy);
		//console.log(lonLat);
		lon = lonLat.lon;
		lat = lonLat.lat;
		if(!lonLat) {
			//Maybe map has not yet been properly initialized
			return;
		}
		$.ajax({
			url : 'php/getMallaID.php',
			data : "lon=" + lon + "&lat=" + lat + "&mallas="+mallaNames,
			dataType : 'text',
			success : showDownloadListDialog
		});
		//ajax call
	}//onclick function
	return myclick;
}

function initDownloadDialog(){
	$("#downloadDialog").dialog({
		title : "Descarga de series cartográficas",
		height : 240,
		width : 350,
		modal : false,
		autoOpen : false,
		closeOnEscape : true,
		resizable : true,
		buttons : {
			Cerrar : function() {
				$(this).dialog('close');
			}
		}
	});
}