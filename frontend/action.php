<?php
    // print_r($_REQUEST);
    require_once "dbconfig.php";
    $conn1 = new PDO("mysql:host=localhost;dbname=EventMap", $user, $password);
    echo "Connected to EventMap at localhost successfully.";

    $id = $_REQUEST['id'];
    $lat = $_REQUEST['lat'];
    $lng = $_REQUEST['lng'];

    $sql = "UPDATE `maps` SET `lat` = '$lat', `lng` = '$lng' WHERE `maps`.`id` = '$id'";
    // $q = $conn1->prepare($sql);
    if($conn1->query($sql) == TRUE) {
        echo "Updated.";
    }
    else {
        echo "Updated fails.";
    }
    // $q->execute();
    // $q->setFetchMode(PDO::FETCH_ASSOC);
    

   

?>