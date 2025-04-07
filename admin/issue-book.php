<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['issue'])) {
        $studentid = strtoupper($_POST['studentid']);
        $bookid = $_POST['bookdetails'];

        $checkQtySql = "SELECT Quantity FROM tblbooks WHERE id = :bookid";
        $checkQtyQuery = $dbh->prepare($checkQtySql);
        $checkQtyQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $checkQtyQuery->execute();
        $book = $checkQtyQuery->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['Quantity'] > 0) {
            $sql = "INSERT INTO tblissuedbookdetails(StudentID, BookId) VALUES(:studentid, :bookid)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $updateQtySql = "UPDATE tblbooks SET Quantity = Quantity - 1 WHERE id = :bookid AND Quantity > 0";
                $updateQtyQuery = $dbh->prepare($updateQtySql);
                $updateQtyQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                $updateQtyQuery->execute();

                $_SESSION['msg'] = "Book issued successfully";
                header('location:manage-issued-books.php');
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again";
                header('location:manage-issued-books.php');
            }
        } else {
            $_SESSION['error'] = "Sorry, no stock available for this book";
            header('location:manage-issued-books.php');
        }
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issue a new Book</title>
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
                error: function() {
                    $("#loaderIcon").hide();
                }
            });
        }

        // Function for book details including image
        function getbook() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_book.php",
                data: 'bookid=' + $("#bookid").val(),
                type: "POST",
                success: function(data) {
                    $("#get_book_name").html(data); // Cập nhật dropdown với HTML trả về

                    // Lấy thông tin hình ảnh từ span ẩn
                    var bookImage = $("#get_book_name").find('.book-image').data('image');
                    if (bookImage) {
                        $("#get_book_image").html('<img src="assets/img/' + bookImage + '" width="100" />');
                    } else {
                        $("#get_book_image").html(''); // Xóa hình ảnh nếu không có
                    }

                    $("#loaderIcon").hide();
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error: ", status, error);
                    $("#get_book_name").html('<option class="others">Error loading book</option>');
                    $("#get_book_image").html('');
                    $("#loaderIcon").hide();
                }
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
                    <h4 class="header-line">Issue a New Book</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">Issue a New Book</div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Mã Sinh viên<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="studentid" id="studentid" onBlur="getstudent()" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <span id="get_student_name" style="font-size:16px;"></span>
                                </div>

                                <div class="form-group">
                                    <label>ISBN hoặc Tiêu đề sách<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookid" id="bookid" onBlur="getbook()" required="required" />
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="bookdetails" id="get_book_name" readonly></select>
                                </div>

                                <div class="form-group">
                                    <label>Ảnh sách:</label>
                                    <div id="get_book_image"></div> <!-- Khu vực hiển thị hình ảnh -->
                                </div>

                                <button type="submit" name="issue" id="submit" class="btn btn-info" disabled>Issue Book</button>
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