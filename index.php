
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>SMS INFORMER 2.0</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- CSS -->
    <link href="assets/libs/reset/reset.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/media.css" rel="stylesheet">
</head>




<body>

<?php
define("BATTERY", 1);   //
define("GPS_LATITUDE", 3);   //
define("GPS_LONGITUDE", 4);  //
define("GPS_SSHC", 5);       //  speed (high byte),  satellites, height, course (least significant byte)
define("GSM_LACCID", 7);     // "LAC CID"	"Local Area Code -  (first 2 B) 0xFF01
define("GSM_OPER", 8);       // "Signal level GSM (old. Byte)
define("DOP_PARAM", 152);    // "152, время младшее(сек), время старшее(сек),температура(знаковое, Градусы, от -128 до 127), HDOP(в 0.1)
$dif_time=3*60*60;
// test.duotec.ru/?013C00C361AA5A03848B5E420455D51442050015670097EE000000075FD69B130802FA00190900D0C464010F11E13A5B8D013F015C0000BC015D0000F603FA320100006D
// 013C00FD5AAA5A03958B5E42048FD51442050015770097DB000000075FD69B130802FA00180900D0C564010F11EA3A5B070009015C00009C005D00008C00FA32010000A6
//echo getenv("REMOTE_ADDR");     printf ("<br>\r\n");
//echo getenv("REQUEST_METHOD");  printf ("<br>\r\n");
//echo getenv("REMOTE_HOST");     printf ("<br>\r\n");
//echo getenv("HTTP_REFERER");    printf ("<br>\r\n");
//echo getenv("SCRIPT_NAME");     printf ("<br>\r\n");
//echo getenv("HTTP_USER_AGENT"); printf ("<br>\r\n");
//echo getenv("QUERY_STRING");    printf ("<br>\r\n");

$sms_req=array_keys($_GET)[0]; // ("QUERY_STRING");
//printf ("  LEN [%02d] Bytes <br>\r\n",  (strlen($sms_req)/2));
$hex_mas = [];
$len_packet=0;
for($i=0; $i<(strlen($sms_req)); $i++)
{
    $hex_mas[]=hexdec($sms_req[$i].$sms_req[$i+1]);
    // printf(" [%02d] = %02X <br>",  $len_packet, $hex_mas[$len_packet]);
    $len_packet++;
    $i++;
}

$crc_packet=$hex_mas[$len_packet-1];
for($i=3; $i<($len_packet-2); $i++) {$crc_calc+=$hex_mas[$i];}  $crc_calc=(($crc_calc>> 0) & 0xFF);
//printf(" CRC %d[%02X = %02X] <br>", $len_packet, $crc_calc, $crc_packet);

$flag_packet=$hex_mas[0];
if ( ($flag_packet==0x01) && ((($crc_packet >> 0) & 0xFF)==$hex_mas[y-1]) ) {
    $len_packet = $hex_mas[1] << $hex_mas[2];
}

$time_packet=(($hex_mas[6]<<24)|($hex_mas[5]<<16)|($hex_mas[4]<<8)|$hex_mas[3]);


$m_t_f=array();
$m_t_v=array();
$t_n=0;
/*
 echo("<tr>");
     printf("<td> &nbsp;Num&nbsp; </td>");
     printf("<td> &nbsp;Tag&nbsp; </td>");
     printf("<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Value<br></td>");
     echo("</tr>");*/
for($i=0; $i<($len_packet-2); $i++)
{
    //$m_t_n[$t_n]=$hex_mas[$i+7];
    $m_t_v[$hex_mas[$i+7]]=(($hex_mas[11+$i]<<24)|($hex_mas[10+$i]<<16)|($hex_mas[9+$i]<<8)|$hex_mas[8+$i]);
    $m_t_f[$hex_mas[$i+7]]=1;
    /*echo("<tr>");
       printf("<td> &nbsp;&nbsp;%02d&nbsp; </td>", ($t_n+1));
       printf("<td> &nbsp;&nbsp;&nbsp;%03d&nbsp; </td>", $hex_mas[$i+7]);
       printf("<td>&nbsp;&nbsp;&nbsp;%08X<br></td>", $m_t_v[$hex_mas[$i+7]] );
       echo("</tr>");*/
    $t_n++;
    $i+=4;
}


