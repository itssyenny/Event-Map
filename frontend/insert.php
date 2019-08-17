<?php
    require_once "dbconfig.php";
    $conn1 = new PDO("mysql:host=localhost;dbname=EventMap", $user, $password);
    echo "Connected to EventMap at localhost successfully.";
    if(!empty($_POST['name'])) {
        $sql = "INSERT INTO `maps` (`id`, `name`, `type`, `location`, `lat`, `lng`, `date`, `startTime`, `endTime`) VALUES (NULL, '$_POST[name]', '$_POST[type]', '$_POST[location]', NULL, NULL, '$_POST[date]', '$_POST[startTime]', '$_POST[endTime]');";
        
        if($conn1->query($sql) == TRUE) {
            header("Location: index.php");    
            echo "Inserting.";           
        }
        else {
            echo "Inserting fails.";
        }
    }
?>
