-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 13, 2025 lúc 07:37 AM
-- Phiên bản máy phục vụ: 10.4.25-MariaDB
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `library`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `FullName` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `AdminEmail` varchar(120) CHARACTER SET utf8mb4 DEFAULT NULL,
  `UserName` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `Password` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `FullName`, `AdminEmail`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'demoadmin', 'hieu@gmail.com', 'admin', 'e6e061838856bf47e1de730719fb2609', '2025-03-25 16:06:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblauthors`
--

CREATE TABLE `tblauthors` (
  `id` int(11) NOT NULL,
  `AuthorName` varchar(159) CHARACTER SET utf8mb4 DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `tblauthors`
--

INSERT INTO `tblauthors` (`id`, `AuthorName`, `creationDate`, `UpdationDate`) VALUES
(1, 'Naruto', '2025-03-28 12:49:09', '2025-03-29 16:03:28'),
(2, 'Ichigo', '2025-03-27 14:30:23', '2025-03-31 16:03:35'),
(3, 'Luffy', '2025-03-13 14:35:08', '2021-03-31 16:03:43'),
(4, 'Songoku', '2025-03-13 14:35:21', '2025-04-02 16:10:29'),
(10, 'Dale Carnegie', '2025-04-06 10:13:23', '2025-04-06 10:14:56'),
(11, 'Michael', '2025-04-06 12:46:10', '2025-04-06 12:46:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblbooks`
--

CREATE TABLE `tblbooks` (
  `id` int(11) NOT NULL,
  `BookName` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `CatId` int(11) DEFAULT NULL,
  `AuthorId` int(11) DEFAULT NULL,
  `ISBNNumber` int(11) DEFAULT NULL,
  `BookPrice` int(11) DEFAULT NULL,
  `Quantity` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lượng sách',
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `BookImage` varchar(255) DEFAULT NULL,
  `BookFile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `tblbooks`
--

INSERT INTO `tblbooks` (`id`, `BookName`, `CatId`, `AuthorId`, `ISBNNumber`, `BookPrice`, `Quantity`, `RegDate`, `UpdationDate`, `BookImage`, `BookFile`) VALUES
(1, 'Dragonball', 8, 4, 2222, 30000, 2, '2025-03-02 20:04:55', '2025-04-07 09:55:01', 'songoku.jpg', ''),
(3, 'Bleach', 6, 2, 1111, 20000, 5, '2025-03-03 20:17:31', '2025-04-07 10:00:58', 'Bleach_cover_01.jpg', ''),
(4, 'Naruto Shippuden', 4, 1, 1234, 50000, 12, '2025-04-02 17:27:25', '2025-04-07 10:01:04', 'Naruto_Volume_1_manga_cover.jpg', ''),
(5, 'Đắc Nhân Tâm', 9, 10, 2003, 50000, 20, '2025-04-06 10:17:44', '2025-04-06 13:38:42', 'dacnhantam86.jpg', ''),
(7, 'PHP & MYSQL', 5, 11, 3333, 20000, 10, '2025-04-06 13:35:22', '2025-04-07 10:01:36', 'PHP_SQL.jpg', ''),
(8, 'Sách mới', 5, 3, 9001, 15000, 20, '2025-04-12 18:35:06', '2025-04-13 03:39:55', 'matnaoxanhnhat.png', 'kiemthu (1).pdf'),
(9, 'New Book', 4, 10, 2100, 20000, 10, '2025-04-12 18:38:57', '2025-04-12 19:24:31', 'img_67fab3419fb9b4.70464226.png', 'CMP174.pdf');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblcategory`
--

CREATE TABLE `tblcategory` (
  `id` int(11) NOT NULL,
  `CategoryName` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `tblcategory`
--

INSERT INTO `tblcategory` (`id`, `CategoryName`, `Status`, `CreationDate`, `UpdationDate`) VALUES
(4, 'Lãng Mạn ', 1, '2025-02-28 18:35:25', '2025-04-02 16:13:34'),
(5, 'Công Nghệ', 1, '2025-02-28 18:35:39', '2025-04-02 16:13:42'),
(6, 'Khoa Học', 1, '2025-02-28 18:35:55', '2025-04-02 16:13:49'),
(8, 'Viễn Tưởng', 1, '2025-04-02 11:16:30', '2025-04-02 11:16:30'),
(9, 'Tâm Lý', 1, '2025-04-06 10:12:56', '2025-04-06 10:12:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblissuedbookdetails`
--

CREATE TABLE `tblissuedbookdetails` (
  `id` int(11) NOT NULL,
  `BookId` int(11) DEFAULT NULL,
  `StudentID` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `IssuesDate` timestamp NULL DEFAULT current_timestamp(),
  `ReturnDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `RetrunStatus` int(1) DEFAULT NULL,
  `fine` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `tblissuedbookdetails`
--

INSERT INTO `tblissuedbookdetails` (`id`, `BookId`, `StudentID`, `IssuesDate`, `ReturnDate`, `RetrunStatus`, `fine`) VALUES
(16, 5, 'SID012', '2025-04-06 14:13:19', '2025-04-07 08:10:52', 1, 10000),
(21, 1, 'SID012', '2025-04-07 09:55:01', '2025-04-07 09:55:26', 1, 10000),
(22, 4, 'SID012', '2025-04-07 09:56:42', '2025-04-07 10:00:15', 1, 0),
(23, 7, 'SID012', '2025-04-07 10:01:26', '2025-04-07 10:01:36', 1, 5000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblstudents`
--

CREATE TABLE `tblstudents` (
  `id` int(11) NOT NULL,
  `StudentId` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `FullName` varchar(120) CHARACTER SET utf8mb4 DEFAULT NULL,
  `EmailId` varchar(120) CHARACTER SET utf8mb4 DEFAULT NULL,
  `MobileNumber` char(11) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Password` varchar(120) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `tblstudents`
--

INSERT INTO `tblstudents` (`id`, `StudentId`, `FullName`, `EmailId`, `MobileNumber`, `Password`, `Status`, `RegDate`, `UpdationDate`) VALUES
(1, 'SID002', 'Hải Dương', 'skydas@gmail.com', '9865472555', 'f925916e2754e5e03f75dd58a5733251', 1, '2025-02-28 15:37:05', '2025-04-07 10:14:09'),
(11, 'SID012', 'hieu', 'hieu@gmail.com', '1234123124', 'c8837b23ff8aaa8a2dde915473ce0991', 1, '2025-04-02 11:05:38', NULL),
(12, 'SID013', 'Hiếu Minh', 'demo@gmail.com', '1234657621', 'c8837b23ff8aaa8a2dde915473ce0991', 1, '2025-04-02 16:28:15', NULL),
(14, 'SID015', 'DuongSkydas', 'skydas86@gmail.com', '1999999999', 'c8837b23ff8aaa8a2dde915473ce0991', 1, '2025-04-06 12:15:37', NULL),
(15, 'SID016', 'Kiên', 'broleader@gmail.com', '0712412521', 'c8837b23ff8aaa8a2dde915473ce0991', 1, '2025-04-07 10:12:12', '2025-04-07 10:12:12'),
(16, 'SID017', 'Vu Nguyen', 'vu@gmail.com', '0962465496', '202cb962ac59075b964b07152d234b70', 1, '2025-04-12 19:13:12', '2025-04-12 19:13:12');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblauthors`
--
ALTER TABLE `tblauthors`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentId` (`StudentId`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tblauthors`
--
ALTER TABLE `tblauthors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