function unixtotime($time)
{
    $m_to_d2=array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365); //????????????
    $m_to_d1=array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 336); //??????????

    $count_days=0; $unix=0; $hms=0;
    $unix=$time;
    $count_days=(int)($unix/86400);
    $hms=$unix-$count_days*86400;
    if ($hms) $count_days++;
    if ($hms>=3600)	   $h=(int)($hms/3600);
    if ($hms-$h*3600>=60)    $m=(int)(($hms-$h*3600)/60);
    $s=$hms-$h*3600-$m*60;
    for ($i=70; $i<138; $i++)
    {
        if ( $count_days < (($i%4)?365:366) )
        {
            if ($i>100) $yy=$i-100; break;
        }
        $count_days -= (($i%4)?365:366);
    }

    for($g=1; $g<13; $g++)
    {
        if ( ($count_days) <=(($yy%4)?$m_to_d2[$g]:$m_to_d1[$g]) )
        {
            $mm=$g;
            $dd=$count_days-(($yy%4)?$m_to_d2[$g-1]:$m_to_d1[$g-1]);
            break;
        }
    }
    return array ($dd,$mm,$yy, $h, $m, $s);
    //printf ("%02d-%02d-%02d %02d:%02d:%02d", $dd,$mm,$yy, $h, $m, $s);
}




///} //for test








function get_val_tag($nom, $m_v) // get value by number of tag
{

    if ($m_v[$nom]!=0) return $m_v[$nom];
    /*for ($i=0; $i<12; $i++)
        {
        if ($m_n[$i]==$nom)
           {
           //echo "Tag[" .$nom."] = [".$m_v[$i]."]<BR>\n";
           return $m_v[$i];
           }
        }*/
    //echo "not found Tag number [".$nom."]<BR>\n";
    return -1;
}

function hextofloat($hex_val)
{
    $bin = str_pad(base_convert(sprintf("%02X", $hex_val), 16, 2), 32, "0", STR_PAD_LEFT);
    $sign = $bin[0];
    $exp = bindec(substr($bin, 1, 8)) - 127;
    $man = (2 << 22) + bindec(substr($bin, 9, 23));
    return ($man * pow(2, $exp - 23) * ($sign ? -1 : 1));
}


function do_post_request($url, $data, $optional_headers = null)
{
    $params = array('http' => array('method' => 'POST',  'content' => $data));
    if ($optional_headers !== null)
    {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'r', false, $ctx);

    if (!$fp)
    {
        //throw new Exception("Problem with  $php_errormsg");
        //printf ("Problem with  $url, $php_errormsg");
        $response=0;
    }
    $response = @stream_get_contents($fp);
    if ($response == false)
    {
        // throw new Exception("Problem reading data from $url, $php_errormsg");
        printf ("Problem reading data from $url, $php_errormsg");
        $response=0;
    }
    return trim($response);
    //return 1;
}

$gsmOperator = get_val_tag(GSM_OPER, $m_t_v);

$LATI=hextofloat(get_val_tag(GPS_LATITUDE, $m_t_v));
$LONG=hextofloat(get_val_tag(GPS_LONGITUDE, $m_t_v));
$SSHC=get_val_tag(GPS_SSHC, $m_t_v);
$GLAC=(get_val_tag(GSM_LACCID, $m_t_v)>>16)&0xFFFF;
$GCID=(((get_val_tag(GSM_LACCID, $m_t_v)<<16)>>16)&0xFFFF);
$GSML=(get_val_tag(GSM_OPER, $m_t_v)>>24);
$GMNC=((get_val_tag(GSM_OPER, $m_t_v))) & 0xFF;
$GMCC=((get_val_tag(GSM_OPER, $m_t_v)>>8)) & 0xFFFF;
$DP_HDOP=(get_val_tag(DOP_PARAM, $m_t_v)>>24);
$DP_TEMP=((get_val_tag(DOP_PARAM, $m_t_v)<<8)>>24);
$DP_TIME=(get_val_tag(DOP_PARAM, $m_t_v)&0xFFFF);
$BATTERY=(get_val_tag(BATTERY, $m_t_v)&0xFFFF);
$INTVOLT=(get_val_tag(BATTERY, $m_t_v)>>16)&0xFFFF;


//echo "GPS_DATE_TIME = [".$time_packet."]   "; unixtotime($time_packet);  echo "<BR>";
//echo "GPS_LATITUDE = [".$LATI."]<BR>\n";
//echo "GPS_LONGITUDE = [".$LONG."]<BR>\n";
//echo "GPS_SPEED = [".($SSHC>>24)."]<BR>\n";
//echo "GPS_SATTELITE = [".(($SSHC<<8)>>24)."]<BR>\n";
//echo "GPS_HEIGHT = [".(10*(($SSHC<<16)>>24))."]<BR>\n";
//echo "GPS_COURSE = [".(2*(($SSHC<<24)>>24))."]<BR>\n";

