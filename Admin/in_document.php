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
    <title>บันทึกข้อความเข้า</title>
    <link rel="stylesheet" href="../styles/indocument.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="top-title">
        บันทึกข้อความเข้า
    </div>
    <div class="add-button">
        <button type="button" class="btn btn-success" onclick="AddDocument()">เพิ่ม</button>
    </div>
    <div class="view-document">
        <table class="table table-bordered">
            <tr>
                <th width="80px">ลำดับที่</th>
                <th width="100px">ที่</th>
                <th width="100px">ลงวันที่</th>
                <th width="400px">เรื่อง</th>
                <th width="130px">ส่งมาจาก</th>
                <th width="100px">ถึง</th>
                <th width="120px">ไฟล์เอกสาร</th>
                <th width="170px">สถานนะ</th>
                <th width="100px">วันที่บันทึก</th>
                <th width="200px">Action</th>

            </tr>
            <?php
            require '../conDB.php';

            $sql = "SELECT in_doc.docin_id, in_doc.docin_number, in_doc.docin_date, in_doc.docin_title, in_doc.docin_sent_from, in_doc.docin_sent_to, in_doc.document_in, 
        in_doc.recording_date, document_type.type_name
        FROM in_doc
        LEFT JOIN document_type ON in_doc.type_id = document_type.type_id
        ORDER BY in_doc.docin_id DESC"; // เรียงจากใหม่สุดไปเก่าที่สุด

            $result = mysqli_query($con, $sql);
            $counter = mysqli_num_rows($result); // นับจำนวนแถวทั้งหมดในผลลัพธ์

            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>"; // ใช้ค่า $counter ที่ถูกนับลำดับแล้ว
                echo "<td>" . $row['docin_number'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($row['docin_date'])) . "</td>";
                echo "<td>" . $row['docin_title'] . "</td>";
                echo "<td>" . $row['docin_sent_from'] . "</td>";
                echo "<td>" . $row['docin_sent_to'] . "</td>";
                echo "<td><a href='" . $row['document_in'] . "' download>ดาวน์โหลด</a></td>";
                echo '<td><button type="button" class="btn btn-warning btn-sm" onclick="Status(' . $row['docin_id'] . ')">กำลังดำเนินการ</button></td>';
                echo "<td>" . date('d/m/Y H:i:s', strtotime($row['recording_date'])) . "</td>";
                echo '<td>';
                echo '<button type="button" class="btn btn-warning mr-2" onclick="Edit(' . $row['docin_id'] . ', \'' . $row['docin_number'] . '\', \'' . $row['docin_date'] . '\', \'' . $row['docin_title'] . '\', \'' . $row['docin_sent_from'] . '\', \'' . $row['docin_sent_to'] . '\')">แก้ไข</button>';
                echo '&nbsp;';
                echo '<button type="button" class="btn btn-danger" onclick="deleteIndoc(' . $row['docin_id'] . ')">ลบ</button> ';
                echo '</td>';
                echo "</tr>";

                $counter--; // ลดค่า $counter ลงทีละหนึ่งเพื่อแสดงลำดับที่ถูกต้อง
            }

            ?>
        </table>
    </div>

    <div class="add-document" id="AddDocument">
        <div class="col-md-12">
            <div class="title-add">
                เพิ่มเอกสารบันทึกข้อความ
            </div>
            <form action="in_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-3">
                    <label for="docin_number"><strong>ที่</strong></label>
                    <input type="text" id="docin_number" name="docin_number" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <strong>ชนิดเอกสาร</strong>
                    <select name="document_type" id="document_type" class="form-select">

                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM document_type ORDER BY type_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ในแท็ก <option>
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['type_id'] . "'>" . $row['type_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>
                    </select>
                </div>
                <div class="form-group my-3">
                    <label for="docin_date"><strong>ลงวันที่</strong></label>
                    <input type="date" id="docin_date_add" name="docin_date" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docin_title"><strong>เรื่อง</strong></label>
                    <input type="text" id="docin_title" name="docin_title" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docin_sent_from"><strong>ส่งมาจาก</strong></label>
                    <input type="text" id="docin_sent_from" name="docin_sent_from" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docin_sent_to"><strong>ถึง</strong></label>
                    <input type="text" id="docin_sent_to" name="docin_sent_to" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="document_in"><strong>ไฟล์</strong></label>
                    <input type="file" id="document_in" name="document_in" accept="document_in/" class="form-control" placeholder="" value="">
                </div>
                <div class="from-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="SubmitAdd" id="SubmitAdd">Submit</button>
                    <button onclick="hideEdit()" id="close_add" class="mt-3 btn btn-danger">Close</button>
                </div>
        </div>
    </div>
    <script>
        function AddDocument() {
            var popup = document.getElementById("AddDocument");
            popup.style.display = "block";
        }

        function hideAdd() {
            var popup = document.getElementById("AddDocument");
            popup.style.display = "none";
        }
    </script>

    <?php
    require '../conDB.php';
    if (isset($_POST['SubmitAdd'])) {
        // รับข้อมูลจากฟอร์ม
        $docin_number = $_POST['docin_number'];
        $type_id = $_POST['document_type'];
        $docin_date = $_POST['docin_date'];
        $docin_title = $_POST['docin_title'];
        $docin_sent_from = $_POST['docin_sent_from'];
        $docin_sent_to = $_POST['docin_sent_to'];

        // การอัปโหลดไฟล์
        $target_dir = "../document_in/"; // ปรับเส้นทางตามที่ต้องการ

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "docin_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }


        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $document_in = $target_dir . createNewFileName($_FILES["document_in"]["name"]);
        move_uploaded_file($_FILES["document_in"]["tmp_name"], $document_in);



        // เพิ่มข้อมูลลงในฐานข้อมูล

        $sql = "INSERT INTO in_doc (docin_id, type_id, docin_number, docin_date, docin_title, 
                docin_sent_from, docin_sent_to, document_in, recording_date)
                VALUES (NULL, '$type_id', '$docin_number', '$docin_date', '$docin_title',
                '$docin_sent_from', '$docin_sent_to', '$document_in', NOW())";

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    }
    $con->close();
    ?>


    <div class="update-document" id="UpdateDocument">
        <div class="col-md-12">
            <div class="title-update">
                แก้ไขเอกสารบันทึกข้อความ
            </div>
            <form action="in_document.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_docin_id" id="update_docin_id" placeholder="" value="">
                <div class="form-group my-3">
                    <label for="update_docin_number"><strong>ที่</strong></label>
                    <input type="text" id="update_docin_number" name="update_docin_number" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <strong>ชนิดเอกสาร</strong>
                    <select name="update_document_type" id="update_document_type" class="form-select">

                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM document_type ORDER BY type_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ในแท็ก <option>
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['type_id'] . "'>" . $row['type_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>
                    </select>
                </div>
                <div class="form-group my-3">
                    <label for="update_docin_date"><strong>วันที่</strong></label>
                    <input type="date" id="update_docin_date" name="update_docin_date" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docin_title"><strong>เรื่อง</strong></label>
                    <input type="text" id="update_docin_title" name="update_docin_title" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docin_sent_from"><strong>ส่งมาจาก</strong></label>
                    <input type="text" id="update_docin_sent_from" name="update_docin_sent_from" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docin_sent_to"><strong>ถึง</strong></label>
                    <input type="text" id="update_docin_sent_to" name="update_docin_sent_to" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_document_in"><strong>ไฟล์</strong></label>
                    <input type="file" id="update_document_in" name="update_document_in" class="form-control" placeholder="" value="">
                </div>
                <div class="from-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="SubmitUpdate" id="SubmitUpdate">Update</button>
                    <button onclick="hideUpdate()" id="close_update" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function Edit(docin_id, docin_number, docin_date, docin_title, docin_sent_from, docin_sent_to) {
            // นำข้อมูลที่ต้องการแก้ไขมาใส่ใน input fields ของหน้าต่างแก้ไข
            document.getElementById("update_docin_id").value = docin_id;
            document.getElementById("update_docin_number").value = docin_number;
            document.getElementById("update_docin_date").value = docin_date;
            document.getElementById("update_docin_title").value = docin_title;
            document.getElementById("update_docin_sent_from").value = docin_sent_from;
            document.getElementById("update_docin_sent_to").value = docin_sent_to;

            // แสดงหน้าต่างแก้ไข
            var updatePopup = document.getElementById("UpdateDocument");
            updatePopup.style.display = "block";
        }

        function hideUpdate() {
            var updatePopup = document.getElementById("UpdateDocument");
            updatePopup.style.display = "none";
        }
    </script>
    <?php
    require '../conDB.php';

    if (isset($_POST['SubmitUpdate'])) {
        // รับข้อมูลที่ต้องการอัปเดต
        $update_docin_id = $_POST['update_docin_id'];
        $update_docin_number = $_POST['update_docin_number'];
        $update_docin_date = $_POST['update_docin_date'];
        $update_docin_title = $_POST['update_docin_title'];
        $update_docin_sent_from = $_POST['update_docin_sent_from'];
        $update_docin_sent_to = $_POST['update_docin_sent_to'];



        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "docin_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์ใหม่
            return $newFileName;
        }

        // การอัปโหลดไฟล์ใหม่
        $target_dir = "../document_in/"; // เปลี่ยนตามตำแหน่งที่คุณต้องการ
        $newFileName = createNewFileName($_FILES["update_document_in"]["name"]); // สร้างชื่อไฟล์ใหม่
        $document_in = $target_dir . $newFileName; // เตรียมตำแหน่งของไฟล์ใหม่

        move_uploaded_file($_FILES["update_document_in"]["tmp_name"], $document_in); // ย้ายไฟล์ไปยังตำแหน่งใหม่


        // อัปเดตข้อมูลในฐานข้อมูล
        $sql_update = "UPDATE in_doc SET 
        docin_number = ?, 
        docin_date = ?, 
        docin_title = ?, 
        docin_sent_from = ?, 
        docin_sent_to = ?, 
        document_in = ?, 
        recording_date = NOW() 
        WHERE docin_id = ?";
        $stmt = $con->prepare($sql_update);
        $stmt->bind_param("ssssssi", $update_docin_number, $update_docin_date, $update_docin_title, $update_docin_sent_from, $update_docin_sent_to, $document_in, $update_docin_id);

        // ประมวลผลคำสั่ง SQL
        if ($stmt->execute()) {
            // หากอัปเดตข้อมูลสำเร็จ ให้กลับไปยังหน้าเว็บไซต์ที่มีรายการเอกสารอยู่
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        } else {
            // หากมีข้อผิดพลาดในการอัปเดต แสดงข้อความข้อผิดพลาด
            echo "Error updating record: " . $con->error;
        }
    }

    ?>

    <!-- end edit function -->

    <!-- delete function -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function deleteIndoc(docin_id) {
            if (confirm("คุณต้องการลบรายการนี้ใช่หรือไม่?")) {
                // ส่งคำร้องขอ AJAX ไปยังไฟล์ PHP เพื่อลบข้อมูล
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "docin_delete.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        // เมื่อลบข้อมูลเสร็จสิ้น รีโหลดหน้าเว็บ
                        window.location.reload();
                    }
                };
                xhr.send("docin_id=" + docin_id);
            }
        }
    </script>
    <!-- end delete function -->


    <div class="status" id="Status">
        <button type="button" class="close" aria-label="Close" onclick="CloseStatus()">X</button>
        <div class="title-status">สถานะ</div>
        <form action="in_document.php" enctype="multipart/form-data">
            <select class="form-select mt-3" id="SelectStatus" onchange="Status()">
                <option selected>กำลังดำเนินการ</option>
                <option value="1">รับทราบ</option>
                <option value="2">รับทราบ : ดำเนินการต่อ</option>
            </select>
            <select class="form-select mt-3" id="SelectSendto" onchange="StatusSendto()" style="display: none;">
                <option selected>ต้องการส่งเอกสารให้</option>
                <option value="1">รายบุคคล</option>
                <option value="2">แผนก</option>
            </select>
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

                // ปิดการเชื่อมต่อฐานข้อมูล
                mysqli_close($con);
                ?>
            </div>

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

                // ปิดการเชื่อมต่อฐานข้อมูล
                mysqli_close($con);
                ?>
            </div>

            <div class="form-group mt-2">
                <label for="additionalDetails">รายละเอียดเพิ่มเติม</label>
                <textarea class="form-control" id="detail" name="detail" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-3" name="AddSend" id="Addsend">ดำเนินการต่อ</button>
        </form>
    </div>

    <script>
        function Status(docin_id) {
            var status = document.getElementById("Status");
            var selectStatus = document.getElementById("SelectStatus");
            var selectSendto = document.getElementById("SelectSendto");
            var checkboxUser = document.getElementById("UserCheckbox");
            var checkboxDepartment = document.getElementById("DepartmentCheckbox");

            // เมื่อเลือก status รับทราบ : ดำเนินการต่อ
            if (selectStatus.value === "2") {
                // แสดงเลือกว่าจะส่งให้แผนกหรือรายบุคคล
                selectSendto.style.display = "block";

                // เรียกใช้ฟังก์ชันเพื่อแสดงส่วนที่เหมาะสมตามค่าของ selectSendto ที่เปลี่ยนแปลง
                StatusSendto();
            } else {
                selectSendto.style.display = "none";
                checkboxDepartment.style.display = "none";
                checkboxUser.style.display = "none";
            }

            status.style.display = "block";
        }

        function CloseStatus() {
            var status = document.getElementById("Status");

            status.style.display = "none";
        }

        function StatusSendto() {
            var selectSendto = document.getElementById("SelectSendto");
            var checkboxUser = document.getElementById("UserCheckbox");
            var checkboxDepartment = document.getElementById("DepartmentCheckbox");

            if (selectSendto.value === "1") {
                checkboxUser.style.display = "block";
                checkboxDepartment.style.display = "none";
            } else if (selectSendto.value === "2") {
                checkboxDepartment.style.display = "block";
                checkboxUser.style.display = "none";
            } else {
                checkboxDepartment.style.display = "none";
                checkboxUser.style.display = "none";
            }
        }
    </script>

    <?php
    require '../conDB.php';

    if (isset($_POST['AddSend'])) {

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    }
    $con->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>