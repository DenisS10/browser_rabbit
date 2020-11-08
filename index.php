<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Browser RabbitMQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- CSS -->
    <link href="assets/libs/reset/reset.css" rel="stylesheet">
    <link href="assets/css/main.css?v=<?=filemtime(__DIR__ . '/assets/css/main.css'); ?>" rel="stylesheet">
    <link href="assets/css/media.css" rel="stylesheet">
    <link href="assets/libs/lib/leaflet/leaflet.css" rel="stylesheet">
        
    <link rel="icon" href="/favicon.png" type="image/x-icon">

</head>


<body>

<script src="assets/libs/jquery/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="assets/libs/lib/leaflet/leaflet.js" type="text/javascript"></script>
<script src="assets/libs/lib/leaflet-plugins-3.0.3/layer/Layer.Deferred.js"></script>
<script src="assets/libs/lib/leaflet-plugins-3.0.3/layer/vector/KML.js"></script>
<script src="assets/libs/lib/tileLayers/WebGis.js" type="text/javascript"></script>
<script src="assets/libs/lib/tileLayers/VirtualEarth.js" type="text/javascript"></script>
<script src="assets/libs/lib/tileLayers/WikiMapia.js" type="text/javascript"></script>
<script src="assets/libs/lib/Leaflet.markercluster-1.4.1/dist/leaflet.markercluster.js" type="text/javascript"></script>
<link rel="stylesheet" href="assets/libs/lib/leaflet-polyline-measure/Leaflet.PolylineMeasure.css"/>
<script src="assets/libs/lib/leaflet-polyline-measure/Leaflet.PolylineMeasure.js"></script>
<script type="text/javascript" src="node_modules/webstomp-client/dist/webstomp.min.js"></script>
<script src="assets/libs/app.js?v=<?=filemtime(__DIR__ . '/assets/libs/app.js'); ?>" type="text/javascript"></script>
<script src="assets/libs/map/map.js?v=<?=filemtime(__DIR__ . '/assets/libs/map/map.js'); ?>" type="text/javascript"></script>

<section id="global_wrap">
    <section id="gps_section" class="global_section">
        <div class="header_box">
            <h2>Position by GPS</h2>
        </div>
        <div class="body_box">
            <div id="map_gps"></div>
        </div>
    </section>

    <section id="info_section" class="global_section">
        <div class="header_box">
            <h2>Message information</h2>
        </div>
        <div style="display: grid" class="body_box msg-info-body">
            <div class="rabbit-msg-box">

            </div>
            <div class="human-readable-box">
                <table id="additional_info">
                    <tbody>
                    <tr class="datetime">
                        <th>Date &amp; Time:</th>
                        <td></td>
                    </tr>
                    <tr class="sats">
                        <th>Sattelite:</th>
                        <td></td>
                    </tr>
                    <tr class="course">
                        <th>Course:</th>
                        <td></td>
                    </tr>
                    <tr class="imei">
                        <th>Imei:</th>
                        <td></td>
                    </tr>
                    <tr class="coords">
                        <th>Coordinates:</th>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</section>


</body>


</html>





