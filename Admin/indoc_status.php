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
require '../conDB.php';

$user_user = $_SESSION['user_id']; // ตัวอย่างเท่านี้ คุณอาจต้องเปลี่ยนแปลงตามโครงสร้างของระบบของคุณ
$user_department = $_SESSION['department_id']; // เช่นเดียวกัน ต้องเปลี่ยนแปลงตามโครงสร้างของระบบของคุณ

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/update_status.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="btn-back">
        <a href="in_document.php"><button type="button" class="btn btn-outline-dark">back</button></a>
    </div>
    <?php
    $docin_id = $_GET['id'];
    $sql = "SELECT * FROM in_doc WHERE docin_id = '$docin_id' ";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    ?>
    <div class="container">
        <div class="status-container">
            <div class="status-title">
                สถานะของเอกสาร <br>
                เอกสารเลขที่ : <?= $row['docin_number'] ?>
            </div>
            <form action="indoc_status.php?id=<?= $row['docin_id'] ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="docin_id" value="<?= $row['docin_id'] ?>">
                <div class="mb-3">
                    <select class="form-select mt-3" name="doc_status" id="SelectStatus" onchange="Status()">
                        <!-- แสดงสถานะเริ่มต้นของเอกสาร -->
                        <?php
                        // ดึงข้อมูลสถานะจากฐานข้อมูล
                        $sql_status = "SELECT * FROM doc_status ORDER BY status_id";
                        $result_status = mysqli_query($con, $sql_status);
                        while ($row_status = mysqli_fetch_array($result_status)) {
                            // ตรวจสอบว่าค่าสถานะนั้นตรงกับค่าที่อยู่ในตาราง in_doc หรือไม่
                            $selected = ($row_status['status_id'] == $row['status_id']) ? 'selected' : '';
                            echo "<option value='" . $row_status['status_id'] . "' $selected>" . $row_status['status_name'] . "</option>";
                        }
                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <select class="form-select mt-3" id="SelectSendto" onchange="StatusSendto()" style="display: none;">
                        <option selected>ต้องการส่งเอกสารให้</option>
                        <option value="1">รายบุคคล</option>
                        <option value="2">แผนก</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check mt-2" id="UserCheckbox" style="display: none;">
                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
                        $sql = "SELECT * FROM user ORDER BY user_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ใน checkbox
                        while ($row = mysqli_fetch_array($result)) {
                            $selected = ''; // เพิ่มตัวแปรเพื่อเก็บค่า selected
                            if ($row['user_id'] == $user_user) { // อาจเกิดปัญหาที่นี่เนื่องจากตัวแปร $user_user ไม่ได้ถูกกำหนดค่า
                                $selected = 'checked'; // ถ้า user_id ตรงกับค่า user_user ให้กำหนด checked
                            }
                            echo "<input class='form-check-input' type='checkbox' name='users[]' id='user_" . $row['user_id'] . "' value='" . $row['user_id'] . "' $selected>";
                            echo "<label class='form-check-label' for='user_" . $row['user_id'] . "'>" . $row['name_user'] . "</label><br>";
                        }
                        ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check mt-2" id="DepartmentCheckbox" style="display: none;">
                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM department ORDER BY de_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ใน checkbox
                        while ($row = mysqli_fetch_array($result)) {
                            $selected = ''; // เพิ่มตัวแปรเพื่อเก็บค่า selected
                            if ($row['de_id'] == $user_department) {
                                $selected = 'checked'; // ถ้า de_id ตรงกับค่า user_department ให้กำหนด checked
                            }
                            echo "<input class='form-check-input' type='checkbox' name='departments[]' id='department_" . $row['de_id'] . "' value='" . $row['de_id'] . "' $selected>";
                            echo "<label class='form-check-label' for='department_" . $row['de_id'] . "'>" . $row['de_name'] . "</label><br>";
                        }

                        ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="additionalDetails">รายละเอียดเพิ่มเติม</label>
                    <textarea class="form-control" id="detail" name="detail" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary" name="SubmitStatus" id="SubmitStatus">ดำเนินการต่อ</button>
                    <button type="submit" class="btn btn-primary" name="SubmitUser" id="SubmitUser" style="display: none;">ส่งให้ผู้ใช้</button>
                    <button type="submit" class="btn btn-primary" name="SubmitDe" id="SubmitDe" style="display: none;">ส่งให้แผนก</button>
                </div>
            </form>
        </div>
        <!-- status function -->
        <script>
            function Status() {
                var status = document.getElementById("EditStatus");
                var selectStatus = document.getElementById("SelectStatus");
                var selectSendto = document.getElementById("SelectSendto");
                var checkboxUser = document.getElementById("UserCheckbox");
                var checkboxDepartment = document.getElementById("DepartmentCheckbox");
                var buttonUser = document.getElementById("SubmitUser");
                var buttonDe = document.getElementById("SubmitDe");
                var buttonSubmit = document.getElementById("SubmitStatus");

                // เมื่อเลือก status รับทราบ : ดำเนินการต่อ
                if (selectStatus.value === "3") {
                    // แสดงเลือกว่าจะส่งให้แผนกหรือรายบุคคล
                    selectSendto.style.display = "block";

                    // เรียกใช้ฟังก์ชันเพื่อแสดงส่วนที่เหมาะสมตามค่าของ selectSendto ที่เปลี่ยนแปลง
                    StatusSendto();
                } else if (selectStatus.value === "2") {
                    buttonSubmit.style.display = "block";
                } else {
                    selectSendto.style.display = "none";
                    checkboxDepartment.style.display = "none";
                    checkboxUser.style.display = "none";
                    buttonDe.style.display = "none";
                    buttonUser.style.display = "none";
                    buttonSubmit.style.display = "block";

                }

            }

            function CloseStatus() {
                var status = document.getElementById("EditStatus");

            }

            function StatusSendto() {
                var selectSendto = document.getElementById("SelectSendto");
                var checkboxUser = document.getElementById("UserCheckbox");
                var checkboxDepartment = document.getElementById("DepartmentCheckbox");
                var buttonUser = document.getElementById("SubmitUser");
                var buttonDe = document.getElementById("SubmitDe");
                var buttonSubmit = document.getElementById("SubmitStatus");

                if (selectSendto.value === "1") {
                    checkboxUser.style.display = "block";
                    checkboxDepartment.style.display = "none";
                    buttonUser.style.display = "block";
                    buttonDe.style.display = "none";
                    buttonSubmit.style.display = "none";
                } else if (selectSendto.value === "2") {
                    checkboxDepartment.style.display = "block";
                    checkboxUser.style.display = "none";
                    buttonUser.style.display = "none";
                    buttonDe.style.display = "block";
                    buttonSubmit.style.display = "none";
                } else {
                    checkboxDepartment.style.display = "none";
                    checkboxUser.style.display = "none";
                    buttonUser.style.display = "none";
                    buttonDe.style.display = "none";
                    buttonSubmit.style.display = "block"; // แสดงปุ่ม "ดำเนินการต่อ" เมื่อไม่มีการเลือกใน select box
                }
            }
        </script>

        <?php
        require '../conDB.php';

        if (isset($_POST['SubmitStatus'])) {
            $ids = $_POST['docin_id']; // เพิ่มบรรทัดนี้เพื่อรับค่า docin_id จากฟอร์ม POST
            $status_id = $_POST['doc_status'];

            $sql_updateStatus = "UPDATE in_doc SET status_id = '$status_id' WHERE docin_id = '$ids'";

            if (mysqli_query($con, $sql_updateStatus)) {
                echo "<script>alert('อัปเดตข้อมูลเรียบร้อยแล้ว'); window.location.href = 'in_document.php';</script>";
            } else {
                echo "Error updating record: " . mysqli_error($con); // แสดงข้อผิดพลาด
            }
        }

        if (isset($_POST['SubmitDe'])) {
            $docin_id = $_POST['docin_id'];
            $de_ids = $_POST['departments'];
            $detail = $_POST['detail'];

            foreach ($de_ids as $de_id) {
                $sql_insertHistoryIn = "INSERT INTO document_history_in (docin_id, de_id, detail) VALUES ('$docin_id', '$de_id', '$detail')";
                mysqli_query($con, $sql_insertHistoryIn);
            }

            // ตรวจสอบว่าส่งเอกสารให้แผนกหรือไม่
            if (count($de_ids) > 0) {
                // อัปเดตสถานะของเอกสารเป็น "รับทราบและดำเนินการต่อ"
                $status_id = 2; // สมมติว่าสถานะ "รับทราบและดำเนินการต่อ" มี ID เป็น 2
                $sql_updateStatus = "UPDATE in_doc SET status_id = '$status_id' WHERE docin_id = '$docin_id'";
                mysqli_query($con, $sql_updateStatus);

                echo "<script>alert('ส่งเอกสารให้แผนกเรียบร้อยแล้ว'); window.location.href = 'in_document.php';</script>";
            }
        }

        if (isset($_POST['SubmitUser'])) {
            $docin_id = $_POST['docin_id'];
            $user_ids = $_POST['users']; // รับค่าผู้ใช้ที่ถูกเลือกจาก checkbox
            $detail = $_POST['detail'];

            foreach ($user_ids as $user_id) {
                $sql_insertHistoryIn = "INSERT INTO user_history_in (docin_id, user_id, detail) VALUES ('$docin_id', '$user_id', '$detail')";
                mysqli_query($con, $sql_insertHistoryIn);
            }

            // ตรวจสอบว่าส่งเอกสารให้ผู้ใช้หรือไม่
            if (count($user_ids) > 0) {
                // อัปเดตสถานะของเอกสารเป็น "รับทราบและดำเนินการต่อ"
                $status_id = 2; // สมมติว่าสถานะ "รับทราบและดำเนินการต่อ" มี ID เป็น 2
                $sql_updateStatus = "UPDATE in_doc SET status_id = '$status_id' WHERE docin_id = '$docin_id'";
                mysqli_query($con, $sql_updateStatus);

                echo "<script>alert('ส่งเอกสารให้ผู้ใช้เรียบร้อยแล้ว'); window.location.href = 'in_document.php';</script>";
            }
        }
        ?>
        <!-- end status function -->
        <div class="result-container">
            <strong>การดำเนินการ</strong>
            <div class="resoult">
                <div class="for-status">
                    <?php
                    $docin_id = $_GET['id']; // รับค่า id จาก URL
                    $sql = "SELECT doc_status.status_name
                            FROM in_doc
                            JOIN doc_status ON in_doc.status_id = doc_status.status_id
                            WHERE in_doc.docin_id = '$docin_id'";
                    $result = mysqli_query($con, $sql);
                    if ($row = mysqli_fetch_assoc($result)) {
                        echo "สถานะปัจจุบัน<br> " . $row['status_name'];
                    }
                    ?>
                </div>
                <div class="send-to">
                    ส่งให้<br>
                    <?php
                    require '../conDB.php';

                    // ตรวจสอบการส่งเอกสารให้แผนก
                    $sql_department = "SELECT COUNT(DISTINCT de_id) AS num_departments 
                    FROM document_history_in
                    WHERE document_history_in.docin_id = '$docin_id'";

                    $result_department = mysqli_query($con, $sql_department);
                    $row_department = mysqli_fetch_assoc($result_department);

                    $num_departments = $row_department['num_departments'];

                    // แสดงจำนวนแผนกที่เอกสารถูกส่งไป
                    echo "ส่งให้แผนก: $num_departments แผนก <br>";

                    // ตรวจสอบการส่งเอกสารให้ผู้ใช้
                    $sql_user = "SELECT COUNT(DISTINCT user_id) AS num_users 
                    FROM user_history_in
                    WHERE user_history_in.docin_id = '$docin_id'";

                    $result_user = mysqli_query($con, $sql_user);
                    $row_user = mysqli_fetch_assoc($result_user);

                    $num_users = $row_user['num_users'];

                    // แสดงจำนวนผู้ใช้ที่เอกสารถูกส่งไป
                    echo "ส่งให้ผู้ใช้: $num_users คน";

                    ?>
                </div>
                <div class="receive">
                    รับเอกสารไปแล้ว....คน

                </div>
            </div>

        </div>



        <?php
        mysqli_close($con); // ย้ายการปิดการเชื่อมต่อฐานข้อมูลมาอยู่หลังจากส่วนแสดงผลการดำเนินการ
        ?>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>