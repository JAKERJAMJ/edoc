<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่ยนรหัสผ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/user.css">
</head>

<body>
    <?php require_once 'nav_user.php'; ?>
    <div class="password-container">
        <form action="Changepassword.php" method="POST">
            <div class="password-title">
                เปลี่ยนรหัสผ่าน
            </div>
            <div class="user-title">
                คุณ <?php echo $user['name_user']; ?>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">รหัสผ่านใหม่</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword2" class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirmpassword" class="form-control" id="exampleInputPassword1">
            </div>
            <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
        </form>
    </div>

    <?php
    require_once '../conDB.php';

    // ตรวจสอบว่ามีการส่งค่ารหัสผ่านเข้ามาหรือไม่
    if (isset($_POST['password']) && isset($_POST['confirmpassword'])) {
        // รับค่ารหัสผ่านใหม่และการยืนยันรหัสผ่าน
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
    
        // ตรวจสอบว่ารหัสผ่านใหม่และการยืนยันรหัสผ่านตรงกันหรือไม่
        if ($password === $confirmpassword) {
            // ทำการอัพเดทรหัสผ่านใหม่ในฐานข้อมูล
            $user_id = $_SESSION['user_id'];
            $query = "UPDATE user SET user_password = '$password' WHERE user_id = '$user_id'";
            $result = mysqli_query($con, $query);
    
            if ($result) {
                echo "<script>alert ('อัพเดทรหัสผ่านเรียบร้อย')</script>";
            } else {
                echo "เกิดข้อผิดพลาดในการอัพเดทรหัสผ่าน";
            }
        } else {
            echo "รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน";
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>