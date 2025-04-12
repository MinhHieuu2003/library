<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $bookid = isset($_GET['bookid']) ? intval($_GET['bookid']) : 0; // Lấy bookid an toàn

    if ($bookid == 0) {
        $_SESSION['error'] = "ID sách không hợp lệ.";
        header('location:manage-books.php');
        exit();
    }

    if (isset($_POST['update'])) {
        $bookname = $_POST['bookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity']; // <<< LẤY SỐ LƯỢNG TỪ FORM

        // --- Xử lý hình ảnh ---
        $bookimage_new = $_FILES["bookimage"]["name"];
        $bookimage_current = $_POST['current_bookimage']; // Lấy tên ảnh hiện tại từ hidden input
        $bookimage_to_save = $bookimage_current; // Mặc định giữ ảnh cũ

        // Kiểm tra nếu có file ảnh mới được tải lên
        if (!empty($bookimage_new)) {
            $target_dir = "assets/img/"; 
            // Có thể thêm logic tạo tên file duy nhất ở đây nếu muốn
            $target_file = $target_dir . basename($bookimage_new);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $uploadOk = 1;

            // Kiểm tra cơ bản (kích thước, loại file...) - nên thêm vào giống add-book.php
             $check = getimagesize($_FILES["bookimage"]["tmp_name"]);
             if ($check === false) {
                 $_SESSION['error'] = "File không phải là ảnh.";
                 $uploadOk = 0;
             }
             // Thêm kiểm tra kích thước, loại file ở đây nếu cần...

            if ($uploadOk == 1 && move_uploaded_file($_FILES["bookimage"]["tmp_name"], $target_file)) {
                 // Upload thành công, sử dụng tên file mới
                 $bookimage_to_save = $bookimage_new;
                 // (Tùy chọn) Xóa ảnh cũ nếu tên khác ảnh mới và ảnh cũ tồn tại
                 if ($bookimage_current != $bookimage_new && file_exists($target_dir . $bookimage_current)) {
                    // unlink($target_dir . $bookimage_current); 
                    // Cẩn thận khi xóa, đảm bảo logic đúng
                 }
            } else {
                 // Upload lỗi, giữ nguyên ảnh cũ và báo lỗi (hoặc dừng lại)
                 $_SESSION['error'] = "Upload ảnh mới thất bại, giữ nguyên ảnh cũ.";
                 // Không cập nhật tên ảnh trong DB nếu upload lỗi
                 // header('location: edit-book.php?bookid='.$bookid); // Có thể chuyển hướng lại
                 // exit();
            }
        } 
        // --- Kết thúc xử lý hình ảnh ---

        // --- Xử lý BookFile ---
        $bookfile_new = $_FILES["bookfile"]["name"];
        $bookfile_current = $_POST['current_bookfile']; 
        $bookfile_to_save = $bookfile_current;

        if (!empty($bookfile_new)) {
            $file_dir = "assets/books/";
            $target_file_path = $file_dir . basename($bookfile_new);
            $file_ext = strtolower(pathinfo($target_file_path, PATHINFO_EXTENSION));
            $uploadOk = 1;

            if ($file_ext != "pdf") {
                $_SESSION['error'] = "Chỉ cho phép tải file PDF.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1 && move_uploaded_file($_FILES["bookfile"]["tmp_name"], $target_file_path)) {
                $bookfile_to_save = $bookfile_new;

                if ($bookfile_current != $bookfile_new && file_exists($file_dir . $bookfile_current)) {
                    unlink($file_dir . $bookfile_current);
                }
            } else {
                $_SESSION['error'] = "Upload file PDF thất bại, giữ nguyên file cũ.";
            }
        }

        // --- CẬP NHẬT SQL ĐỂ BAO GỒM QUANTITY ---
        $sql = "UPDATE tblbooks 
            SET BookName=:bookname, 
                CatId=:category, 
                AuthorId=:author, 
                ISBNNumber=:isbn, 
                BookPrice=:price, 
                Quantity=:quantity,
                bookimage=:bookimage,
                BookFile=:bookfile   
            WHERE id=:bookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_INT); 
        $query->bindParam(':author', $author, PDO::PARAM_INT);   
        $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR);   
        $query->bindParam(':quantity', $quantity, PDO::PARAM_INT); 
        $query->bindParam(':bookimage', $bookimage_to_save, PDO::PARAM_STR); 
        $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $query->bindParam(':bookfile', $bookfile_to_save, PDO::PARAM_STR);

        if ($query->execute()) {
             $_SESSION['msg'] = "Thông tin sách đã được cập nhật thành công!";
             header('location:manage-books.php');
             exit();
        } else {
             $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.";
             // Chuyển hướng về trang edit để người dùng thấy lỗi và sửa
             header('location: edit-book.php?bookid='.$bookid); 
             exit();
        }

    } // end if(isset($_POST['update']))
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Chỉnh sửa sách</title>
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
                    <h4 class="header-line">Chỉnh sửa thông tin sách</h4>
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
                        <div class="panel-heading">Thông tin chi tiết sách</div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <?php 
                                // --- CẬP NHẬT SQL SELECT ĐỂ LẤY QUANTITY ---
                                $sql = "SELECT 
                                            tblbooks.BookName, 
                                            tblbooks.bookimage, 
                                            tblcategory.CategoryName, 
                                            tblcategory.id as cid, 
                                            tblauthors.AuthorName, 
                                            tblauthors.id as athrid, 
                                            tblbooks.ISBNNumber, 
                                            tblbooks.BookPrice, 
                                            tblbooks.Quantity,      
                                            tblbooks.id as bookid 
                                        FROM tblbooks 
                                        JOIN tblcategory ON tblcategory.id = tblbooks.CatId 
                                        JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId 
                                        WHERE tblbooks.id = :bookid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { 
                                        $current_bookimage = $result->bookimage; 
                                        ?>  
                                        <!-- Hidden input để lưu tên ảnh hiện tại, dùng khi không upload ảnh mới -->
                                        <input type="hidden" name="current_bookimage" value="<?php echo htmlentities($current_bookimage); ?>">

                                        <div class="form-group">
                                            <label>Tên sách<span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->BookName); ?>" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Thể loại<span style="color:red;">*</span></label>
                                            <select class="form-control" name="category" required="required">
                                                <option value="<?php echo htmlentities($result->cid); ?>"> <?php echo htmlentities($result->CategoryName); ?></option>
                                                <?php 
                                                $status = 1;
                                                // Lấy các category khác category hiện tại
                                                $sql1 = "SELECT * FROM tblcategory WHERE Status=:status AND id!=:cid ORDER BY CategoryName ASC"; 
                                                $query1 = $dbh->prepare($sql1);
                                                $query1->bindParam(':status', $status, PDO::PARAM_INT);
                                                $query1->bindParam(':cid', $result->cid, PDO::PARAM_INT);
                                                $query1->execute();
                                                $resultss = $query1->fetchAll(PDO::FETCH_OBJ);
                                                if ($query1->rowCount() > 0) {
                                                    foreach ($resultss as $row) { ?>  
                                                        <option value="<?php echo htmlentities($row->id); ?>"><?php echo htmlentities($row->CategoryName); ?></option>
                                                    <?php }
                                                } ?> 
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Tác giả<span style="color:red;">*</span></label>
                                            <select class="form-control" name="author" required="required">
                                                <option value="<?php echo htmlentities($result->athrid); ?>"> <?php echo htmlentities($result->AuthorName); ?></option>
                                                <?php 
                                                // Lấy các author khác author hiện tại
                                                $sql2 = "SELECT * FROM tblauthors WHERE id!=:athrid ORDER BY AuthorName ASC";
                                                $query2 = $dbh->prepare($sql2);
                                                 $query2->bindParam(':athrid', $result->athrid, PDO::PARAM_INT);
                                                $query2->execute();
                                                $result2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                                if ($query2->rowCount() > 0) {
                                                    foreach ($result2 as $ret) {?>  
                                                        <option value="<?php echo htmlentities($ret->id); ?>"><?php echo htmlentities($ret->AuthorName); ?></option>
                                                    <?php }
                                                } ?> 
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>ISBN<span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($result->ISBNNumber); ?>" required="required" />
                                            <p class="help-block">ISBN là Mã số tiêu chuẩn quốc tế cho sách. ISBN phải là duy nhất.</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Giá (VND)<span style="color:red;">*</span></label>
                                            <input class="form-control" type="number" name="price" value="<?php echo htmlentities($result->BookPrice); ?>" required="required" min="0"/>
                                        </div>

                                        <!-- =========================================== -->
                                        <!-- === THÊM TRƯỜNG SỬA SỐ LƯỢNG Ở ĐÂY === -->
                                        <!-- =========================================== -->
                                        <div class="form-group">
                                            <label>Số lượng<span style="color:red;">*</span></label>
                                            <input class="form-control" type="number" name="quantity" value="<?php echo htmlentities($result->Quantity); ?>" required="required" min="0"/>
                                        </div>
                                        <!-- =========================================== -->

                                        <input type="hidden" name="current_bookfile" value="<?php echo htmlentities($result->BookFile); ?>">

                                        <div class="form-group">
                                            <label>Ảnh bìa sách hiện tại:</label><br/>
                                            <!-- Sửa đường dẫn ảnh -->
                                            <img src="assets/img/<?php echo htmlentities($current_bookimage); ?>" width="100" style="margin-bottom: 10px; border: 1px solid #ccc;" /><br/>
                                            <label>Tải ảnh mới (để thay thế):</label>
                                            <input type="file" name="bookimage" class="form-control" accept="image/png, image/jpeg, image/jpg, image/gif" />
                                            <p class="help-block">Để trống nếu bạn không muốn thay đổi ảnh bìa.</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Tải file mới (PDF):</label>
                                            <input type="file" name="bookfile" class="form-control" accept="application/pdf" />
                                            <p class="help-block">Để trống nếu không muốn thay đổi file sách.</p>
                                        </div>

                                <?php } // end foreach
                                } else { // end if rowCount > 0 ?>
                                 <div class="alert alert-warning">Không tìm thấy thông tin sách với ID này.</div>   
                                <?php } ?>
                                
                                <?php if ($query->rowCount() > 0): // Chỉ hiển thị nút nếu có dữ liệu sách ?>
                                <button type="submit" name="update" class="btn btn-info">Cập nhật</button>
                                <?php endif; ?>

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