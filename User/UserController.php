<?php
include './conDB.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: ../login.php');
    exit(); // Always exit after a redirect
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserController</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/user.css">
</head>

<body>
    <?php require_once 'nav_user.php'; ?>

    <div class="container">
        <div class="button-container">
            <a href="UserController.php"><button type="button" class="new-doc-button">เอกสารใหม่</button></a>
            <a href="document.php"><button type="button">คลังเอกสาร</button></a>
        </div>
    </div>
    <p class="title-history">เอกสารที่ยังไม่ได้รับ</p>
    <div class="history">
        <p class="in-document">บันทึกข้อความเข้า</p>
        <table class="table table-bordered">
            <tr>
                <th width="80px">ลำดับที่</th>
                <th width="100px">ที่</th>
                <th width="100px">ลงวันที่</th>
                <th width="400px">เรื่อง</th>
                <th width="130px">ส่งมาจาก</th>
                <th width="100px">ถึง</th>
                <th width="120px">ไฟล์เอกสาร</th>
                <th width="170px">สถานะ</th>
                <th width="100px">รายละเอียด</th>
                <th width="200px">Action</th>
            </tr>
            <?php
            require '../conDB.php';

            $sql = "SELECT in_doc.docin_id, in_doc.docin_number, in_doc.docin_date, in_doc.docin_title, in_doc.docin_sent_from, in_doc.docin_sent_to, in_doc.document_in, 
            in_doc.recording_date, document_type.type_name, doc_status.status_name, user_history_in.detail, received.received_name, user_history_in.user_history_id
            FROM in_doc
            INNER JOIN user_history_in ON in_doc.docin_id = user_history_in.docin_id
            LEFT JOIN document_type ON in_doc.type_id = document_type.type_id
            LEFT JOIN doc_status ON in_doc.status_id = doc_status.status_id
            LEFT JOIN received ON user_history_in.received_id = received.received_id
            WHERE user_history_in.user_id = ? AND user_history_in.received_id = 1
            ORDER BY in_doc.docin_id DESC";


            $stmt = $con->prepare($sql);
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            $counter = $result->num_rows; // Assuming $counter contains the total number of rows

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td>" . $row['docin_number'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($row['docin_date'])) . "</td>";
                echo "<td>" . $row['docin_title'] . "</td>";
                echo "<td>" . $row['docin_sent_from'] . "</td>";
                echo "<td>" . $row['docin_sent_to'] . "</td>";
                echo "<td><a href='" . $row['document_in'] . "' download>ดาวน์โหลด</a></td>";
                echo "<td><button type='button' class='btn btn-warning btn-sm'>" . $row['received_name'] . "</button></td>";
                echo "<td>" . $row['detail'] . "</td>";
                echo '<td>';
                echo '<button type="button" class="btn btn-success mr-2" onclick="receiveDocument(\'' . $row['user_history_id'] . '\')">รับเอกสาร</button>';
                echo '</td>';
                echo "</tr>";

                $counter--; // Decrement the counter to display the correct order

            }
            ?>
        </table>
        <div class="submit-documentin" id="submitDocin">
            <form action="UserController.php" method="post">
                <input type="hidden" name="user_history_id" id="user_history_id">
                <button type="submit" class="btn btn-success" name="submit" id="submit">รับเอกสาร</button>
                <button type="button" class="btn btn-danger" id="close" onclick="close()">ปิด</button>
            </form>
        </div>
        <script>
            function receiveDocument(user_history_id) {
                var docin = document.getElementById('submitDocin');
                document.getElementById("user_history_id").value = user_history_id;

                docin.style.display = "block";
            }

            function close() {
                var docin = document.getElementById('submitDocin');
                docin.style.display = "none";
            }
        </script>

        <?php
        require '../conDB.php';

        if (isset($_POST['submit'])) {
            // Validate and sanitize the input
            $history_docin = mysqli_real_escape_string($con, $_POST['user_history_id']);
            $received_date = date('Y-m-d');
        
            // Prepare the SQL statement using Prepared Statements
            $updateQuery = "UPDATE user_history_in SET received_id = '2', received_time = ? WHERE user_history_id = ?";
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $received_date, $history_docin);
        
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>window.location.href = window.location.href;</script>";
            } else {
                echo "Error: " . mysqli_error($con) . "<br>";
                echo "<script>alert('ผิดพลาด');</script>";
            }
        
            mysqli_stmt_close($stmt);
        }        
        ?>

        <p class="ex-document">เอกสารภายนอก</p>
    </div>









    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>