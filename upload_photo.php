<?php
include 'auth.php';
include 'config/db.php';

if(!is_dir('uploads')){
    mkdir('uploads', 0777, true);
}

$id = intval($_POST['id']);

if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ''){

    $allowed = ['jpg','jpeg','png','webp'];

    $fileName = $_FILES['photo']['name'];

    $tmpName = $_FILES['photo']['tmp_name'];

    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if(in_array($ext, $allowed)){

        $newName = time().'_'.rand(1000,9999).'.'.$ext;

        move_uploaded_file(
            $tmpName,
            'uploads/'.$newName
        );

        $stmt = $conn->prepare("
            UPDATE apprentices
            SET photo=?
            WHERE id=?
        ");

        $stmt->bind_param("si", $newName, $id);

        $stmt->execute();
    }
}

header("Location: list.php");
exit();
?>