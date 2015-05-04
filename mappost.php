<?php
 // BlogPost.php
 // Publica automáticamente al blog puntos de interés de Google Maps y Streetview.
 //
 //Genera automáticamente el post con el código HTML y javascript necesario
 //para mostrar el punto elegido, con su título y todo.
 //
 // **********************************************+
 //
 //Parámetros:
 //
 //   /* GoogleMaps */
 //
 //   googleMaps_lat
 //   googleMaps_lng
 //   googleMaps_mapzoom
 //   
 //   /* Streetview */
 //   
 //   googleMaps_yaw
 //   googleMaps_pitch
 //   googleMaps_zoom
 //   
 //   /* Blog Post Title */
 //   
 //   title

require_once('config.php');

$rpcserver = "http://" . $blog_url . "/xmlrpc.php";

// Tomamos parámetros del formulario

$gm_lat = $_POST["googleMaps_lat"];
$gm_long = $_POST["googleMaps_lng"];
$gm_zoom = $_POST["googleMaps_mapzoom"];
$gs_yaw = $_POST["googleMaps_yaw"];
$gs_pitch = $_POST["googleMaps_pitch"];
$gs_zoom = $_POST["googleMaps_zoom"];
$post_title = $_POST["title"];

srand ( time() + 31415926535 );
$post_id = time() + rand() % 999;
$post_body = "[streettag gm_lat='$gm_lat' gm_long='$gm_long' gm_zoom='$gm_zoom' gs_yaw='$gs_yaw' gs_pitch='$gs_pitch' gs_zoom='$gs_zoom']";

require_once('xmlrpc.inc');

$client = new xmlrpc_client( "/xmlrpc.php", $blog_url );

$f = new xmlrpcmsg("metaWeblog.newPost",
    array(
        new xmlrpcval( "blog", "string"), // BlogID (Ignored)
        new xmlrpcval( $blog_user, "string"), // User
        new xmlrpcval( $blog_password, "string"),    // Pass
        new xmlrpcval( // body
        array(
            "title" => new xmlrpcval( $post_title, "string"),
            "description" => new xmlrpcval ( $post_body, "string")
        ), "struct"),
        new xmlrpcval(true, "boolean") // publish
    )
);

$oResponse = $client->send($f);

for ($i = 0; $i < $f->getNumParams(); $i++) {
    $e = $f->getParam($i);
    echo $e->scalarval();
}

$xWebserviceOutput;
 
if ($oResponse->faultCode() ) {
    $xWebserviceOutput = $oResponse->faultString();
}
else
{
    $oValue = $oResponse->value();
    $xWebserviceOutput = $oValue->scalarval();
}

echo $xWebserviceOutput;

?>

<html>
    <head>
    <title><?php print $post_title; ?></title>
    <script src="jquery-1.3.1.js" type="text/javascript"></script>
    </head>
    <body>

?>

<?php

   print $post_body;
 
?>
        <h2><a href="javascript:history.go(-1)">Volver</a></h2>
        
    </body>
</html>
