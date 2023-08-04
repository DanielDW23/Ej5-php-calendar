

<?php

include("conexionBaseDatos.php");

if(isset($_GET['date'])){
    $date = $_GET['date'];
    $stmt = $pdo->prepare("SELECT * FROM cita WHERE fecha = :date");
    $stmt->bindParam(":date", $date);
    $bookings = array();

    if ($stmt->execute()) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
    }
}

if(isset($_POST['submit'])){
    
    $name = $_POST['name'];
    $apellido = $_POST['apellido'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $email = $_POST['email'];
    $empresa = isset($_POST['empresa']) ? $_POST['empresa'] : null;
    $motivo = $_POST['motivo'];
    $timeslot = $_POST['timeslot'];
    $fechaEnv = $_POST['fechaEnv'];

    $stmt = $pdo->prepare("SELECT * FROM cita WHERE fecha = :date");
    $stmt->bindParam(":date", $date);
    if($stmt->execute()){
        $result = $stmt->fetchAll();
        if(count($result)>0){
            $msg = "<div class='alert alert-danger'>Ya reservado</div>";
        }else{

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, telefono, email, empresa) VALUES (:name, :apellido, :telefono, :email, :empresa)");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":apellido", $apellido);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":empresa", $empresa);
            $stmt->execute();
    
            $stmt = $pdo->prepare("INSERT INTO cita (motivo,fecha,timeslot) VALUES (:motivo, :fechaEnv, :timeslot)");
            $stmt->bindParam(":motivo", $motivo);
            $stmt->bindParam(":fechaEnv", $fechaEnv);
            
            $stmt->bindParam(":timeslot", $timeslot);
            $stmt->execute();
    
            $msg = "<div class='alert alert-success'>Reserva realizada correctamente</div>";
    
            
        }

    }

    
    // $stmt = $mysqli->prepare("INSERT INTO bookings (name, email, date) VALUES (?,?,?)");
    // $stmt->bind_param('sss', $name, $email, $date);
    // $stmt->execute();
    // $msg = "<div class='alert alert-success'>Booking Successfull</div>";
    // $stmt->close();
    // $mysqli->close();
}

$duration = 30;
$cleanup = 0;
$start = "9:00";
$end = "17:00";

function timeslots($duration, $cleanup, $start, $end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();

    for($intStart = $start; $intStart < $end; $intStart -> add($interval)->add($cleanupInterval)){
    $endPeriod = clone $intStart;
    $endPeriod-> add($interval);
    if($endPeriod > $end){
        break;
    }

    $slots[] = $intStart-> format("H:i")."-" . $endPeriod->format("H:i");
    }
    return $slots; 
}

?>
<!DOCTYPE html>
<html lang="es">

  <head>

    <meta charset="UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="../img/icons8-appointment-32.png">
    <title>CoraCita</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/book.css">
  </head>

  <body>


    <div class="containerSlot">
                <h1 class="text-center">Reserva de citas: <?php echo date('d/m/Y', strtotime($date)); ?></h1><hr>
                <div class="row ">
                    <div class="col-md-12">
                        <?php echo isset($msg)?$msg:""?>
                    </div>
                    <?php $timeslots = timeslots($duration, $cleanup, $start, $end);
                    foreach ($timeslots as $ts) {
                    ?>
                    <div class= "col-md-2">
                        <div class="form-group">
                        <button class="btn btn-primary book openModalBtnSlot" data-timeslot="<?php echo $ts; ?>"> <?php echo $ts;?></button>
                        </div>
                    </div>

                    <?php 
                }
                ?>
                </div>
                    <button class="btn-home"><a href="indexCal.php">VOLVER AL CALENDARIO DE CITAS</a></button>
                    <button class="btn-home"><a href="../inicio.php">PAGINA DE INICIO</a></button>
                
                </div>

            


    </div>

    <div class="container">
        <div id="myModal" class="modal">
                <div class="image-container">
                <img src="../img/decoration.svg" alt="Modal Image">
                </div>
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- <span class="close">&times;</span> -->
        
        <h1 class="text-center">Reserva de cita: <?php echo date('d/m/Y', strtotime($date)); ?></h1>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <?php echo isset($msg)?$msg:''; ?>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" autocomplete="off">

                    <input type="hidden" name="fechaEnv" value = <?php echo date('Y/m/d') ?>>

                    <div class="form-group">
                        <label for="timeslot">Franja horaria:</label>
                        <input type="text" readonly class="form-control" name="timeslot" id="timeslot" value="">
                    </div>
                    <div class="form-group">
                        <label for="name">Nombre</label><img class="asterikRed"src="..\img\icons8-asterisk-24.png" alt="asterisco rojo">
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label><img class="asterikRed"src="..\img\icons8-asterisk-24.png" alt="asterisco rojo">
                        <input type="text" class="form-control" name="apellido">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label><img class="asterikRed"src="..\img\icons8-asterisk-24.png" alt="asterisco rojo">
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa</label>
                        <input type="text" class="form-control" name="empresa">
                    </div>
                    <div class="form-group">
                        <label for="motivo">Motivo:</label><img class="asterikRed"src="..\img\icons8-asterisk-24.png" alt="asterisco rojo"><br>
                        <textarea id="motivo" name="motivo" cols="80" rows="5"></textarea> 
                    </div>
                    <button class="btn-submit" type="submit" name="submit">Enviar</button><img class="asterikRed asterikTexto"src="..\img\icons8-asterisk-24.png" alt="asterisco rojo"><span class="textRed">Campos obligatorios</span>
                </form>
            </div>
        </div>
    </div>

   
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="../js/book.js"></script>
    <script>
        // $(".book").click(function(){
        //     let timeslot = $(this).attr('data-timeslot');
        //     $("#slot").html(timeslot);
        //     $("#timeslot").val(timeslot);
        //     $("#myModal").modal("show");
        // })

    </script>
  </body>

</html>
