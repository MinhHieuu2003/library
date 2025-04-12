<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['add'])) {
        $bookname = $_POST['bookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
    
        // ==============================
        // XỬ LÝ FILE ẢNH BÌA
        // ==============================
        $bookimage = $_FILES["bookimage"]["name"];
        $image_tmp = $_FILES["bookimage"]["tmp_name"];
        $image_ext = strtolower(pathinfo($bookimage, PATHINFO_EXTENSION));
    
        $uploadOk = 1;
    
        // Kiểm tra định dạng ảnh
        $check = getimagesize($image_tmp);
        if ($check === false) {
            $_SESSION['error'] = "File không phải là ảnh.";
            $uploadOk = 0;
        }
    
        if ($_FILES["bookimage"]["size"] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File ảnh quá lớn (yêu cầu < 5MB).";
            $uploadOk = 0;
        }
    
        if (!in_array($image_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Chỉ cho phép JPG, JPEG, PNG, GIF.";
            $uploadOk = 0;
        }
    
        // Đổi tên ảnh thành chuỗi unique
        $bookimage_newname = uniqid('img_', true) . '.' . $image_ext;
        $target_dir_img = "assets/img/";
        $target_file_img = $target_dir_img . $bookimage_newname;
    
        // ==============================
        // XỬ LÝ FILE SÁCH PDF
        // ==============================
        $bookfile = $_FILES["bookfile"]["name"];
        $bookfile_tmp = $_FILES["bookfile"]["tmp_name"];
        $bookfile_ext = strtolower(pathinfo($bookfile, PATHINFO_EXTENSION));
    
        $uploadPdfOk = 1;
    
        if ($bookfile_ext != "pdf") {
            $_SESSION['error'] = "Chỉ cho phép tải lên file PDF.";
            $uploadPdfOk = 0;
        }
    
        if ($_FILES["bookfile"]["size"] > 20 * 1024 * 1024) {
            $_SESSION['error'] = "File PDF quá lớn (yêu cầu < 20MB).";
            $uploadPdfOk = 0;
        }
    
        // Đổi tên file PDF thành unique
        $bookfile_newname = uniqid('pdf_', true) . '.' . $bookfile_ext;
        $target_dir_pdf = "assets/books/";
        $target_file_pdf = $target_dir_pdf . $bookfile_newname;
    
        // ==============================
        // UPLOAD và INSERT DB nếu OK
        // ==============================
        if ($uploadOk == 1 && $uploadPdfOk == 1) {
            if (move_uploaded_file($image_tmp, $target_file_img)) {
                if (move_uploaded_file($bookfile_tmp, $target_file_pdf)) {
                    $sql = "INSERT INTO tblbooks(BookName, CatId, AuthorId, ISBNNumber, BookPrice, Quantity, bookimage, BookFile) 
                            VALUES(:bookname, :category, :author, :isbn, :price, :quantity, :bookimage, :bookfile)";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
                    $query->bindParam(':category', $category, PDO::PARAM_STR);
                    $query->bindParam(':author', $author, PDO::PARAM_STR);
                    $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
                    $query->bindParam(':price', $price, PDO::PARAM_STR);
                    $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                    $query->bindParam(':bookimage', $bookimage_newname, PDO::PARAM_STR);
                    $query->bindParam(':bookfile', $bookfile_newname, PDO::PARAM_STR);
    
                    $query->execute();
                    $lastInsertId = $dbh->lastInsertId();
    
                    if ($lastInsertId) {
                        $_SESSION['msg'] = "Thêm sách thành công!";
                        header('location:manage-books.php');
                        exit();
                    } else {
                        unlink($target_file_img);
                        unlink($target_file_pdf);
                        $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại.";
                        header('location:add-book.php');
                        exit();
                    }
                } else {
                    unlink($target_file_img);
                    $_SESSION['error'] = "Không thể upload file PDF.";
                    header('location:add-book.php');
                    exit();
                }
            }
        } else {
            header('location:add-book.php');
            exit();
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
    <title>Online Library Management System | Add Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Thêm sách mới</h4>
                </div>
            </div>
            <!-- Hiển thị thông báo lỗi/thành công -->
             <div class="row">
                 <?php if(!empty($_SESSION['error'])){ ?>
                     <div class="col-md-6 col-md-offset-3">
                         <div class="alert alert-danger">
                             <strong>Lỗi :</strong> <?php echo htmlentities($_SESSION['error']); ?>
                             <?php $_SESSION['error']=""; // Xóa session sau khi hiển thị ?>
                         </div>
                     </div>
                 <?php } ?>
                  <?php if(!empty($_SESSION['msg'])){ ?>
                     <div class="col-md-6 col-md-offset-3">
                         <div class="alert alert-success">
                             <strong>Thành công :</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                              <?php $_SESSION['msg']=""; // Xóa session sau khi hiển thị ?>
                         </div>
                     </div>
                 <?php } ?>
             </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">Thông tin sách</div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Tên sách<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookname" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <label>Thể loại<span style="color:red;">*</span></label>
                                    <select class="form-control" name="category" required="required">
                                        <option value="">Chọn thể loại</option>
                                        <?php 
                                        $status = 1;
                                        $sql_cat = "SELECT * FROM tblcategory WHERE Status = :status ORDER BY CategoryName ASC"; // Thêm ORDER BY
                                        $query_cat = $dbh->prepare($sql_cat);
                                        $query_cat->bindParam(':status', $status, PDO::PARAM_INT);
                                        $query_cat->execute();
                                        $results_cat = $query_cat->fetchAll(PDO::FETCH_OBJ);
                                        if ($query_cat->rowCount() > 0) {
                                            foreach ($results_cat as $result_cat) { ?>  
                                                <option value="<?php echo htmlentities($result_cat->id); ?>"><?php echo htmlentities($result_cat->CategoryName); ?></option>
                                            <?php }
                                        } ?> 
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tác giả<span style="color:red;">*</span></label>
                                    <select class="form-control" name="author" required="required">
                                        <option value="">Chọn tác giả</option>
                                        <?php 
                                        $sql_auth = "SELECT * FROM tblauthors ORDER BY AuthorName ASC"; // Thêm ORDER BY
                                        $query_auth = $dbh->prepare($sql_auth);
                                        $query_auth->execute();
                                        $results_auth = $query_auth->fetchAll(PDO::FETCH_OBJ);
                                        if ($query_auth->rowCount() > 0) {
                                            foreach ($results_auth as $result_auth) { ?>  
                                                <option value="<?php echo htmlentities($result_auth->id); ?>"><?php echo htmlentities($result_auth->AuthorName); ?></option>
                                            <?php }
                                        } ?> 
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>ISBN<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="isbn" required="required" autocomplete="off" />
                                    <p class="help-block">ISBN là Mã số tiêu chuẩn quốc tế cho sách. ISBN phải là duy nhất.</p>
                                </div>

                                <div class="form-group">
                                    <label>Giá (VND)<span style="color:red;">*</span></label>
                                    <input class="form-control" type="number" name="price" autocomplete="off" required="required" min="0" />
                                </div>

                                <!-- ============================================ -->
                                <!-- ===   THÊM TRƯỜNG NHẬP SỐ LƯỢNG Ở ĐÂY   === -->
                                <!-- ============================================ -->
                                <div class="form-group">
                                    <label>Số lượng<span style="color:red;">*</span></label>
                                    <input class="form-control" type="number" name="quantity" autocomplete="off" required="required" min="0" />
                                </div>
                                <!-- ============================================ -->

                                <div class="form-group">
                                    <label>Ảnh bìa sách<span style="color:red;">*</span></label>
                                    <input class="form-control" type="file" name="bookimage" accept="image/png, image/jpeg, image/jpg, image/gif" required />
                                </div>

                                <div class="form-group">
                                    <label>File sách<span style="color:red;">*</span></label>
                                    <input class="form-control" type="file" name="bookfile" accept="application/pdf" required />
                                </div>

                                <button type="submit" name="add" class="btn btn-info">Thêm sách</button>
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