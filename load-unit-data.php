<?php
try {
    $conn_string = "host=127.0.0.1 dbname=testdb port=25432 user=user1 password=user connect_timeout=60";
    $dbconn = pg_connect($conn_string);

    $select = pg_query($dbconn, "SELECT * FROM last_msg_unit WHERE unit_id = '6209579435'");
    $data = pg_fetch_all($select);


//            $seltest = Yii::$app->db->createCommand('SELECT * FROM YII_HEALTH')->queryAll();
    pg_close($dbconn);
} catch (Exception $exception) {

}
header('Content-Type: application/json');
echo json_encode($data[0]);
