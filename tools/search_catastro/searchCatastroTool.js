function catastroDrawPoint(x, y) {
	geojson = {
		"type" : "Point",
		"coordinates" : [x, y]
	};
	search_vector.removeAllFeatures();
	if(geojson) {
		var geojson_format = new OpenLayers.Format.GeoJSON();
		var feat = geojson_format.read(geojson);
		search_vector.addFeatures(feat);
		var overviewmap = map.getControlsByClass('OpenLayers.Control.OverviewMap');
		if (overviewmap.length == 1){
			overviewmap[0].update();
		}
	} else {
		//alert("Non respondiu nada");
	}
}

function isInMapBounds(x, y) {
	var bounds = map.getMaxExtent();
	return bounds.contains(x, y, false);
}

function initSearchCatastroDialog() {

	$.get('tools/search_catastro/searchCatastroForm.htm', function(template) {
		$(template).appendTo(".form-section1");

		var obj = $(".form-section1").children().last();
		set_expand_collapse_click(obj);
		$("#catastro-btn").button();
		$("#search-dialog").dialog();
		$("#catastro-btn").click(function() {
			var tf = $("#RC-textfield");

			///////////// TODO: Ajax Waiting code... /////////////
			tf.ajaxStart(function() {
				$(this).css('background-color', 'red');
			});
			tf.ajaxComplete(function() {
				$(this).css('background-color', 'white');
			});
			////////////////////////////////////////////////////
			var value = tf.val();
			var largo = value.length;

			if(value == "" || largo != 14) {
				alert("Debe introducir unha referencia de 14 caracteres");
				//return;
			}
			$.ajax({
				url : "tools/search_catastro/catastro_proxy.php",
				type : "GET",
				data : "Provincia=&Municipio=&SRS="+map.displayProjection+"&RC=" + value,
				dataType : "xml",
				success : function(response) {
					var r = $(response);
					var x = r.find('xcen').text();
					var y = r.find('ycen').text();
					if(x != "" & y != "") {
						if(isInMapBounds(x, y)) {
							map.setCenter(new OpenLayers.LonLat(x, y), 15, dragging = true, forceZoomChange = true);
							catastroDrawPoint(x, y);
						} else {
							return;
						}
					} else {
						return;
					}
				},
				error : function(response) {
					alert("Error na resposta.");
				}
			});

		});
	});
}