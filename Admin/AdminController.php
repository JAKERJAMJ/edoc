<?php
// เริ่มหรือดำเนินการกับเซสชัน
session_start();

// ตรวจสอบว่ามี session 'admin' ที่ถูกตั้งขึ้นหรือไม่
if (!isset($_SESSION['admin'])) {
    // หากไม่มี session 'admin' ให้เปลี่ยนเส้นทางไปยังหน้าล็อกอินหรือหน้าที่ต้องการ
    header("Location: ../login.php");
    exit; // จบการทำงานของสคริปต์
}
require 'adminnav.php'
// หากผู้ใช้ล็อกอินแล้วให้แสดงเนื้อหาของหน้าเว็บไซต์ต่อไปนี้
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Controller</title>
    <link rel="stylesheet" href="../styles/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="top">
        <span class="title">การจัดการข้อมูล</span>
    </div>
    <div class="all-button text-center">
        <a href="add_user.php"><button type="button" class="btn btn-secondary">จัดการผู้ใช้</button></a>
        <a href="department.php"><button type="button" class="btn btn-secondary">จัดการแผนก</button></a>
        <button type="button" class="btn btn-success" onclick="OpenDocument()">เพิ่มเอกสาร</button>
    </div>

    <div class="document" id="DocumentType">
        <button type="button" class="close" aria-label="Close" onclick="CloseDocument()">
            <span aria-hidden="true">&times;</span>
        </button>
        <span class="type">เลือกประเภทเอกสาร</span>
        <div class="in">
            <a href="in_document.php"><button type="button" class="btn btn-outline-success">บันทึกข้อความเข้า</button></a>
        </div>
        <div class="ex">
            <a href="ex_document.php"><button type="button" class="btn btn-outline-info">หนังสือเข้าภายนอก</button></a>
        </div>
    </div>

    <script>
        function OpenDocument() {
            var popup = document.getElementById("DocumentType");
            popup.style.display = "block";
        }

        function CloseDocument() {
            var popup = document.getElementById("DocumentType");
            popup.style.display = "none";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>