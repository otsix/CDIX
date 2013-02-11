/**
 * Vector Layer where show the geometry result of search operations.
 */
var search_vector = '';

/**
 * xvm_init() function calls XVM and overwrite some parameters:
 *    - styles
 *    - overwrite info_control to get a download_dialog
 *    - load a "search_vector" layer to show some geometries as search results 
 */
function xvm_init() {	
	init();
	//Cambiar css del panel de controles del XVM y color de fondo del control de capas
	$(".olControlLayerSwitcher .layersDiv").css("background-color", "black");
	$(".olControlPanel").css({
		"padding" : "2px",
		"border" : "1px solid grey",
		"top" : "50px",
		"left" : "0px",
		"background-color" : "white",
		"-moz-border-radius" : "5px 5px 5px 5px",
		"-webkit-border-bottom-right-radius" : "5px",
		"-webkit-border-top-right-radius" : "5px",
		"-khtml-border-bottom-right-radius" : "5px",
		"-khtml-border-top-right-radius" : "5px",
		"opacity" : "0.9",
		"filter" : "filter:alpha(opacity=90);"
	});
	$(".olControlPanel div").css("float", "left");

	$("div.olControlMousePosition").css({
		"bottom" : "110px",
		"color" : "black",
		"width" : "100px"
	});
	
	$("div.olControlScale").css({
		"bottom" : "150px",
		"color" : "black",
		"width" : "120px"
	});
	
	$("div.olControlScaleLine").css({
		"bottom" : "150px",
		"color" : "black",
		"width" : "120px"
		
	});
	
	//
	$(".olControlOverviewMapContainer").css("bottom", "107px");

	$(".olControlOverviewMapElement").css({
		"background" : "#000000",
		"opacity" : "0.8",
		"filter" : "filter:alpha(opacity=80)",
		"-moz-border-radius" : "5px 5px 5px 5px",
		"-webkit-border-bottom-left-radius" : "5px",
		"-webkit-border-top-left-radius" : "5px",
		"-khtml-border-bottom-left-radius" : "5px",
		"-khtml-border-top-left-radius" : "5px"
	});

	$(".olControlOverviewMapMinimizeButton").css({
		"background" : "#000000",
		"opacity" : "0.5",
		"filter" : "filter:alpha(opacity=50)"
	});
	
	//Change Info Icon to Download Icon
	$(".olControlWMSGetFeatureInfoItemActive").css({
		'background-image': "url('js/xvm/images/download.png')"
	});
	$(".olControlWMSGetFeatureInfoItemInactive").css({
		'background-image': "url('js/xvm/images/download.png')"
	});	
	
	search_vector = new OpenLayers.Layer.Vector("search_result",{
			style: {
				strokeColor: "red",
				strokeWidth: 3, 
				fillColor: "red", 
				fillOpacity: 0.2,
				pointRadius: 10
			}
		});	
	search_vector.displayInLayerSwitcher = false;
	
	initSplash("presentacion");
	
	///////////////////////// 
	// Load Search Tools
	/////////////////////////
	initSearchPlacesDialog();
	initSearchCoordinatesDialog();
	initSearchCatastroDialog();
	initSearchGeonamesDialog();
	initSearchMapSheetsDialog();
	expand_collapse_accordions()

	//////////////////////////////////////////////////
	// Change infoTool to download tool
	//////////////////////////////////////////////////
	//Change click callback function of the infoTool (download_dialog.js)
	featureInfo = map.getControlsBy('displayClass', 'olControlWMSGetFeatureInfo')[0];
	featureInfo.events.listeners.getfeatureinfo[0].func = getDownloadDialogClickFunction();	
	featureInfo.title = $.i18n.prop("download_tooltip"); //title is not changed... :/
	initDownloadDialog();
	
	//featureInfo.deactivate();
	map.addLayer(search_vector);
	
	//Proj4js.defs["EPSG:4326"] = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";
	//Proj4js.defs["EPSG:23029"] = "+proj=utm +zone=29 +ellps=intl +units=m +no_defs";
	//Proj4js.defs["EPSG:25829"] = "+proj=utm +zone=29 +ellps=GRS80 +units=m +no_defs";


}