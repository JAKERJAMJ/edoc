<?php
require '../conDB.php';

if (isset($_POST['docin_id'])) {
    // รับค่า docin_id ที่ต้องการลบ
    $docin_id = $_POST['docin_id'];

    // เขียนคำสั่ง SQL สำหรับลบข้อมูล
    $sql_delete = "DELETE FROM in_doc WHERE docin_id = ?";

    // เตรียมคำสั่ง SQL และผูกค่า parameter
    $stmt = $con->prepare($sql_delete);
    $stmt->bind_param("i", $docin_id);

    // ประมวลผลคำสั่ง SQL
    if ($stmt->execute()) {
        // หากลบข้อมูลสำเร็จ
        echo "<script>alert ('ลบรายการเอกสารเรียบร้อยแล้ว')</script>";
    } else {
        // หากเกิดข้อผิดพลาดในการลบข้อมูล
        echo "<script>alert ('เกิดข้อผิดพลาดในการลบรายการเอกสาร:')</script> " . $stmt->error;
    }

    // ปิดคำสั่ง SQL
    $stmt->close();
}

// ปิดการเชื่อมต่อกับฐานข้อมูล
$con->close();
?>
