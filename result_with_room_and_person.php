<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_for_reserv.css">  
    <title>Document</title>
</head>
<body>
<?php include 'const.php';?>    

<div class="potv_container">

    <?php

    // var_dump($_POST['room_hidden_id']);

    function httpPost($url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    if (
        isset($_POST['room_hidden_id']) &&
        isset($_POST['potv_name']) &&
        isset($_POST['potv_email']) &&
        isset($_POST['potv_tel'])
    ) {
        $URL_FOR_ADD_RESERV = $URL_TO_POST_RESERV_OUTSIDE;
        if (empty($_POST['room_hidden_id'])) {
            echo "Изберете тип помещение";
            return;
        }
        $splitted_hidden_date = explode(":", htmlspecialchars(trim($_POST['room_hidden_id'])));
        $children = array_slice($splitted_hidden_date, 6);
        $child_count = [];
        if(sizeof($children) > 0) {
            for ($i=0; $i<sizeof($children);$i++) {
                array_push($child_count, $children[$i]);
            }
        }

        $data = array(
            "arrival"=> $splitted_hidden_date[3],
            "departure"=> $splitted_hidden_date[4],
            "guestName"=> $_POST['potv_name'],
            "phoneNumber"=> $_POST['potv_tel'],
            "emailAddress"=> $_POST['potv_email'],
            "note"=> substr(preg_replace("/\"/","'",htmlspecialchars(trim($_POST['potv_comment']))), 0, 150),
            "paymentType"=> 0,
            "referenceNumber"=> "string",
            "rooms"=> [
            
                array(
                    "roomTypeId"=> $splitted_hidden_date[0],
                    "adults"=> $splitted_hidden_date[5],
                    "boardTypeId"=> null,
                    "childrenAge"=> $child_count
                )
            ]
            );

        // var_dump($data);
        // die();

        $json_data = json_encode($data);

        $response_data = httpPost($URL_FOR_ADD_RESERV, $json_data);
        $php_readable_data = json_decode($response_data, true);
        
        var_dump($php_readable_data);

        if ($php_readable_data['bookingId']) {
            echo "Успешно направена резерация с номер : " . $php_readable_data['bookingId'] . " очакваите потвърждение от наш служител <bg />";
        } else {
            echo "Възникна грешка, моля опитаите пак или се обадете на рецепция.<br />";
            echo $php_readable_data['note'][0];
        }
    }

    ?>

</div>

<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
//   window.location.href="ask_reserve.php";
}
</script>
</body>
</html>