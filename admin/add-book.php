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
        $quantity = $_POST['quantity']; // <<< LẤY DỮ LIỆU SỐ LƯỢNG TỪ FORM
        $bookimage = $_FILES["bookimage"]["name"]; // Chỉ lấy tên file

        // ---- Đường dẫn lưu ảnh ----
        // Đảm bảo thư mục tồn tại và có quyền ghi
        $target_dir = "assets/img/"; 
        // Tạo tên file duy nhất hoặc kiểm tra nếu file đã tồn tại nếu cần
        $target_file = $target_dir . basename($bookimage);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // --- Kiểm tra cơ bản cho upload ảnh ---
        $uploadOk = 1;
        // Kiểm tra xem file có phải ảnh thật không
        $check = getimagesize($_FILES["bookimage"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = "File không phải là ảnh.";
            $uploadOk = 0;
        }
        // Kiểm tra kích thước file (ví dụ: giới hạn 5MB)
        if ($_FILES["bookimage"]["size"] > 5000000) {
            $_SESSION['error'] = "Xin lỗi, file ảnh quá lớn (yêu cầu < 5MB).";
            $uploadOk = 0;
        }
        // Cho phép định dạng ảnh nhất định
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['error'] = "Xin lỗi, chỉ cho phép file JPG, JPEG, PNG & GIF.";
            $uploadOk = 0;
        }

        // --- Nếu kiểm tra OK thì tiến hành upload và lưu DB ---
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["bookimage"]["tmp_name"], $target_file)) {
                // --- CẬP NHẬT CÂU LỆNH SQL ĐỂ THÊM QUANTITY ---
                $sql = "INSERT INTO tblbooks(BookName, CatId, AuthorId, ISBNNumber, BookPrice, Quantity, bookimage) 
                        VALUES(:bookname, :category, :author, :isbn, :price, :quantity, :bookimage)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
                $query->bindParam(':category', $category, PDO::PARAM_STR); // Thường là INT nếu lưu ID
                $query->bindParam(':author', $author, PDO::PARAM_STR);     // Thường là INT nếu lưu ID
                $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
                $query->bindParam(':price', $price, PDO::PARAM_STR);       // Nên là DECIMAL hoặc INT/FLOAT trong DB
                $query->bindParam(':quantity', $quantity, PDO::PARAM_INT); // <<< BIND PARAM CHO QUANTITY (nên là INT)
                $query->bindParam(':bookimage', $bookimage, PDO::PARAM_STR); // Lưu tên file
                
                $query->execute();
                $lastInsertId = $dbh->lastInsertId();

                if ($lastInsertId) {
                    $_SESSION['msg'] = "Thêm sách thành công!";
                    header('location:manage-books.php');
                    exit(); // Thêm exit sau header redirect
                } else {
                    // Xóa ảnh đã upload nếu insert DB lỗi
                    unlink($target_file); 
                    $_SESSION['error'] = "Đã xảy ra lỗi. Vui lòng thử lại";
                    header('location:add-book.php'); // Ở lại trang add để sửa
                    exit(); 
                }
            } else {
                $_SESSION['error'] = "Xin lỗi, đã có lỗi khi upload file ảnh.";
                header('location:add-book.php'); // Ở lại trang add
                exit();
            }
        } else {
             // Nếu $uploadOk = 0 do lỗi kiểm tra file
             header('location:add-book.php'); // Ở lại trang add
             exit();
        }
    } // end if(isset($_POST['add']))
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