<?php
$username = "root";
$password = "";
$database = "users";
$server = "localhost";

$conn = mysqli_connect($server,$username,$password,$database);
if (!$conn){
//   echo "Success";
// }
// else{
  die("Error:".mysqli_connect_error());
}
?>