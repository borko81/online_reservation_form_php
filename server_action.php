<?php
session_start();
?>

<?php include 'const.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CheckForFree</title>
</head>
<body>
    <?php
        //var_dump($_POST);

        $income = trim(isset($_POST['income']) ? htmlspecialchars($_POST['income']) : '');
        $outcome = trim(isset($_POST['outcome']) ? htmlspecialchars($_POST['outcome']) : '');
        $people = isset($_POST['people']) ? explode(":", trim($_POST['people'])) : '';
        $people_count='';
        $SITE_URL = $NEEDED_URL;


        if(!empty($income) && !empty($outcome) && !empty($people)) {
            $SITE_URL .= 'Arrival=' . $income;
            $SITE_URL .= '&Departure=' . $outcome;
            
           
            if (sizeof($people) == 1) {
                $SITE_URL .= '&Adults=' . $people[0];
            } else {
                $SITE_URL .= '&Adults=' . $people[0];
                for($i=1; $i<sizeof($people); $i++) {
                    $SITE_URL .= '&ChildrenAge=' . $people[$i];
                }
            }

            $SITE_URL .= "&token=$TOKEN";

            // echo $SITE_URL;
            
            // Send data to API
            $cURLConnection = curl_init();
                curl_setopt($cURLConnection, CURLOPT_URL, $SITE_URL);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($cURLConnection);
                curl_close($cURLConnection);
                $result_php = json_decode($result, true);
                
                for ($i=0; $i<sizeof($result_php['roomPrices']); $i++) {
                    $price = round($result_php['roomPrices'][$i]["price"], 2);
                    $room_tipe = $result_php['roomPrices'][$i]["roomType"]["name"];
                    $room_id =  $result_php['roomPrices'][$i]["roomType"]["id"];

                    echo "<div class='grid-item'>";
                    echo "<h3 class='card-title'>$room_tipe</h3> ";
                    echo "<h5 class='card-text'>Сума  $price лв. избор <input type='radio' id='huey' name='room_choice' value='{$room_id}:{$price}:{$room_tipe}' required></h5>";
                    echo "</div>";
                }
        }
    ?>
</body>
</html>