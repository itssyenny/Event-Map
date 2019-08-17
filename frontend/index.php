<?php
    require_once 'dbconfig.php';
    try {
        $conn = new PDO("mysql:host=localhost;dbname=EventMap", $user, $password);
        // echo "Connected to EventMap at $host successfully.";
        $sql = "SELECT * FROM `maps` WHERE lat IS NULL AND lng IS NULL";
        // $q = $conn->query($sql);
        $q = $conn->prepare($sql) ;
        $q->execute(array());
        $q->setFetchMode(PDO::FETCH_ASSOC);

        $sqlAll = "SELECT * FROM `maps`";
        $All = $conn->prepare($sqlAll) ;
        $All->execute(array());
        $All->setFetchMode(PDO::FETCH_ASSOC);

    } catch(PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
    }

    function updateEventWithLatLng() {
        $sql = "UPDATE `maps` SET lat = :lat, lng = :lng WHERE id = :id";
        $q = $conn->prepare($sql);

        if($q->execute()) {
            return true;
        }
        else {
            return false;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        
    <style>
        #map {
            height: 600px; /* The height is 400 pixels */
            width: 1000px; /* The width is the width of the web page*/
            margin-left: 1em;
            margin-top: 1em;
        }
        #data, #Alldata {
            display: none;
        }
    
        #addbtn {
            margin-top: -25em;
            margin-left: 65em;
            /* margin-bottom: 10em; */
        }
        #btn {
            width: 230px;
            height: 30px;
            text-align: center;
            font-size: 12px;
            margin-bottom: 1em;
            cursor:pointer;
        }
        #add {
            display: none;
            position: relative;
        }
        #input[type="submit"] {
            background: none;
            cursor: pointer;
        }
        #select {
            width: 100px;
            height: 30px;
            text-align: center;
            position: relative;
            font-size: 12px;
            margin-top: 10px;
            margin-bottom: 1em;
            margin-left: 65rem;
        }
    </style>
    <title>Team 8 Google Maps</title>
    
</head>

