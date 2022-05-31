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

    if ($_SESSION["reload_page"] == "borko") {
        header('Location: ask_reserve.php');
    }

    if (
        ($_SESSION["reload_page"] == null) &&
        isset($_POST['room_hidden_id']) &&
        isset($_POST['potv_name']) &&
        isset($_POST['potv_email']) &&
        validateEmail($_POST['potv_email']) &&
        isset($_POST['potv_tel'])
    ) {
        $URL_FOR_ADD_RESERV = $URL_TO_POST_RESERV_OUTSIDE;
        if (empty($_POST['room_hidden_id'])) {
            echo "<button class='btn btn-primary form-control go_back_button' id='go_back_button'>Върнете се за да изберете тип помещение</button>";
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
                echo        "<p>Успешно направена резерация с номер :  $RES_ID очакваите потвърждение от наш служител</p>";
                echo     "</div>";

                echo "</div>";
                echo "<script>let modal =document.getElementById('myModal');modal.style.display = 'block';</script>";

            } else {
                echo "Възникна грешка, моля опитаите пак или се обадете на рецепция.<br />";
                echo $php_readable_data['note'][0] ? $php_readable_data['note'][0] : '';
            }
        }
    } else {
        echo "Възникна грешка, моля опитаите пак или се обадете на рецепция.<br />";
        echo "<button class='btn btn-primary form-control go_back_button' id='go_back_button'>Върнете се обратно и преверете данните.</button>";
    }

    ?>

</div>

<script>

// Get the button that opens the modal


// Get the <span> element that closes the modal
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


let go_back = document.getElementById("go_back_button");
go_back.addEventListener("click", function () {
    window.history.back();
});

if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
}

</script>
</body>
</html>