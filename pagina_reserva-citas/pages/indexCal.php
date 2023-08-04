<?php 
include("conexionBaseDatos.php");

        function build_calendar ($month, $year){
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM cita where MONTH(fecha) = :month AND YEAR(fecha)= :year");
            $stmt->bindParam(":month", $month);
            $stmt->bindParam(":year", $year);
            $bookings = array();

            
            
            //chatGPT
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $bookings[] = $row['fecha'];
                }
                $stmt->closeCursor();
            }
            //chatGPT end
            
            // creating an array con los dias de la semana
            $daysOfWeek = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');
                            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            // el primer dia del mes mktime(hour,min,sec,month,day,year)
            $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
            // el numero de dias en el mes 
            $numberDays = date('t',$firstDayOfMonth);
            //pedimos info sobre el primer dia del mes
            $dateComponents = getdate($firstDayOfMonth);
                    
                            //el nombre del mes
                            $monthName =  $meses[$dateComponents['mon']-1];
            // get el index del primer dia del mes desde array de los dias de la semana 
            $dayOfWeek = $dateComponents['wday'];
                                    if($dayOfWeek == 0){
                                        $dayOfWeek = 6;
                                    }else{
                                        $dayOfWeek = $dayOfWeek-1;
                                    }; 
            //get la fecha actual 
            $dateToday = date('Y-m-d');
            
            //creamos la tabla con HTML
            // $calendar = "<table class='table'>";
            // $calendar .= "<center><h2>$monthName $year<h2></center>";
            // $calendar .= "<div class='button_container' style='display: flex; justify-content: space-around; flex-direction: row; align-items:center;'>";
            // $calendar .= "<a class='button' id='monthNext' href='?month=" . date('m', mktime(0, 0, 0, $month-1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month-1, 1, $year)) . "'>Mes anterior</a>";
            // $calendar .= " <a class='button' id='monthCurrent' href='?month=".date('m')."&year=".date('Y')."'>MES ACTUAL</a> ";
            // $calendar .= "<a class='button' id='monthLast' href='?month=" . date('m', mktime(0, 0, 0, $month+1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month+1, 1, $year)) . "'>Mes siguiente</a>";
            // $calendar .= "</div>";
            // $calendar .= "<tr>";

            //attempt to change styles
            $calendar = "<table class='table'>";
            $calendar .= "<div class='monthHeader'>";
            $calendar .= "<a class='buttonMonth' href='?month=" . date('m', mktime(0, 0, 0, $month-1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month-1, 1, $year)) . "'><img src='../img/last.svg' alt='Month Last'></a>";
            $calendar .= "<center><h2>$monthName $year<h2></center>";
            $calendar .= "<a class='buttonMonth' href='?month=" . date('m', mktime(0, 0, 0, $month+1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month+1, 1, $year)) . "'><img src='../img/next.svg' alt='Month Next'></a>";
            $calendar .= "</div>";
            $calendar .= "<div class='subHead'><a class='button' id='monthCurrent' href='?month=".date('m')."&year=".date('Y')."'>MES ACTUAL</a></div>";
            $calendar .= "<tr>";
            //attempt end. success

            // Creamos los headers de la tabla

            foreach($daysOfWeek as $day) {
                $calendar .= "<th  class='header'>$day</th>";
           } 

           // Create the rest of the calendar
      
           // Initiate the day counter, starting with the 1st.
      
           $currentDay = 1;
      
           $calendar .= "</tr><tr>";
      
           // The variable $dayOfWeek is used to
           // ensure that the calendar
           // display consists of exactly 7 columns.
      
           if ($dayOfWeek > 0) { 
               for($k=0;$k<$dayOfWeek;$k++){
                      $calendar .= "<td  class='empty'></td>"; 
      
               }
           }
          
           
           $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        
           while ($currentDay <= $numberDays) {
      
                // Seventh column (Saturday) reached. Start a new row.
      
                if ($dayOfWeek == 7) {
      
                     $dayOfWeek = 0;
                     $calendar .= "</tr><tr>";
      
                }
                
                $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
                $date = "$year-$month-$currentDayRel";
                
                $dayname = strtolower(date('l', strtotime($date)));
                $eventNum = 0;
                $today = $date==date('Y-m-d')? "today" : "";
                if (in_array($dayname, array('saturday', 'sunday'))) {
                    $calendar.="<td><h4>$currentDay</h4> <button class='availability findesemana' id='notAvailable'>No disponible</button>";
                
                }elseif($date<date('Y-m-d')){
                    $calendar.="<td><h4>$currentDay</h4> <button class='availability' id='notAvailable'>N/D</button>";
                }elseif(in_array($date, $bookings)){
                   $calendar.="<td class='$today'><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>RESERVADO</button>";
                }else{
                   $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."'class='availability' id='available'>DISPONIBLE</a>";
                }
                  
                  
                 
                  
                $calendar .="</td>";
                // Increment counters
       
                $currentDay++;
                $dayOfWeek++;
      
           }
           
           
      
           // Complete the row of the last week in month, if necessary
      
           if ($dayOfWeek != 7) { 
           
                $remainingDays = 7 - $dayOfWeek;
                  for($l=0;$l<$remainingDays;$l++){
                      $calendar .= "<td class='empty'></td>"; 
      
               }
      
           }
           
           $calendar .= "</tr>";
      
           $calendar .= "</table>";
      
           return $calendar;
      
        }
          
      ?>
      
    
    <!DOCTYPE html>
    <html lang="es">
      
      <head>
      <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="../img/icons8-appointment-32.png">
        <title>CoraCita</title>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
          
      </head>
      
      <body>
          <div class="container">
              <div class="row">
                  <div class="col-md-12">
                      <?php
                           $dateComponents = getdate();
                           if(isset($_GET['month']) && isset($_GET['year'])){
                               $month = $_GET['month']; 			     
                               $year = $_GET['year'];
                           }else{
                               $month = $dateComponents['mon']; 			     
                               $year = $dateComponents['year'];
                           }
                        //    var_dump($month);
                        //    var_dump($year);
                          echo build_calendar($month,$year);

                      ?>
                  </div>
              </div>
              <button class="button" id="btnHome"><a href="../inicio.php">← &nbsp PAGINA DE INICIO</a></button>
          </div>
      </body>
      
      </html>
      


















