//echo "GSM_LAC = [".$GLAC."]<BR>\n";
//echo "GSM_CID = [".$GCID."]<BR>\n";
//echo "GSM_LEV = [".$GSML."]<BR>\n";
//echo "GSM_MNC = [".$GMNC."]<BR>\n";
//echo "GSM_MCC = [".$GMCC."]<BR>\n";
//echo "DP_HDOP = [".$DP_HDOP."]<BR>\n";
//echo "DP_TEMP = [".$DP_TEMP."]<BR>\n";
//echo "DP_TIME = [".$DP_TIME."]<BR>\n";
//echo "INT_TEMP = [".(($ITEMP/100)-500)."]<BR>\n";



/*
echo("<div align=\"center\"> GPS position <br>
    <img src=\"http://static-maps.yandex.ru/1.x/?l=map&pt=".$LONG.",".$LATI.",flag&z=12&l=map&size=650,450&scale=1.5\" alt=\"VIP\" border=\"1\"> <br>
   <a href=\"http://static-maps.yandex.ru/1.x/?l=map&pt=".$LONG.",".$LATI.",flag&z=17&l=map&size=650,450\"> +ZOOM+ </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   <a href=\"http://static-maps.yandex.ru/1.x/?l=map&pt=".$LONG.",".$LATI.",flag&z=7&l=map&size=650,450\">-ZOOM-</a><br><br>
    </div>");
*/



$host = "mobile.maps.yandex.net";
$port = "80";
$RequestURL = "mobile.maps.yandex.net/cellid_location/";
$data = "?&cellid=$GCID&operatorid=$GMNC&countrycode=$GMCC&lac=$GLAC";
if ($GLAC && $GCID && $GMNC && $GMCC )
{
//    echo "http://$RequestURL$data";
    $res1 = do_post_request("http://$RequestURL$data", ' ');
    //define("RES", $res1);

    //printf ("RES =%s", $res1);
    $lati="0";  $long="0";
    if ($lat=strstr($res1, "latitude=\"") )  for ($t=10; $t<19; $t++) {$lati[$t-10]=$lat[$t];}
    if ($lon=strstr($res1, "longitude=\""))  for ($t=11; $t<20; $t++) {$long[$t-11]=$lon[$t];}
    define("LONG1", $long);
    define("LATI1", $lati);

    $nlati="0";  $nlong="0";
    if ($nlat=strstr($res1, "nlatitude=\"") )  for ($t=11; $t<20; $t++) {$nlati[$t-11]=$nlat[$t];}
    if ($nlon=strstr($res1, "nlongitude=\""))  for ($t=12; $t<21; $t++) {$nlong[$t-12]=$nlon[$t];}

    define("NLONG", $nlong);
    define("NLATI", $nlati);

}


