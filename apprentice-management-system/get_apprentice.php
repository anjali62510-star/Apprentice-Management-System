<?php
include 'config/db.php';

if(isset($_GET['id']) && $_GET['id'] != ''){

    $id = (int)$_GET['id'];

    $res = $conn->query("SELECT name FROM apprentices WHERE t_no=$id");

    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        echo $row['name'];
    } else {
        echo "";
    }
}
?>