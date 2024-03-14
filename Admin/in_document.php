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
                <th width="100px">สถานนะ</th>
                <th width="100px">วันที่บันทึก</th>
                <th width="300px">Action</th>

            </tr>
            <?php
            require '../conDB.php';

            $sql = "SELECT in_doc.docin_id, in_doc.docin_number, in_doc.docin_date, in_doc.docin_title, in_doc.docin_sent_from, in_doc.docin_sent_to, in_doc.document_in, 
                    in_doc.recording_date, document_type.type_name
                    FROM in_doc
                    LEFT JOIN document_type ON in_doc.type_id = document_type.type_id
                    ORDER BY in_doc.docin_id";

            $result = mysqli_query($con, $sql);
            $counter = 1;
            // สมมติว่าคอลัมน์ 'status' เก็บข้อมูลสถานะ
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td>" . $row['docin_number'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($row['docin_date'])) . "</td>";
                echo "<td>" . $row['docin_title'] . "</td>";
                echo "<td>" . $row['docin_sent_from'] . "</td>";
                echo "<td>" . $row['docin_sent_to'] . "</td>";
                echo "<td><a href='" . $row['document_in'] . "' download>ดาวน์โหลด</a></td>";
                echo '<td><button type="button" class="btn btn-secondary">สถานะ</button></td>';
                echo "<td>" . date('d/m/Y H:i:s', strtotime($row['recording_date'])) . "</td>";
                echo '<td>';
                echo '<button type="button" class="btn btn-warning mr-2" onclick="Edit(' . $row['docin_id'] . ', \'' . $row['docin_number'] . '\', \'' . $row['docin_date'] . '\', \'' . $row['docin_title'] . '\', \'' . $row['docin_sent_from'] . '\', \'' . $row['docin_sent_to'] . '\')">แก้ไข</button>';
                echo '&nbsp;';
                echo '<button type="button" class="btn btn-danger">ลบ</button>';
                echo '</td>';
                echo "</tr>";

                $counter++;
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
                            // ตรวจสอบว่าเป็น type id ที่ต้องการให้เป็นตัวเลือกแรกหรือไม่
                            $selected = ($row['type_id'] == 2) ? 'selected' : '';
                            echo "<option value='" . $row['type_id'] . "' $selected>" . $row['type_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>
                    </select>
                </div>
                <div class="form-group my-3">
                    <label for="docin_date"><strong>ลงวันที่</strong></label>
                    <input type="date" id="docin_date" name="docin_date" class="form-control" placeholder="" value="">
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
                    <input type="file" id="document_in" name="document_in" accept="document_ex/" class="form-control" placeholder="" value="">
                </div>
                <div class="from-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="submit_add" id="submit_add">Submit</button>
                    <button onclick="hideAdd()" id="close_add" class="mt-3 btn btn-danger">Close</button>
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
    if (isset($_POST['submit_add'])) {
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
            $newFileName = "docex_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }


        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $document_in = $target_dir . createNewFileName($_FILES["document_in"]["name"]);
        move_uploaded_file($_FILES["document_in"]["tmp_name"], $document_in);
        // ทำซ้ำสำหรับ car_picture2, car_picture3, และ car_picture4 ...


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
    <!-- end add function -->

    <!-- function edit -->

    <div class="edit-document" id="EditDocument">
        <div class="col-md-12">
            <div class="title-edit">
                แก้ไขเอกสารบันทึกข้อความ
            </div>
            <form action="in_document.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="docin_id" id="edit_docin_id" value="">
                <div class="form-group my-3">
                    <label for="docex_number"><strong>ที่</strong></label>
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
                    <input type="date" id="docin_date" name="docin_date" class="form-control" placeholder="" value="">
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
                    <button type="submit" class="mt-3 btn btn-primary" name="SubmitEdit" id="submit_edit">Submit</button>
                    <button onclick="hideEdit()" id="close_add" class="mt-3 btn btn-danger">Close</button>
                </div>
        </div>
    </div>

    <!-- script -->
    <script>
        function Edit(docin_id, docin_number, docin_date, docin_title, docin_sent_from, docin_sent_to) {
            var edit = document.getElementById("EditDocument");
            var InputDocinID = edit.querySelector("input[name='docin_id']");
            var InputDocinNumber = edit.querySelector("input[name='docin_number']");
            var InputDocinDate = edit.querySelector("input[name='docin_date']");
            var InputDocinTitle = edit.querySelector("input[name='docin_title']");
            var InputDocinSentFrom = edit.querySelector("input[name='docin_sent_from']");
            var InputDocinSentTo = edit.querySelector("input[name='docin_sent_to']");


            InputDocinID.value = docin_id;
            InputDocinNumber.value = docin_number;
            InputDocinDate.value = docin_date;
            InputDocinTitle.value = docin_title;
            InputDocinSentFrom.value = docin_sent_from;
            InputDocinSentTo.value = docin_sent_to;

            // Display the edit popup
            edit.style.display = "block";
        }

        function hideEdit() {
            var edit = document.getElementById("EditDocument");
            edit.style.display = "none";
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>