?>
<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<?php if (($m_t_f[GPS_LATITUDE]==1)&&($m_t_f[GPS_LONGITUDE]==1)): ?>

    <script type="text/javascript">
        ymaps.ready(init);

        function init()
        {
            var myMap_gps;
            myMap_gps = new ymaps.Map("map_gps",
                {
                    center: [<?= $LATI ?>, <?= $LONG ?>],
                    zoom: 16,
                    controls: []
                });

            myMap_gps.controls.add("zoomControl",
                {
                    position: {top: 15, left: 15}
                });

            var myPlacemark_gps = new ymaps.Placemark([<?= $LATI ?>, <?= $LONG ?>]);

            myMap_gps.geoObjects.add(myPlacemark_gps);


        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    ymaps.ready(init);
    function init()
    {
        var myMap;
        myMap = new ymaps.Map("map", {
            center: [<?= LATI1 ?>, <?= LONG1 ?>],
            zoom: 14,
            controls: []
        });

        myMap.controls.add("zoomControl", {
            position: {top: 15, left: 15}
        });

        //var myPlacemark = new ymaps.Placemark([".LATI1.",".LONG1."] , {},{});
        var myPlacemark = new ymaps.Circle([[<?= LATI1 ?>, <?= LONG1 ?>], 15000/<?= $GSML ?>]);
        myMap.geoObjects.add(myPlacemark);
        //var myPlacemark2 = new ymaps.Circle([[".NLATI.",".NLONG."], 5000/$GSML]);
        //myMap.geoObjects.add(myPlacemark2);
    }

</script>

<?php
//}


/*
REMOTE_ADDR Ц IP-адрес хоста (компьютера), отправл¤ющего запрос;

REMOTE_HOST Ц им¤ хоста, с которого отправлен запрос;

HTTP_REFERER Ц адрес страницы, ссылающейс¤ на текущий скрипт;

REQUEST_METHOD Ц метод, который был использован при отправке запроса;

QUERY_STRING Ц информаци¤, наход¤ща¤с¤ в URL после знака вопроса;

SCRIPT_NAME Ц виртуальный путь к программе, котора¤ должна выполн¤тьс¤;

HTTP_USER_AGENT Ц информаци¤ о браузере, который использует клиент
*/


//echo getenv("REMOTE_ADDR");     printf ("<br>\r\n");
//echo getenv("REQUEST_METHOD");  printf ("<br>\r\n");
//echo getenv("REMOTE_HOST");     printf ("<br>\r\n");
//echo getenv("HTTP_REFERER");    printf ("<br>\r\n");
//echo getenv("SCRIPT_NAME");     printf ("<br>\r\n");
//echo getenv("HTTP_USER_AGENT"); printf ("<br>\r\n");
//echo getenv("QUERY_STRING");    printf ("<br>\r\n");



?>

<section id="global_wrap">
    <section id="gps_section" class="global_section">
        <div class="header_box">
            <h2>Position by GPS</h2>
        </div>
        <div class="body_box">
            <div id="map_gps"></div>
        </div>
    </section>

    <section id="gsm_section" class="global_section">
        <div class="header_box">
            <h2>Position by GSM</h2>
        </div>
        <div class="body_box">
            <div id="map"></div>
        </div>
    </section>

    <section id="info_section" class="global_section">
        <div class="header_box">
            <h2>Device information</h2>
        </div>
        <div class="body_box">
            <table>
                <tbody>

                <?php
                echo("<tr>");
                printf("<th>Date & Time:</th>");

                list  ($dd,$mm,$yy, $h, $m, $s) = unixtotime($time_packet+$dif_time);
                printf("<td>%02d-%02d-%02d at %02d:%02d:%02d</td>",  $dd, $mm, $yy, $h, $m, $s);
                echo("</tr>");
                if ($m_t_f[GPS_SSHC]==1)
                {
                    echo("<tr>");
                    printf("<th>Speed:</th>");
                    printf("<td>%d km/h</td>", ($SSHC>>24)*1,6);
                    echo("</tr>");

                    echo("<tr>");
                    printf("<th>Height:</th>");
                    printf("<td>%d m</td>", (10*((($SSHC<<16)>>24) & 0xFF)));
                    echo("</tr>");

                    echo("<tr>");
                    printf("<th>Sattelite:</th>");
                    printf("<td>%d</td>", (($SSHC<<8)>>24));
                    echo("</tr>");
                }



                echo("<tr>");
                printf("<th>GSM level:</th>");
                printf("<td>%d [%d %%]</td>", $GSML, 100*($GSML-6)/25);
                echo("</tr>");

                echo("<tr>");
                printf("<th>GSM operator:</th>");
                switch($GMNC)
                {
                    case 1: $operator_name="MTS RUS"; break;
                    case 2: $operator_name="MegaFon RUS"; break;
                    case 99: $operator_name="BeeLine RUS"; break;
                    default: $operator_name="Unknown"; break;
                }
                //$operator_name=$GMNC==1?"MTS RUS":$GMNC==2?"MegaFon RUS"$GMNC==99?"BeeLine Rus":"Unknown";
                printf("<td>%s</td>",  $operator_name);
                echo("</tr>");

                echo("<tr>");
                printf("<th>Internal power:</th>");
                printf("<td>%d mV</td>", $BATTERY);
                echo("</tr>");
                echo("<tr>");
                printf("<th>External power:</th>");
                printf("<td>%d mV</td>", $INTVOLT);
                echo("</tr>");


                if ($m_t_f[DOP_PARAM]==1)
                {
                    if ($m_t_f[GPS_LATITUDE]==1)
                    {
                        echo("<tr>");
                        printf("<th>GPS HDOP:</th>");
                        printf("<td>%.1f</td>", $DP_HDOP/10);
                        echo("</tr>");
                    }
                    echo("<tr>");
                    printf("<th>Temperature:</th>");
                    printf("<td>%d C</td>",  $DP_TEMP);
                    echo("</tr>");

                    echo("<tr>");
                    printf("<th>Session time:</th>");
                    printf("<td>%d sec</td>",  $DP_TIME);
                    echo("</tr>");
                }

                ?>


                </tbody>
            </table>
        </div>
    </section>
</section>



</body>


</html>





