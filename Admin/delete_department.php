<?php
// เริ่มหรือดำเนินการกับเซสชัน
session_start();

// ตรวจสอบว่ามี session 'admin' ที่ถูกตั้งขึ้นหรือไม่
if (!isset($_SESSION['admin'])) {
    // หากไม่มี session 'admin' ให้เปลี่ยนเส้นทางไปยังหน้าล็อกอินหรือหน้าที่ต้องการ
    header("Location: ../login.php");
    exit; // จบการทำงานของสคริปต์
}

if (isset($_POST['de_id'])) {
    require '../conDB.php';
    $de_id = $_POST['de_id'];
    // สร้างคำสั่ง SQL เพื่อลบข้อมูล
    $sql = "DELETE FROM department WHERE de_id = $de_id";
    // ดำเนินการลบข้อมูล
    if ($con->query($sql) === TRUE) {
        // ส่งข้อมูลกลับถ้าลบสำเร็จ
        echo "Deleted successfully";
    } else {
        // ส่งข้อความข้อผิดพลาดถ้าเกิดข้อผิดพลาดในการลบ
        echo "Error: " . $sql . "<br>" . $con->error;
    }
    // ปิดการเชื่อมต่อกับฐานข้อมูล
    $con->close();
}
