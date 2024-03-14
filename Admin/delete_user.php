<?php
    require '../conDB.php';

    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        // Use prepared statement to prevent SQL injection
        $sql = $con->prepare("DELETE FROM user WHERE user_id = ?");
        $sql->bind_param("i", $user_id);

        if ($sql->execute()) {
            echo 'User deleted successfully.';
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
        $con->close();
    }
?>
