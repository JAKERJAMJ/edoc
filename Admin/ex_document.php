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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หนังสือเข้าภายนอก</title>
    <link rel="stylesheet" href="../styles/exdocument.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="top-title">
        หนังสือเข้าภายนอก
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

            $sql = "SELECT ex_doc.docex_id, ex_doc.docex_number, ex_doc.docex_date, ex_doc.docex_title, ex_doc.docex_sent_from, ex_doc.docex_sent_to, ex_doc.document_ex, 
        ex_doc.recording_date, document_type.type_name, doc_status.status_name
        FROM ex_doc
        LEFT JOIN document_type ON ex_doc.type_id = document_type.type_id
        LEFT JOIN doc_status ON ex_doc.status_id = doc_status.status_id
        ORDER BY ex_doc.docex_id DESC"; // เรียงจากใหม่สุดไปเก่าที่สุด

            $result = mysqli_query($con, $sql);
            $counter = mysqli_num_rows($result); // นับจำนวนแถวทั้งหมดในผลลัพธ์

            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>"; // ใช้ค่า $counter ที่ถูกนับลำดับแล้ว
                echo "<td>" . $row['docex_number'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($row['docex_date'])) . "</td>";
                echo "<td>" . $row['docex_title'] . "</td>";
                echo "<td>" . $row['docex_sent_from'] . "</td>";
                echo "<td>" . $row['docex_sent_to'] . "</td>";
                echo "<td><a href='" . $row['document_ex'] . "' download>ดาวน์โหลด</a></td>";
                echo "<td><a href='exdoc_status.php?id=".$row['docex_id']."'><button type='button' class='btn btn-warning btn-sm'>" . $row['status_name'] . "</button></td>";
                echo "<td>" . date('d/m/Y H:i:s', strtotime($row['recording_date'])) . "</td>";
                echo '<td>';
                echo '<button type="button" class="btn btn-warning mr-2" onclick="Edit(' . $row['docex_id'] . ', \'' . $row['docex_number'] . '\', \'' . $row['docex_date'] . '\', \'' . $row['docex_title'] . '\', \'' . $row['docex_sent_from'] . '\', \'' . $row['docex_sent_to'] . '\', \'' . $row['status_id'] . '\')">แก้ไข</button>';
                echo '&nbsp;';
                echo '<button type="button" class="btn btn-danger" onclick="deleteIndoc(' . $row['docex_id'] . ')">ลบ</button> ';
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
                เพิ่มหนังสือเข้าภายนอก
            </div>
            <form action="ex_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-3">
                    <label for="docex_number"><strong>ที่</strong></label>
                    <input type="text" id="docex_number" name="docex_number" class="form-control" placeholder="" value="">
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
                    <label for="docex_date"><strong>ลงวันที่</strong></label>
                    <input type="date" id="docex_date_add" name="docex_date" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docex_title"><strong>เรื่อง</strong></label>
                    <input type="text" id="docex_title" name="docex_title" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docex_sent_from"><strong>ส่งมาจาก</strong></label>
                    <input type="text" id="docex_sent_from" name="docex_sent_from" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="docex_sent_to"><strong>ถึง</strong></label>
                    <input type="text" id="docex_sent_to" name="docex_sent_to" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="document_ex"><strong>ไฟล์</strong></label>
                    <input type="file" id="document_ex" name="document_ex" accept="document_ex/" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <strong>สถานะ</strong>
                    <select name="doc_status" id="doc_status" class="form-select"ex
                        <?php
                        require '../conDB.php';

                        // ดึงข้อมูลตำแหน่งจากฐานข้อมูล
                        $sql = "SELECT * FROM doc_status ORDER BY status_id";
                        $result = mysqli_query($con, $sql);

                        // นำข้อมูลมาใส่ในแท็ก <option>
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['status_id'] . "'>" . $row['status_name'] . "</option>";
                        }

                        // ปิดการเชื่อมต่อฐานข้อมูล
                        mysqli_close($con);
                        ?>
                    </select>
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
        $docex_number = $_POST['docex_number'];
        $type_id = $_POST['document_type'];
        $docex_date = $_POST['docex_date'];
        $docex_title = $_POST['docex_title'];
        $docex_sent_from = $_POST['docex_sent_from'];
        $docex_sent_to = $_POST['docex_sent_to'];
        $status_id = $_POST['doc_status'];

        // การอัปโหลดไฟล์
        $target_dir = "../document_ex/"; // ปรับเส้นทางตามที่ต้องการ

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "docex_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }


        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $document_ex = $target_dir . createNewFileName($_FILES["document_ex"]["name"]);
        move_uploaded_file($_FILES["document_ex"]["tmp_name"], $document_ex);



        // เพิ่มข้อมูลลงในฐานข้อมูล

        $sql = "INSERT INTO ex_doc (docex_id, type_id, docex_number, docex_date, docex_title, 
                docex_sent_from, docex_sent_to, document_ex, recording_date, status_id)
                VALUES (NULL, '$type_id', '$docex_number', '$docex_date', '$docex_title',
                '$docex_sent_from', '$docex_sent_to', '$document_ex', NOW(), '$status_id')";

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
            <form action="ex_document.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_docex_id" id="update_docex_id" placeholder="" value="">
                <div class="form-group my-3">
                    <label for="update_docex_number"><strong>ที่</strong></label>
                    <input type="text" id="update_docex_number" name="update_docex_number" class="form-control" placeholder="" value="">
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
                    <label for="update_docex_date"><strong>วันที่</strong></label>
                    <input type="date" id="update_docex_date" name="update_docex_date" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docex_title"><strong>เรื่อง</strong></label>
                    <input type="text" id="update_docex_title" name="update_docex_title" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docex_sent_from"><strong>ส่งมาจาก</strong></label>
                    <input type="text" id="update_docex_sent_from" name="update_docex_sent_from" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_docex_sent_to"><strong>ถึง</strong></label>
                    <input type="text" id="update_docex_sent_to" name="update_docex_sent_to" class="form-control" placeholder="" value="">
                </div>
                <div class="form-group my-3">
                    <label for="update_document_ex"><strong>ไฟล์</strong></label>
                    <input type="file" id="update_document_ex" name="update_document_ex" class="form-control" placeholder="" value="">
                </div>
                <div class="from-group my-3">
                    <button type="submit" class="mt-3 btn btn-primary" name="SubmitUpdate" id="SubmitUpdate">Update</button>
                    <button onclick="hideUpdate()" id="close_update" class="mt-3 btn btn-danger">Close</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function Edit(docex_id, docex_number, docex_date, docex_title, docex_sent_from, docex_sent_to) {
            // นำข้อมูลที่ต้องการแก้ไขมาใส่ใน input fields ของหน้าต่างแก้ไข
            document.getElementById("update_docex_id").value = docex_id;
            document.getElementById("update_docex_number").value = docex_number;
            document.getElementById("update_docex_date").value = docex_date;
            document.getElementById("update_docex_title").value = docex_title;
            document.getElementById("update_docex_sent_from").value = docex_sent_from;
            document.getElementById("update_docex_sent_to").value = docex_sent_to;

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
        $update_docex_id = $_POST['update_docex_id'];
        $update_docex_number = $_POST['update_docex_number'];
        $update_docex_date = $_POST['update_docex_date'];
        $update_docex_title = $_POST['update_docex_title'];
        $update_docex_sent_from = $_POST['update_docex_sent_from'];
        $update_docex_sent_to = $_POST['update_docex_sent_to'];



        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "docex_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์ใหม่
            return $newFileName;
        }

        // การอัปโหลดไฟล์ใหม่
        $target_dir = "../document_ex/"; // เปลี่ยนตามตำแหน่งที่คุณต้องการ
        $newFileName = createNewFileName($_FILES["update_document_ex"]["name"]); // สร้างชื่อไฟล์ใหม่
        $document_in = $target_dir . $newFileName; // เตรียมตำแหน่งของไฟล์ใหม่

        move_uploaded_file($_FILES["update_document_ex"]["tmp_name"], $document_ex); // ย้ายไฟล์ไปยังตำแหน่งใหม่


        // อัปเดตข้อมูลในฐานข้อมูล
        $sql_update = "UPDATE ex_doc SET 
        docex_number = ?, 
        docex_date = ?, 
        docex_title = ?, 
        docex_sent_from = ?, 
        docex_sent_to = ?, 
        document_ex = ?, 
        recording_date = NOW() 
        WHERE docex_id = ?";
        $stmt = $con->prepare($sql_update);
        $stmt->bind_param("ssssssi", $update_docex_number, $update_docex_date, $update_docex_title, $update_docex_sent_from, $update_docex_sent_to, $document_ex, $update_docex_id);

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
        function deleteIndoc(docex_id) {
            if (confirm("คุณต้องการลบรายการนี้ใช่หรือไม่?")) {
                // ส่งคำร้องขอ AJAX ไปยังไฟล์ PHP เพื่อลบข้อมูล
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "docex_delete.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        // เมื่อลบข้อมูลเสร็จสิ้น รีโหลดหน้าเว็บ
                        window.location.reload();
                    }
                };
                xhr.send("docex_id=" + docex_id);
            }
        }
    </script>
    <!-- end delete function -->




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>