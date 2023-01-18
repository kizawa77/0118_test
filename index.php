<?php
    // insert.php - <header()> → index.php
    // login.php - <a href トップページ> → index.php
    // select.php - <a href> → index.php

//1.準備
require_once('funcs.php');
$pdo = db_conn();

//2.SQLアクセス
$stmt = $pdo->prepare(
    'SELECT *
    FROM shops'
);
$status = $stmt->execute();

//3.データ表示
$json_data = array();
if($status == false){
    sql_error($status);
} else {
    while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
        $json_tmp = 
            array(
            'shop_id' => $result['shop_id'],
            'company_id' => $result['company_id'],
            'shop_name' => $result['shop_name'],
            'shop_name_jp' => $result['shop_name_jp'],
            'shop_br_name' => $result['shop_br_name'],
            'shop_br_name_jp' => $result['shop_br_name_jp'],
            'shop_post' => $result['shop_post'],
            'shop_address1' => $result['shop_address1'],
            'shop_address1_jp' => $result['shop_address1_jp'],
            'shop_address2' => $result['shop_address2'],
            'shop_address2_jp' => $result['shop_address2_jp'],
            'shop_latitude' => $result['shop_latitude'],
            'shop_longitude' => $result['shop_longitude'],
            'shop_phone' => $result['shop_phone'],
            'shop_fax' => $result['shop_fax'],
            'shop_mail' => $result['shop_mail'],
            'shop_hp' => $result['shop_hp']
        );
        array_push($json_data, $json_tmp);

    }
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>（仮）Coupon Distribution</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="./images/favicon.ico">
    <link rel="json" href=".js/index.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100&family=Yuji+Syuku&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><a class="navbar-brand" href="login.php">利用者情報</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="register.php">登録する</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="login.php">ログイン</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="logout.php">ログアウト</a></div>
            </div>
        </nav>
    </header>
    <main>
        <section>
            <div class="area1">
                <div class="myMap" id="myMap">
                    <!-- MapArea -->

                    <!-- <div id="view"></div> -->
                    <!-- <div id="myMap"></div> -->

                    <!-- /MapArea -->
                </div>
            </div>
        </section>
        <section>
            <div class="area2">
                <div class="area2 left">
                    他人所有クーポン表示
                </div>
                <div class="area2 right">
                    自己所有クーポン表示
                </div>
            </div>
            <?= $json_data[1]['shop_name'] ?>
        </section>
    </main>
    <footer>
        Coupon Distribution
    </footer>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- jQuery&GoogleMapsAPI -->
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src='https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=As7d2X_xt0yhfh7brRTN8VAUTXh3ErLo-5DCwLvhAihnMgPlPyWirYa0mT0m_JKK'
        async defer></script>
    <script src="js/BmapQuery.js"></script>

    
    <script>

        //****************************************
        //最初に実行する関数
        //****************************************
        function GetMap() {
            navigator.geolocation.getCurrentPosition(mapsInit, mapsError, set);
        }

        //****************************************
        //成功関数
        //****************************************
        let map;

        function mapsInit(position) {

            // 緯度経度情報を取得 lat=緯度 lon=経度
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            // Map表示
            $('#myMap').css('width', '100%');
            $('#myMap').css('height', '600px');

            map = new Bmap('#myMap');

            map.startMap(lat, lon, 'road', 25); //Mapの開始位置

            for (let i = 0; i < <?= count($json_data) ?>; i++) {
                    <?= $i ?> = i;
                    let latitude = <?= $json_data[$i]['shop_latitude'] ?>;
                    let longitude = <?= $json_data[$i]['shop_longitude'] ?>;
                    let shop_name = <?= $json_data[$i]['shop_name'] ?>;
                    let shop_br_name = <?= $json_data[$i]['shop_br_name'] ?>;

                    let pin = map.pinText(lat, lon, '現在地', ' ');
                    pin = map.pinText(latitude, longitude, shop_name, shop_br_name);

                    // map.onPin(pin, 'click', function () {

                    //     $('#explanation').php(journey1[i][j]['explanation']);
                    //     $('#picture').css('object-fit', 'contain');
                    //     $('#picture').php(`
                    //         <img src='${journey1[i][j]['picture']}'>
                    //         `);
                    //     console.log(journey1[i][j]['picture']);
                    // });
            }

            // map.infoboxHTML(lat, lon, '<div style='background:red;'>Hello,world</div>');

            map.onPin(pin, 'click', function () {

                //InfoBoxを表示
                map.infobox(lat, lon, 'タイトル', '説明文');
                $('#area2').php(
                    'ピンがクリックされました。'
                );
                console.log('clicked');
            });

            // map.onGeocode('click', function (data) {
            //     console.log(data);                   //Get Geocode ObjectData
            //     const lat = data.location.latitude;  //Get latitude
            //     const lon = data.location.longitude; //Get longitude
            //     document.querySelector('#geocode').innerHTML = lat + ',' + lon;

            //     map.infobox(lat, lon, 'タイトル', '今押した場所', '説明文');
            // });
        }

        //****************************************
        //失敗関数
        //****************************************
        function mapsError(error) {
            let e = '';
            if (error.code == 1) { //1＝位置情報取得が許可されてない（ブラウザの設定）
                e = '位置情報が許可されてません';
            }
            if (error.code == 2) { //2＝現在地を特定できない
                e = '現在位置を特定できません';
            }
            if (error.code == 3) { //3＝位置情報を取得する前にタイムアウトになった場合
                e = '位置情報を取得する前にタイムアウトになりました';
            }
            alert('エラー：' + e);
        };

        //****************************************
        //オプション設定
        //****************************************
        const set = {
            enableHighAccuracy: true, //より高精度な位置を求める
            maximumAge: 20000,        //最後の現在地情報取得が20秒以内であればその情報を再利用する設定
            timeout: 10000            //10秒以内に現在地情報を取得できなければ、処理を終了
        };

    </script>
</body>

</html>