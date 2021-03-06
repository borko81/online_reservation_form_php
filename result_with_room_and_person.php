<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_for_reserv.css"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 
    <title>Generate reserve</title>
    <style>
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        
        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        
        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include 'const.php';?>    

<div class="potv_container">

    <?php

    // var_dump($_POST['room_hidden_id']);

    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
     }

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

    // Check if user refresh page and try to isert data again, variable borko i fake may change it
    if (isset($_SESSION['reload_page']) && ($_SESSION["reload_page"] == "borko")) {
        header('Location: ask_reserve.php');
    }

    if (
        (!isset($_SESSION["reload_page"])) &&
        isset($_POST['room_hidden_id']) &&
        isset($_POST['potv_name']) &&
        isset($_POST['potv_email']) &&
        validateEmail($_POST['potv_email']) &&
        isset($_POST['potv_tel'])
    ) {
        $URL_FOR_ADD_RESERV = $URL_TO_POST_RESERV_OUTSIDE;
        if (empty($_POST['room_hidden_id'])) {
            echo "<button class='btn btn-primary form-control go_back_button' id='go_back_button' onclick='go_back()';>?????????????? ???? ???? ???? ???????????????? ?????? ??????????????????</button>";
        } else {
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
            
            //var_dump($php_readable_data);

            if (isset($php_readable_data['bookingId']) && (!empty($php_readable_data['bookingId']))) {
                $RES_ID = $php_readable_data['bookingId'];
                $_SESSION["reload_page"] = "borko";
                echo "<div id='myModal' class='modal'>";

                echo    "<div class='modal-content'>";
                echo        "<span class='close'>&times;</span>";
                echo        "<p>?????????????? ?????????????????? ?????????????????? ?? ?????????? :  $RES_ID ?????????????????? ???????????????????????? ???? ?????? ????????????????</p>";
                echo     "</div>";

                echo "</div>";
                echo "<script>let modal =document.getElementById('myModal');modal.style.display = 'block';</script>";

            } else {
                echo "???????????????? ????????????, ???????? ???????????????? ?????? ?????? ???? ?????????????? ???? ????????????????.<br />";
                echo $php_readable_data['note'][0] ? $php_readable_data['note'][0] : '';
            }
        }
    } else {
        echo "???????????????? ????????????, ???????? ???????????????? ?????? ?????? ???? ?????????????? ???? ????????????????.<br />";
        echo "<button class='btn btn-primary form-control go_back_button' id='go_back_button'>?????????????? ???? ?????????????? ?? ?????????????????? ??????????????.</button>";
    }

    ?>

</div>

<script>

var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
  window.location.assign("ask_reserve.php");
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    window.location.assign("ask_reserve.php");
  }
}

function go_back() {
    window.history.back();
}

</script>
</body>
</html>