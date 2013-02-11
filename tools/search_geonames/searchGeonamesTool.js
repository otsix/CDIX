function geonamesDrawPoint(x, y) {
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

var geonamesResponseAction = function(r) {
	if(r != null || r.geonames != null || r.geonames.length > 0) {
		var geoname = '';
		for(var i = 0; i < r.geonames.length; i++) {
			geoname = r.geonames[i];
			if(geoname.adminName1 == "Galicia") {
				var lat = geoname.lat;
				var lng = geoname.lng;
				var name = geoname.toponymName;
				var ll_point = new OpenLayers.LonLat(lng, lat);
				var proj_point = ll_point.transform(new OpenLayers.Projection('EPSG:4326'), map.displayProjection);
				var x = proj_point.lon;
				var y = proj_point.lat;
				if(isInMapBounds(x, y)) {
					map.setCenter(new OpenLayers.LonLat(x, y), 6, dragging = true, forceZoomChange = true);
					geonamesDrawPoint(x,y);
					// After found first element in, it breaks
					break;
				}
			}

		} // for
	} else {
		return;
	}
}
function initSearchGeonamesDialog() {
	//Load html code to the boxContainer.searchTab
	$.get('tools/search_geonames/searchGeonamesForm.htm', function(template) {		
				
		$(template).appendTo(".form-section1");
		
		var obj = $(".form-section1").children().last();
		set_expand_collapse_click(obj);

		$("#geonames-btn").button();
		$("#geonames-btn").click(function() {
			var tf = $("#geonames-textfield");

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

			if(value == "" || largo < 3) {
				alert("Debe introducir un topónimo máis longo");
				//return;
			}

			$.ajax({
				url : "http://ws.geonames.org/searchJSON",
				type : "GET",
				data : "q=" + encodeURIComponent(value) + "&country=ES",
				dataType : "json",
				success : geonamesResponseAction,
				error : function(response) {
				    alert("Error na resposta.");
				    //console.log(response);
				}
			});

		});
	});
}