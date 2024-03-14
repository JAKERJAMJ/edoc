<?php
// เริ่มหรือดำเนินการกับเซสชัน
session_start();

// ตรวจสอบว่ามี session 'admin' ที่ถูกตั้งขึ้นหรือไม่
if (!isset($_SESSION['admin'])) {
    // หากไม่มี session 'admin' ให้เปลี่ยนเส้นทางไปยังหน้าล็อกอินหรือหน้าที่ต้องการ
    header("Location: ../login.php");
    exit; // จบการทำงานของสคริปต์
}
require 'adminnav.php';
// หากผู้ใช้ล็อกอินแล้วให้แสดงเนื้อหาของหน้าเว็บไซต์ต่อไปนี้
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบุคลากร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/add_user.css">
</head>

<body>
    <div class="title">จัดการบุคลากร</div>
    <div class="add-button">
        <button type="button" class="btn btn-success" onclick="AddUser()">เพิ่ม</button>
    </div>
    <div class="view-user">
        <table class="table table-bordered">
            <tr>
                <th width="100px">ลำดับที่</th>
                <th width="300px">ชื่อ</th>
                <th width="280px">แผนก</th>
                <th width="280px">เบอร์โทรศัพท์</th>
                <th width="280px">Email</th>
                <th width="280px">Password</th>
                <th width="280px">Action</th>
            </tr>
            <?php
            require '../conDB.php';

            $sql = "SELECT user.user_id, user.name_user, user.phone_number, user.email, user.user_password, department.de_name
        FROM user
        LEFT JOIN department ON user.de_id = department.de_id
        ORDER BY user.user_id";
            $result = mysqli_query($con, $sql);
            $counter = 1;
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td>" . $row['name_user'] . "</td>";
                echo "<td>" . $row['de_name'] . "</td>";
                echo "<td>" . $row['phone_number'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['user_password'] . "</td>";
                echo '<td>';
                echo '<button type="button" class="btn btn-warning mr-2" onclick="Edit(' . $row['user_id'] . ', \'' . $row['name_user'] . '\', \'' . $row['phone_number'] . '\', \'' . $row['email'] . '\', \'' . $row['user_password'] . '\', \'' . $row['de_name'] . '\')">แก้ไข</button>';
                echo '&nbsp;&nbsp;&nbsp;'; 
                echo '<button type="button" class="btn btn-danger" onclick="deleteUser(' . $row['user_id'] . ')">ลบ</button>';
                echo '</td>';
                echo "</tr>";

                $counter++;
            }

            ?>
        </table>
    </div>



    <!-- function add -->
    <div class="add-user" id="AddUser">
        <div class="col-md-12">
            <div class="title-add">
                เพิ่มข้อมูลบุคลากร
            </div>
            <form action="add_user.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-3">
                    <strong>ชื่อ</strong>
                    <input type="text" name="name_user" class="form-control" placeholder="ชื่อบุคลากร">
                </div>
                <div class="form-group my-3">
                    <strong>Email</strong>
                    <input type="email" name="email" class="form-control" placeholder="Email">
                </div>
                <div class="form-group my-3">
                    <strong>Password</strong>
                    <input type="text" name="user_password" class="form-control" placeholder="Password">
                </div>
                <div class="form-group my-3">
                    <strong>เบอร์โทรศัพท์.</strong>
                    <input type="text" name="phone_number" class="form-control" placeholder="เบอร์โทรศัพท์">
                </div>
                <div class="form-group my-3">
                    <strong>แผนก</strong>
                    <select name="department" id="department" class="form-select">
                        <option value="">เลือกแผนก</option>

                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM department ORDER BY de_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ในแท็ก <option>
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['de_id'] . "'>" . $row['de_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>

                    </select>
                </div>
                <div class="form-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="submit_add" id="submit">Submit</button>
                    <button onclick="hideAddUser()" id="close_add" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function AddUser() {
            var popup = document.getElementById("AddUser");
            popup.style.display = "block";
        }

        function hideAddUser() {
            var popup = document.getElementById("AddUser");
            popup.style.display = "none";
        }
    </script>

    <?php
    require '../conDB.php';

    if (isset($_POST['submit_add'])) {
        $name_user = $_POST['name_user'];
        $email = $_POST['email'];
        $user_password = $_POST['user_password'];
        $phone_number = $_POST['phone_number'];
        $de_id = $_POST['department']; // Get the selected de_id

        $sql = "INSERT INTO user (user_id, email, user_password, name_user, phone_number, de_id) 
            VALUES (NULL, '$email', '$user_password', '$name_user', '$phone_number', '$de_id')";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }

    ?>

    <!-- end add function -->

    <!-- function Edit -->

    <div class="edit-user" id="EditUser">
        <div class="col-md-12">
            <div class="title-edit">
                แก้ไขข้อมูลบุคลากร
            </div>
            <form action="add_user.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" id="edit_user_id" value="">
                <div class="form-group my-3">
                    <label for="name_user"><strong>ชื่อ</strong></label>
                    <input type="text" id="name_user" name="name_user" class="form-control" placeholder="" value="">
                    <input type="hidden" name="edit_name_user" id="edit_user_id" value="">
                </div>
                <div class="form-group my-3">
                    <label for="email"><strong>Email</strong></label>
                    <input type="email" name="email" class="form-control" placeholder="" value="">
                    <input type="hidden" name="edit_email" id="edit_email" value="">
                </div>
                <div class="form-group my-3">
                    <strong>Password</strong>
                    <input type="text" name="user_password" class="form-control" placeholder="" value="">
                    <input type="hidden" name="edit_user_password" id="edit_user_password" value="">
                </div>

                <div class="form-group my-3">
                    <strong>เบอร์โทรศัพท์.</strong>
                    <input type="text" name="phone_number" class="form-control" placeholder="" value="">
                    <input type="hidden" name="user_phone_number" id="edit_phone_number" value="">
                </div>
                <div class="form-group my-3">
                    <strong>แผนก</strong>
                    <input type="hidden" name="user_department" id="edit_department" value="">
                    <select name="department" id="department" class="form-select">

                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM department ORDER BY de_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ในแท็ก <option>
                        while ($row = mysqli_fetch_array($result)) {
                            $selected = ''; // เพิ่มตัวแปรเพื่อเก็บค่า selected
                            if ($row['de_id'] == $user_department) {
                                $selected = 'selected'; // ถ้า de_id ตรงกับค่า user_department ให้กำหนด selected
                            }
                            echo "<option value='" . $row['de_id'] . "' $selected>" . $row['de_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>

                    </select>
                </div>

                <div class="form-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="submitEdit" id="submit">Submit</button>
                    <button type="button" onclick="hideEditUser()" id="close_edit" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function Edit(user_id, name_user, phone_number, email, user_password, department) {
            var popup = document.getElementById("EditUser");
            var inputId = popup.querySelector("input[name='user_id']");
            var inputName = popup.querySelector("input[name='name_user']");
            var inputEmail = popup.querySelector("input[name='email']");
            var inputPassword = popup.querySelector("input[name='user_password']");
            var inputPhone = popup.querySelector("input[name='phone_number']");
            var inputDepartment = popup.querySelector("select[name='department']");

            inputId.value = user_id;
            inputName.value = name_user;
            inputEmail.value = email;
            inputPassword.value = user_password;
            inputPhone.value = phone_number;

            // Set the selected department in the dropdown
            var options = inputDepartment.options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].text === department) {
                    inputDepartment.selectedIndex = i;
                    break;
                }
            }

            popup.style.display = "block";
        }



        function hideEditUser() {
            var popup = document.getElementById("EditUser");
            popup.style.display = "none";
        }
    </script>



    <?php
    require '../conDB.php';

    if (isset($_POST['submitEdit'])) {
        $user_id = $_POST['user_id'];
        $name_user = $_POST['name_user'];
        $email = $_POST['email'];
        $user_password = $_POST['user_password'];
        $phone_number = $_POST['phone_number'];
        $de_id = $_POST['department'];

        // Use prepared statement to prevent SQL injection
        $sql = $con->prepare("UPDATE user SET name_user = ?, email = ?, user_password = ?, phone_number = ?, de_id = ? WHERE user_id = ?");
        $sql->bind_param("sssssi", $name_user, $email, $user_password, $phone_number, $de_id, $user_id);

        if ($sql->execute()) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
        $con->close();
    }

    ?>


    <!-- end Edit function -->


    <!-- Function Delete -->
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function deleteUser(userId) {
            var confirmation = confirm("Are you sure you want to delete this user?");
            if (confirmation) {
                $.ajax({
                    type: "POST",
                    url: "delete_user.php", // Replace with the actual server-side script
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        // Handle the response, e.g., refresh the page
                        window.location.href = window.location.href;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting user:", error);
                    }
                });
            }
        }
    </script>
    <!-- end delete funtion -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>