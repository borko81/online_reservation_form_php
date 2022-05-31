<?php
session_start();
$_SESSION["reload_page"] = null;
session_destroy();
?>


<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="style_for_reserv.css">  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Резервационна система</title>
</head>

<body>

    <div class="res_header">
        <p>LOGO</p>
        <a href="" class="politics" data-toggle="modal" data-target="#myModal">Общи условия</a>
    </div>

    <div class="wrapper">
        <!-- Форма за запитване, начална краина дата брои гости -->
        <div class="box_one a">
            <form action="" name="ask_for_free_rooms" method="POST">
                <div class="form-group div_with_margin">
                    <label for="income_date" class="free_label">Пристигане</label><br />
                    <input type="date" id="income_date" class="form-control" name="income_date" required="true">
                </div>
                <div class="form-group div_with_margin">
                    <label for="outcome_date" class="free_label">Отпътуване</label>
                    <input type="date" class="form-control" id="outcome_date" name="outcome_date" required="true">
                </div>
                <div class="form-group div_with_margin">
                    <label for="people_count" class="free_label">Брои гости</label>
                    <select class="form-control" id="people_count" name="people_count" required="true">
                        <option value="2">2 възрасти</option>
                        <option value="3">3 възрасти</option>
                        <option value="4">4 възрасти</option>
                        <option value="2:6">2 възрасти + 1 дете до 6г.</option>
                        <option value="2:12">2 възрасти + 1 дете до 12г.</option>
                        <option value="2:12:12">2 възрасти + 2 деца до 12 г</option>
                        <option value="2:12:6">2 възрасти + 2 деца: 1 до 6, 1 до 12 г</option>
                      </select>
                </div>
                <div id="form-group div_with_margin">
                    <button class="btn btn-primary form-control my_but" name="submit1" id="ask_for_rooms" type="submit">Проверка за свободни помещение</button>
                </div>
            </form>
        </div>

        <div class="box_one b">
            <form action="result_with_room_and_person.php" method="POST">
                <div class="form-group div_with_margin">
                    <input type="text" id="potv_name" class="form-control" name="potv_name" placeholder="Въведете име" required="true">
                </div>
                <div class="form-group div_with_margin">
                    <input type="tel" id="potv_tel" class="form-control" name="potv_tel" placeholder="Въведете телефон" required="true">
                </div>
                <div class="form-group div_with_margin">
                    <input type="emai" id="potv_email" class="form-control" name="potv_email" placeholder="Въведете email" required="true">
                </div>
                
                <div class="form-group div_with_margin">
                    <textarea name="potv_comment" id="potv_comment" class="note_class" placeholder="Коментар ако има такъв, ще бъде изпълнен по възможност" style="resize:none"></textarea>
                </div>
                <?php
                    echo "<div>";
                    echo "<input type='hidden' class='human_choice_hidden form-control' name='room_hidden_id'>";
                    echo "</div>";
                ?>
                  <div class="form-group div_with_margin">
                    <input type="submit" name="SubmitForTakeReserv" value="Направи резерация" class="form-control btn btn-primary my_but" id="button_take_down">
                </div>
            </form>
        </div>

        <!-- Show free room's -->
        <?php

            echo "<div class='box c'>";
                echo "<div id='loadingDiv'>Зарежда се резултат</div>";
                echo "<div class='mygrid-container' id='msg'></div>";
            echo "</div>";

        ?>

      </div>

      <!-- Modalen prozorec s obshtite uslowiq -->
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Общи условия UnrealSoft LTD</h4>
            </div>
            <div class="modal-body">
              <p>Полето "Забележки" в бланката за резервация няма задължаващ характер за , като всички желания на клиента, попълнени във въпросното поле, ще бъдат удовлетворени при възможност.</p>
              <p>Цените във формата са в лева (BGN), с включен 9% ДДС, застраховка и туристически данък в размер 2,02лв на човек на възрастен. Общата дължима от клиента сума по резервацията се калкулира автоматично, в зависимост от броя дни, броя възрастни и деца, и от актуалната ценова оферта за съответния период.</p>
              <strong>ЛИЧНИ ДАННИ</strong>

              <p>Вашите лични данни са защитени съгласно законодателството на Република България и се използват единствено от резервационния отдел на хотела.
              Вашите лични данни няма да бъдат предоставяни на трети лица.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
          
        </div>
      </div>

    <script type="text/javascript">

        let income_input = document.getElementById("income_date");
        let outcome_input = document.getElementById("outcome_date");
        
        income_input.addEventListener("click", function () {
            income_input.showPicker()
        });

        outcome_input.addEventListener("click", function () {
            outcome_input.showPicker()
        });

        var $loading = $('#loadingDiv').hide();
        $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });

        $( "#ask_for_rooms" ).on( "click", function() {
            let income = $("#income_date").val();
            let outcome = $("#outcome_date").val();
            let people = $("#people_count").val();
            
            if (income && outcome && people) {
                $.ajax({

                    type:"post",
                    url:"server_action.php",
                    data: 
                    {  
                    'income' :income,
                    'outcome': outcome,
                    'people': people
                    },
                    cache:false,
                    success: function (html) 
                    {
                    $('#msg').html(html);

                        $('input[type=radio]').click( function(e) {
                            let human_choice = $(this).val();
                            let information_schema = '';
                              information_schema += human_choice  + ':';
                              information_schema += income + ':';
                              information_schema += outcome + ':';
                              information_schema += people;
                            $('.human_choice_hidden').val(information_schema);
                        });

                    }
                });
                return false;

            }
        });
    </script>
</body>
</html>