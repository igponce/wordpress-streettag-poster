<?php ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta lang='es-es' content='Español España'>
    <title>Administrador Google Street View</title>
    <script src="jquery-1.3.1.js" type="text/javascript"></script>
</head>
<body>
    
<h1>Point-Logger - Google Steet View</h1>

	<div id="GMapContainer"></div>
	<div id="GStreetViewContainer"></div>

        <label id="TagLocation">[TAG LOCATION]</label>

<!--
        <p><label>Google Maps Embed:</label><input type="text" id="embed_googlemaps" value="Google maps embed code"></p>
        <p><label>Streetview Embed:</label><input type="text" id="embed_streetview" value="Google streetview embed code"></p>
-->

<p>Buscador de direcciones: <input type="text" id="GMapSearcher" value="" size=128/><span id="searching"></span></p>

        <form name="pointlogger" action="mappost.php" method="POST">
            
        <p>Nombre del POI: <input type="text" name="title" value="" size=128/> <input type="submit"  name="GeoTAG It!" /> </p>
	
        <!-- <p><label class="GMdata">Latitud:</label> -->
           <input type="text" id="googleMaps_lat" name="googleMaps_lat" value="" style="visibility:hidden"/></p>
	<!-- <p><label class="GMdata">Longitud:</label> -->
           <input type="text" id="googleMaps_lng" name="googleMaps_lng" value="" style="visibility:hidden"/></p>
        <!-- <p><label class="GMdata">Map Zoom</label> -->
            <input type="text" id="googleMaps_mapzoom" name="googleMaps_mapzoom" value="14" style="visibility:hidden"/></p>
	<!-- <p><label class="GMdata">Grados:</label> -->
           <input type="text" id="googleMaps_yaw" name="googleMaps_yaw" value="" style="visibility:hidden"/></p>
	<!-- <p><label class="GMdata">Inclinación:</label> -->
           <input type="text" id="googleMaps_pitch" name="googleMaps_pitch" value="" style="visibility:hidden"/></p>
	<!-- <p><label class="GMdata">Zoom:</label> -->
           <input type="text" id="googleMaps_zoom" name="googleMaps_zoom" value="" style="visibility:hidden"/></p>
        
        </form>
        
	<!-- Esto deberia estar en un archivo .css aparte-->
	<style>
		#GMapContainer{float:left; width:50%; height:300px; margin-bottom:20px;}
		#GStreetViewContainer{float:right; width:49%; height:300px;margin-bottom:20px;}
		#GStreetViewError{position:absolute;right:10px; width:49%; height:300px;line-height:300px; margin-bottom:20px;z-index:9999; background:white; text-align:center; font-weight:bold;}
		.GMdata{width:100px; float:left;display:block;}
		.gsc-search-button{background:white; padding:5px;}
		.gsc-input{width:200px;}
	</style>
	
	<!-- Llamada al script -->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo($google_maps_api_key) ?>" type="text/javascript"></script>
	
	<!-- Esto deberia estar en un archivo .js aparte -->
	<script type="text/javascript">
		
		var GoogleMap;
		var streetView;
		var GoogleGeoCoder;
		var GoogleStreetViewClient;
		var initLat = 41.387917;
		var initLong = 2.1699187;
		var seachingString = "Buscando...";
		var errorNoStreetView = "Error : No hay fotos asociadas.";
		var errorNoFlash = "No puede usarse Google Street View sin tener instalado flash";
                
		jQuery("document").ready(	function () {
		  if (GBrowserIsCompatible()) {
		    GoogleMap = new GMap2(document.getElementById("GMapContainer"));
		    GoogleMap.setCenter(new GLatLng(initLat,initLong), 14);
		    GoogleMap.addControl(new GLargeMapControl());
		    GoogleMap.addControl(new GMapTypeControl());
		    svOverlay = new GStreetviewOverlay();
		    GoogleMap.addOverlay(svOverlay);
		    streetView = new GStreetviewPanorama(document.getElementById("GStreetViewContainer"));
		    streetView.setLocationAndPOV(new GLatLng(initLat,initLong));
		    GoogleStreetViewClient = new GStreetviewClient(); 
		    GEvent.addListener(streetView, "error", handleNoFlash);
                    
		   	GoogleGeoCoder = new GClientGeocoder();
                        
                        jQuery ("#TagLocation").click(function(event){
                            
                            var embed = "http://maps.google.com/staticmap?center="
                                    /* long,lat */ + jQuery("#googleMaps_lat").val() + ',' + jQuery ("#googleMaps_lng").val()
                                    /* zoom */     + "&zoom=" + jQuery("#googleMaps_mapzoom").val() 
                                    /* tipo */     + "&maptype=mobile"
                                    /* tamano */   + "&size=500x335"
                                    /* api_key */  + "&key=ABQIAAAAR96X-1DDYQbFf08Wgr9pwBQNxvaK9NZIEwjRpohVmN1FzgtahhQuX8m-QfnqpCQ3S5vnk9TQCC8baQ&sensor=false";
                                    
                            window.alert(embed);
                            jQuery("#embed_googlemaps").val(embed);
                        });
                        
			jQuery("#GMapSearcher").keyup(function(event){
				if ( jQuery(this).val().length > 3 )
				{
					jQuery("#searching").html(seachingString);
					GoogleGeoCoder.getLatLng( jQuery(this).val(), function(point){
						if (point){
							GoogleMap.setCenter(point, 15);
							GoogleStreetViewClient.getNearestPanorama(point,streetViewChangeLocation);
							jQuery("#searching").html("");
                                                        jQuert("#googleMaps_mapzoom").val("15");
						}
						});
				}
			});

			GEvent.addListener(GoogleMap,"click", function(overlay,latlng) {
				GoogleStreetViewClient.getNearestPanorama(latlng,streetViewChangeLocation);
                                jQuery("#googleMaps_mapzoom").val (GoogleStreetViewClient.getZoom());
			});
                        
                        GEvent.addListener(GoogleMap,"zoomend", function(oldLevel, newLevel) {
                                jQuery("#googleMaps_mapzoom").val(newLevel);
                        });
                        
			
			GEvent.addListener(streetView,"yawchanged", function() {
				GPov = streetView.getPOV();
				jQuery("#googleMaps_yaw").val( GPov.yaw );
				jQuery("#googleMaps_pitch").val( GPov.pitch );
				jQuery("#googleMaps_zoom").val( GPov.zoom );
			});
			
			GEvent.addListener(streetView,"pitchchanged", function() {
				GPov = streetView.getPOV();
				jQuery("#googleMaps_yaw").val( GPov.yaw );
				jQuery("#googleMaps_pitch").val( GPov.pitch );
				jQuery("#googleMaps_zoom").val( GPov.zoom );
			});
			
			GEvent.addListener(streetView,"zoomchanged", function() {
				GPov = streetView.getPOV();
				jQuery("#googleMaps_yaw").val( GPov.yaw );
				jQuery("#googleMaps_pitch").val( GPov.pitch );
				jQuery("#googleMaps_zoom").val( GPov.zoom );
			});
			
			GEvent.addListener(streetView,"initialized", function(GLocation) {
				GPov = streetView.getPOV();
				jQuery("#googleMaps_lat").val( GLocation.latlng.lat() );
				jQuery("#googleMaps_lng").val( GLocation.latlng.lng() );
				jQuery("#googleMaps_yaw").val( GPov.yaw );
				jQuery("#googleMaps_pitch").val( GPov.pitch );
				jQuery("#googleMaps_zoom").val( GPov.zoom );
			});
       
      }
    });
    
    function streetViewChangeLocation( data )
    {
    	$("#GStreetViewError").remove();
    	if (data.code != 200) 
    	{
        	if ( !$("#GStreetViewError")[0] ) 
        		$("#GStreetViewContainer").before('<div id="GStreetViewError">'+errorNoStreetView+'</div>');
        	jQuery("#googleMaps_lat").val( "" );
			jQuery("#googleMaps_lng").val( "" );
			jQuery("#googleMaps_yaw").val( "" );
			jQuery("#googleMaps_pitch").val( "" );
			jQuery("#googleMaps_zoom").val( "" );
        	return;
      	}
      	else
      	{
      		point = data.location.latlng;
			streetView.setLocationAndPOV(point);
			GPov = streetView.getPOV();
			jQuery("#googleMaps_lat").val( point.lat() );
			jQuery("#googleMaps_lng").val( point.lng() );
			jQuery("#googleMaps_yaw").val( GPov.yaw );
			jQuery("#googleMaps_pitch").val( GPov.pitch );
			jQuery("#googleMaps_zoom").val( GPov.zoom );
      	}
    }
    
    
    function handleNoFlash(errorCode) 
    {
      if (errorCode == FLASH_UNAVAILABLE) 
      {
        alert(errorNoFlash);
        return;
      }
    }
    </script>

    
    
</div><!-- #examples -->
</div><!-- #main -->

</script>
</body>
</html>

