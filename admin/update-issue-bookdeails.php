<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['return'])) {
        $rid = intval($_GET['rid']);
        $fine = $_POST['fine'];
        $rstatus = 1;

        // Lấy BookId từ bản ghi đang trả
        $getBookSql = "SELECT BookId FROM tblissuedbookdetails WHERE id = :rid";
        $getBookQuery = $dbh->prepare($getBookSql);
        $getBookQuery->bindParam(':rid', $rid, PDO::PARAM_INT);
        $getBookQuery->execute();
        $bookResult = $getBookQuery->fetch(PDO::FETCH_OBJ);
        $bookid = $bookResult->BookId;

        // Cập nhật thông tin trả sách
        $sql = "UPDATE tblissuedbookdetails SET fine = :fine, RetrunStatus = :rstatus, ReturnDate = NOW() WHERE id = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_INT);
        $query->bindParam(':fine', $fine, PDO::PARAM_STR);
        $query->bindParam(':rstatus', $rstatus, PDO::PARAM_INT);
        $query->execute();

        // Cộng lại số lượng sách trong bảng tblbooks
        $updateQtySql = "UPDATE tblbooks SET Quantity = Quantity + 1 WHERE id = :bookid";
        $updateQtyQuery = $dbh->prepare($updateQtySql);
        $updateQtyQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $updateQtyQuery->execute();

        $_SESSION['msg'] = "Book Returned successfully";
        header('location:manage-issued-books.php');
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issued Book Details</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script>
        // Function for getting student name
        function getstudent() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_student.php",
                data: 'studentid=' + $("#studentid").val(),
                type: "POST",
                success: function(data) {
                    $("#get_student_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }

        // Function for book details
        function getbook() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_book.php",
                data: 'bookid=' + $("#bookid").val(),
                type: "POST",
                success: function(data) {
                    $("#get_book_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
    </script>
    <style type="text/css">
        .others {
            color: red;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Issued Book Details</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Issued Book Details
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <?php 
                                $rid = intval($_GET['rid']);
                                // Thêm bookimage vào truy vấn SQL
                                $sql = "SELECT tblstudents.FullName, tblbooks.BookName, tblbooks.bookimage, tblbooks.ISBNNumber, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid, tblissuedbookdetails.fine, tblissuedbookdetails.RetrunStatus 
                                        FROM tblissuedbookdetails 
                                        JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                        JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                                        WHERE tblissuedbookdetails.id = :rid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':rid', $rid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { ?>                                      
                                        <div class="form-group">
                                            <label>Tên Sinh viên:</label>
                                            <?php echo htmlentities($result->FullName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Sách:</label>
                                            <?php echo htmlentities($result->BookName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Hình ảnh sách:</label><br/>
                                            <?php if ($result->bookimage) { ?>
                                                <img src="assets/img/<?php echo htmlentities($result->bookimage); ?>" width="100" />
                                            <?php } else { ?>
                                                No Image
                                            <?php } ?>
                                        </div>

                                        <div class="form-group">
                                            <label>ISBN:</label>
                                            <?php echo htmlentities($result->ISBNNumber); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Ngày mượn:</label>
                                            <?php echo htmlentities($result->IssuesDate); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Ngày trả:</label>
                                            <?php if ($result->ReturnDate == "") {
                                                echo htmlentities("Not Return Yet");
                                            } else {
                                                echo htmlentities($result->ReturnDate);
                                            } ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Phạt (VND):</label>
                                            <?php if ($result->fine == "") { ?>
                                                <input class="form-control" type="text" name="fine" id="fine" required />
                                            <?php } else {
                                                echo htmlentities($result->fine);
                                            } ?>
                                        </div>

                                        <?php if ($result->RetrunStatus == 0) { ?>
                                            <button type="submit" name="return" id="submit" class="btn btn-info">Return Book</button>
                                        <?php } ?>
                                <?php }
                                } ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>