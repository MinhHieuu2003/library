<?php 
require_once("includes/config.php");

if (!empty($_POST["bookid"])) {
    $bookid = $_POST["bookid"];
    
    // Truy vấn để lấy BookName, id, số lượng và bookimage từ tblbooks
    $sql = "SELECT BookName, id, bookimage FROM tblbooks WHERE ISBNNumber = :bookid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            // Trả về HTML cho dropdown
            echo '<option value="' . htmlentities($result->id) . '">' . htmlentities($result->BookName) . '</option>';
            // Thêm thông tin hình ảnh dưới dạng data attribute
            echo '<span class="book-image" style="display:none;" data-image="' . htmlentities($result->bookimage) . '"></span>';
        }
        echo "<script>$('#submit').prop('disabled', false);</script>";
    } else {
        echo '<option class="others">Invalid ISBN</option>';
        echo "<script>$('#submit').prop('disabled', true);</script>";
    }
}
?>