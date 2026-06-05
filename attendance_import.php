<?php
include 'config/db.php';

if(isset($_FILES['file'])){

    $file = $_FILES['file']['tmp_name'];

    if(!$file){
        die("No file uploaded");
    }

    $handle = fopen($file, "r");

    $inserted = 0;

    while(($line = fgets($handle)) !== false){

        $line = trim($line);

        if(empty($line)) continue;

        // Split by TAB (since your format is tab-based)
        $cols = preg_split('/\t+/', $line);

        // Skip header row
        if($cols[0] == 'SNo') continue;

        if(count($cols) < 3) continue;

        $empid = trim($cols[1]);
        $datetime = trim($cols[2]);

        // Convert datetime
        $dt = DateTime::createFromFormat('d/m/Y h:i:s A', $datetime);

        if(!$dt) continue;

        $date = $dt->format('Y-m-d');
        $time = $dt->format('H:i:s');

        // Get user_id from apprentices
        $get = $conn->query("SELECT id FROM apprentices WHERE t_no='$empid'");
        $row = $get->fetch_assoc();

        if(!$row) continue;

        $user_id = $row['id'];

        // Check existing record
        $check = $conn->query("
            SELECT id FROM attendance 
            WHERE user_id='$user_id' AND attendance_date='$date'
        ");

        if($check->num_rows > 0){

            // Update OUT time
            $conn->query("
                UPDATE attendance 
                SET out_time='$time', last_updated_at=NOW()
                WHERE user_id='$user_id' AND attendance_date='$date'
            ");

        } else {

            // Insert IN time
            $stmt = $conn->prepare("
                INSERT INTO attendance 
                (user_id, attendance_date, arr_time, status, day_type)
                VALUES (?, ?, ?, 'present', 'full')
            ");

            $stmt->bind_param("iss", $user_id, $date, $time);
            $stmt->execute();

            $inserted++;
        }
    }

    fclose($handle);

    echo "<script>
    alert('$inserted records processed');
    window.location='attendance_list.php?date=$date';
    </script>";
}
?>