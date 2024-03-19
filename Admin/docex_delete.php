<?php
require '../conDB.php';

if (isset($_POST['docex_id'])) {
    // รับค่า docin_id ที่ต้องการลบ
    $docex_id = $_POST['docex_id'];

    // เขียนคำสั่ง SQL สำหรับลบข้อมูล
    $sql_delete = "DELETE FROM ex_doc WHERE docex_id = ?";

    // เตรียมคำสั่ง SQL และผูกค่า parameter
    $stmt = $con->prepare($sql_delete);
    $stmt->bind_param("i", $docex_id);

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