<body>
  <h3><center>Team 8 Google Maps</center></h3>
    <!--The div element for the map -->
        <div class="container">

            <?php
                $event = $q->fetchAll();
                $Allevent = $All->fetchAll();
                function utf8ize($d) {
                    if (is_array($d)) {
                        foreach ($d as $k => $v) {
                            $d[$k] = utf8ize($v);
                        }
                    } else if (is_string ($d)) {
                        return utf8_encode($d);
                    }
                    return $d;
                }
                   
                echo '<div id="data">' . json_encode(utf8ize($event)) . '</div>';     
                echo '<div id="Alldata">' . json_encode(utf8ize($Allevent)) . '</div>';                
            ?>
  
            <div id="map"></div>            
        
            <div id="addbtn">
                <button type="button" class="btn btn-primary" id="btn"> Add Event </button>
                <form id="add" action="insert.php" method="post">
                    <p>Event Name &nbsp;<input type="text" name="name" placeholder="Event Name"/></p>
                    <p>Event Type &nbsp;
                        <select name="type" style="width: 200px; height: 30px; font-size: 12px; text-align-last:center;" class="btn btn-primary">
                        <option name="any type" value="any types">Any types</option>
                        <option name="music" value="music">Musics</option>
                        <option name="education" value="education">Education</option>
                        <option name="health" value="health">Health</option>
                        </select>
                    </p>
                    <p>Location &nbsp; &nbsp; &nbsp; &nbsp;<input type="text" name="location" placeholder="Event Location"/></p>
                    <p>Date &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type="text" name="date" placeholder="YYYY-MM-DD"/></p>
                    <p>Start Time &nbsp; &nbsp;<input type="text" name="startTime" placeholder="HH:MM:SS" /></p>
                    <p>End Time &nbsp; &nbsp; &nbsp;<input type="text" name="endTime" placeholder="HH:MM:SS" /></p>
                    
                    <input class="btn btn-primary" type="submit" id="submit"><br><br>
                </form>
            </div>

            <div id="select">
                    <select style="width: 230px; height: 30px; font-size: 12px; text-align-last:center;" class="btn btn-primary">
                    <option value="volvo">Any types</option>
                    <option value="saab">Musics</option>
                    <option value="opel">Education</option>
                    <option value="audi">Health</option>
                    </select>
            </div>
        </div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("button").click(function() {
                $("form").toggle();
            });
        });
        
        var map;
        var geocoder;

        // Initialize and add the map
        function initMap() {
            // The location of Uluru
            var ntu = {lat: 25.0173, lng: 121.5398};
            // The map, centered at Uluru
            map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: ntu});

            var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h1 id="firstHeading" class="firstHeading" style="font-size: 36px;">NTU Event</h1>'+
            '<div id="bodyContent">'+
            '<p style="font-size: 14px">type: musical<br>location: NTU campus<br>date: 05 June 2019</p>'+
            '</div>'+
            '</div>';

            var infowindow = new google.maps.InfoWindow({ content: contentString });

            var marker = new google.maps.Marker({position: ntu, map: map});
           
            
            var cdata = document.getElementById('data').innerHTML;
            
            cdata = JSON.parse(cdata);
            Array.prototype.forEach.call(cdata, function(data) {
                var location = data.name + ' ' + data.location;
                console.log(location);
            });

            
            geocoder = new google.maps.Geocoder();
            codeAddress(cdata);

            var Alldata = document.getElementById('Alldata').innerHTML;
            Alldata = JSON.parse(Alldata);

            var infoWind = new google.maps.InfoWindow;
            Array.prototype.forEach.call(Alldata, function(data) {
                var content = document.createElement('div');
                content.setAttribute("id", "content");
                var incontent = document.createElement('div');
                incontent.setAttribute("id", "siteNotice");
                var h1 = document.createElement('div');
                h1.setAttribute("id", "firstHeading");
                h1.setAttribute("style", "font-size: 36px; font-weight: bold;");
                h1.textContent = data.name;

                var incontent1 = document.createElement('div');
                incontent1.setAttribute("id", "bodyContent");
                var p = document.createElement('div');
                p.setAttribute("style", "font-size: 14px;");
                p.innerHTML = `Type: ${data.type}<br />Location: ${data.location}<br />Date:${data.date}<br />Time:${data.startTime} to ${data.endTime}`;
                incontent1.appendChild(p);

                content.appendChild(incontent).appendChild(h1);
                content.appendChild(incontent1);

                var marker1 = new google.maps.Marker({position: new google.maps.LatLng(data.lat, data.lng), map: map});
                
                marker1.addListener('click', function() { infoWind.setContent(content); infoWind.open(map, marker1); });
            });

            var ntu2 = {lat: 25.0261, lng: 121.5275};
            var marker1 = new google.maps.Marker({position: ntu2, map: map});
            marker1.addListener('click', function() { infowindow.open(map, marker1); });
        }

        function codeAddress(cdata) {
            Array.prototype.forEach.call(cdata, function(data) {
                var address = data.name + ' ' + data.location;
                
                geocoder.geocode( { 'address': address}, function(results, status) {
                    if (status == 'OK') {
                        map.setCenter(results[0].geometry.location);
                        // update the lat and lng
                        var points = {};
                        points.id = data.id;
                        points.lat = map.getCenter().lat();
                        points.lng = map.getCenter().lng();
                        updateEventWithLatLng(points);
                        // alert(map.getCenter().lat());
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });
        }

        function  updateEventWithLatLng(points) {
            $.ajax({

                url:"action.php",
                method:"post",
                data: points,
                success: function(res) {
                    console.log(res);
                }
            });
        }
    
    </script>
 
<!-- Replace the value of the key parameter with your own API key. -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWChHtaVbTFZ1vPjoor6Vp4OAKDUkKc_M&callback=initMap">
    </script>

    
</script> 
</body>
</html>
