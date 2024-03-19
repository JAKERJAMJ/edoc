<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: ../login.php");
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once '../conDB.php';

// ดึงข้อมูลของผู้ใช้จากฐานข้อมูลโดยใช้ user_id จาก session
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

// หากไม่พบข้อมูลผู้ใช้ ให้เปลี่ยนเส้นทางไปยังหน้า login
if (!$user) {
    header("Location: ../login.php");
    exit;
}
?>
<header>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid d-flex justify-content-between">
            <div>
                <a href="UserController.php"><button class="btn btn-light" type="button">E-Document</span></button></a>
            </div>
            <div class="dropdown">
                <button class="btn btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $user['name_user']; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"> <!-- เพิ่ม class dropdown-menu-end เพื่อจัดให้ dropdown อยู่ด้านขวาของ Navbar -->
                    <li><a class="dropdown-item" href="Changepassword.php">เปลี่ยนรหัสผ่าน</a></li>
                    <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                </ul>
                
            </div>
        </div>
    </nav>
</header>