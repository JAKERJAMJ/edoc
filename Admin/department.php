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
    <title>จัดการแผนก</title>
    <link rel="stylesheet" href="../styles/department.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="title">
        <span>จัดการแผนก</span>
    </div>
    <div class="add-button">
        <button type="button" class="btn btn-success" onclick="showPopup()">
            เพิ่ม
        </button>
    </div>
    <div class="view-department">
    <table class="table table-bordered">
    <tr>
        <th width="100px">ลำดับที่</th>
        <th>แผนก</th>
        <th width="50px">จำนวนบุคลากร</th>
        <th width="280px">Action</th>
    </tr>
    <?php
    require '../conDB.php';
    $sql = "SELECT department.de_id, department.de_name, COUNT(user.user_id) AS employee_count
        FROM department
        LEFT JOIN user ON department.de_id = user.de_id
        GROUP BY department.de_id
        ORDER BY department.de_id";

    $result = mysqli_query($con, $sql);
    $counter = 1; // เพิ่มตัวแปรนับลำดับ

    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $counter . "</td>";
        echo "<td>" . $row['de_name'] . "</td>";
        echo "<td>" . $row['employee_count'] . "</td>";
        echo '<td>';
        echo '<button type="button" class="btn btn-warning mr-2" onclick="Edit(' . $row['de_id'] . ', \'' . $row['de_name'] . '\')">แก้ไข</button>';
        echo '&nbsp;&nbsp;&nbsp;'; 
        echo '<button type="button" class="btn btn-danger" onclick="deleteDepartment(' . $row['de_id'] . ')">ลบ</button>';
        echo '</td>';
        echo "</tr>";

        $counter++; // เพิ่มค่าลำดับ
    }
    ?>
</table>

    </div>
    <!-- add function -->
    <div class="add-de" id="add_de">
        <div class="col-md-12">
            <form action="department.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-3">
                    <strong>เพิ่มแผนก</strong>
                    <input type="text" name="de_name" class="form-control" placeholder="Department Name">
                    <button type="submit" class="mt-3 btn btn-primary" name="submit_add" id="submit">Submit</button>
                    <button onclick="hidePopup()" id="close_add" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
    <!-- script add -->
    <script>
        function showPopup() {
            var popup = document.getElementById("add_de");
            popup.style.display = "block";
        }

        function hidePopup() {
            var popup = document.getElementById("add_de");
            popup.style.display = "none";
        }
    </script>
    <!-- end script -->
    <?php
    require '../conDB.php';

    if (isset($_POST['submit_add'])) {
        $de_name = $_POST['de_name'];

        $sql = "INSERT INTO department (de_id, de_name) 
                    VALUES (NULL, '$de_name')";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }

    ?>
    <!-- end add function -->
    <!-- edit function -->

    <div class="edit-de" id="edit">
        <div class="col-md-12">
            <form action="department.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-3">
                    <strong id="edit-text">แก้ไขแผนก</strong>
                    <input type="text" name="de_name" class="form-control" placeholder="" id="edit_de_name">
                    <input type="hidden" name="edit_de_id" id="edit_de_id"> <!-- เพิ่ม input hidden เพื่อเก็บ ID ที่จะแก้ไข -->
                    <button type="submit" class="mt-3 btn btn-primary" name="submit_edit" id="submit_edit">Submit</button> <!-- เปลี่ยนชื่อปุ่ม Submit เป็น submit_edit -->
                    <button type="button" onclick="hideEdit()" id="close_edit" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function Edit(id, name) {
            var popup = document.getElementById("edit");
            var inputField = popup.querySelector("input[name='de_name']");
            var inputId = popup.querySelector("input[name='edit_de_id']"); // เพิ่มการค้นหา input ของ ID
            inputField.value = name; // ใช้ value แทน placeholder เพื่อให้แสดงข้อมูลที่แก้ไขได้
            inputId.value = id; // ใส่ค่า ID ลงใน input hidden
            popup.style.display = "block";
        }

        function hideEdit() {
            var popup = document.getElementById("edit");
            popup.style.display = "none";
        }
    </script>

    <?php
    if (isset($_POST['submit_edit'])) {
        $de_id = $_POST['edit_de_id']; // รับค่า ID ที่ต้องการแก้ไข
        $de_name = $_POST['de_name'];

        $sql = "UPDATE department SET de_name='$de_name' WHERE de_id=$de_id";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }
    ?>

    <!-- end edit function -->

    <!-- delete function -->
    <script>
        function deleteDepartment(id) {
            if (confirm("คุณต้องการลบรายการนี้ใช่หรือไม่?")) {
                // ส่งคำร้องขอ AJAX ไปยังไฟล์ PHP เพื่อลบข้อมูล
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_department.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        // เมื่อลบข้อมูลเสร็จสิ้น รีโหลดหน้าเว็บ
                        window.location.reload();
                    }
                };
                xhr.send("de_id=" + id);
            }
        }
    </script>
    <!-- end function delete -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>