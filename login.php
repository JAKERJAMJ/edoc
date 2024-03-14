<?php

require 'nav.php';
session_start();
require_once 'conDB.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="login">
        <div class="title-login">
            login
        </div>
        <div class="input-container">
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
    <?php
    // ตรวจสอบว่ามีการส่งข้อมูลแบบ POST มาหรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าอีเมลและรหัสผ่านจากฟอร์ม
        $email = $_POST["email"];
        $password = $_POST["password"];

        // ตรวจสอบค่าอีเมลและรหัสผ่าน
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            // เพิ่มเงื่อนไขตรวจสอบว่าเป็น admin หรือไม่
            if ($email == "admin@admin.com" && $password == "admin") {
                $_SESSION['admin'] = true;
                // หากค่าตรงกัน ให้เปลี่ยนเส้นทางไปยังหน้าที่คุณต้องการ (Admin)
                header("Location: ./Admin/AdminController.php");
                exit;
            } else {
                // หากค่าไม่ตรงกัน สามารถทำอะไรต่อได้ตามต้องการ เช่น แสดงข้อความผิดพลาด
                $query = "SELECT * FROM user WHERE email = '$email' AND user_password = '$password'";
                $result = mysqli_query($con, $query);

                $row = mysqli_fetch_array($result);

                if ($row) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['name_user'] = $row['name_user'];
                    $_SESSION['de_id'] = $row['de_id'];

                    header("location: ./User/UserController.php");
                } else {
                    // หากไม่พบผู้ใช้ในฐานข้อมูล
                    echo "<script>alert('รหัสผ่านหรืออีเมลไม่ถูกต้อง');</script>";
                }
            }
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>