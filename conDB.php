<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "e_document";
// create connection
$con = new mysqli($servername,$username,$password,$database);

// check connection
if (mysqli_connect_error()){
    echo "connect database fail";
}else{
    echo "";
}
    
?>