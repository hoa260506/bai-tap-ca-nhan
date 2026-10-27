
USE quan_ly_su_kien;

INSERT INTO cau_lac_bo (ten_cau_lac_bo, mo_ta)
SELECT du_lieu.ten_cau_lac_bo, du_lieu.mo_ta
FROM (
    SELECT 'CLB Nghiên cứu Khoa học / STEM' AS ten_cau_lac_bo,
           'Tăng cường niềm đam mê khoa học, công nghệ, kỹ thuật và toán học. Thành viên tham gia dự án sáng tạo, mô hình, robot và chuẩn bị cho các cuộc thi khoa học kỹ thuật.' AS mo_ta
    UNION ALL SELECT 'CLB Tiếng Anh (English Club - EC)',
           'Môi trường luyện tập giao tiếp, tranh biện, thuyết trình bằng tiếng Anh; tổ chức workshop và giao lưu văn hóa quốc tế.'
    UNION ALL SELECT 'CLB Tin học & Lập trình (Coding/IT Club)',
           'Dành cho người yêu thích công nghệ thông tin, phát triển phần mềm, lập trình web/game và tìm hiểu trí tuệ nhân tạo (AI).'
    UNION ALL SELECT 'CLB Âm nhạc (Music Club)',
           'Quy tụ thành viên yêu ca hát và chơi nhạc cụ; biểu diễn trong sự kiện của trường và tổ chức đêm nhạc nội bộ.'
    UNION ALL SELECT 'CLB Nhảy & Vũ đạo (Dance Club)',
           'Luyện tập Modern Dance, Hip-hop, K-pop Cover, Ballet và các thể loại nhảy khác.'
    UNION ALL SELECT 'CLB Kịch',
           'Phát triển khả năng diễn xuất, viết kịch bản, đạo diễn và dựng cảnh; dàn dựng kịch sân khấu hoặc phim ngắn học đường.'
    UNION ALL SELECT 'CLB Mỹ thuật & Thiết kế (Art & Design Club)',
           'Dành cho người yêu hội họa, vẽ tranh, nhiếp ảnh, thiết kế đồ họa và đồ thủ công.'
    UNION ALL SELECT 'CLB Bóng đá',
           'Rèn luyện thể lực, tinh thần đồng đội và kỹ thuật thi đấu; tham gia các giải bóng đá giao hữu giữa các trường.'
    UNION ALL SELECT 'CLB Võ thuật',
           'Rèn luyện sức khỏe, tính kỷ luật, khả năng tự vệ và tinh thần thượng võ.'
    UNION ALL SELECT 'CLB Tình nguyện',
           'Tổ chức quyên góp, tiếp sức mùa thi, thăm hỏi hoàn cảnh khó khăn và các chiến dịch bảo vệ môi trường.'
    UNION ALL SELECT 'CLB Truyền thông',
           'Quản lý kênh thông tin và mạng xã hội; chụp ảnh, viết bài, thiết kế ấn phẩm và quay video sự kiện.'
    UNION ALL SELECT 'CLB Sách',
           'Không gian đọc sách, chia sẻ và review sách; tổ chức tọa đàm, đổi sách cũ và quyên góp sách.'
    UNION ALL SELECT 'CLB Văn học',
           'Sáng tác thơ, truyện ngắn, bình thơ, phân tích tác phẩm văn học và tổ chức hoạt động cảm thụ nghệ thuật chữ viết.'
    UNION ALL SELECT 'CLB Hiến máu tình nguyện',
           'Tuyên truyền, vận động hiến máu cứu người và phối hợp tổ chức ngày hội hiến máu tại trường.'
) AS du_lieu
WHERE NOT EXISTS (
    SELECT 1
    FROM cau_lac_bo AS clb
    WHERE clb.ten_cau_lac_bo = du_lieu.ten_cau_lac_bo
);

INSERT INTO su_kien (
    cau_lac_bo_id,
    ten_su_kien,
    mo_ta,
    dia_diem,
    thoi_gian_bat_dau,
    thoi_gian_ket_thuc,
    so_luong_toi_da,
    trang_thai,
    nguoi_tao_id
)
SELECT
    clb.id,
    ds.ten_su_kien,
    ds.mo_ta,
    ds.dia_diem,
    ds.bat_dau,
    ds.ket_thuc,
    ds.so_luong,
    'cong_khai',
    1
FROM (
    SELECT
        'CLB Nghiên cứu Khoa học / STEM' AS ten_clb,
        'Ngày hội sáng tạo STEM 2027' AS ten_su_kien,
        'Trưng bày mô hình khoa học, robot và các dự án sáng tạo của sinh viên.' AS mo_ta,
        'Phòng hội thảo A' AS dia_diem,
        '2027-03-20 08:00:00' AS bat_dau,
        '2027-03-20 11:30:00' AS ket_thuc,
        100 AS so_luong

    UNION ALL SELECT
        'CLB Tiếng Anh (English Club - EC)',
        'English Speaking Day 2027',
        'Giao lưu tiếng Anh, tranh biện và thuyết trình theo nhóm.',
        'Hội trường B',
        '2027-03-25 14:00:00',
        '2027-03-25 17:00:00',
        80

    UNION ALL SELECT
        'CLB Tin học & Lập trình (Coding/IT Club)',
        'Workshop PHP và xây dựng website',
        'Thực hành lập trình PHP, kết nối MySQL và xây dựng website cơ bản.',
        'Phòng máy tính 1',
        '2027-04-05 08:00:00',
        '2027-04-05 11:30:00',
        50

    UNION ALL SELECT
        'CLB Âm nhạc (Music Club)',
        'Đêm nhạc Thanh xuân',
        'Chương trình biểu diễn ca hát và nhạc cụ do sinh viên thực hiện.',
        'Sân khấu ngoài trời',
        '2027-04-10 18:30:00',
        '2027-04-10 21:00:00',
        200

    UNION ALL SELECT
        'CLB Nhảy & Vũ đạo (Dance Club)',
        'Dance Showcase 2027',
        'Biểu diễn các tiết mục hiện đại, Hip-hop và K-pop Cover.',
        'Hội trường lớn',
        '2027-04-15 18:00:00',
        '2027-04-15 20:30:00',
        150

    UNION ALL SELECT
        'CLB Kịch',
        'Sân khấu tuổi trẻ',
        'Trình diễn các vở kịch ngắn về cuộc sống sinh viên và tình bạn.',
        'Phòng biểu diễn nghệ thuật',
        '2027-04-20 19:00:00',
        '2027-04-20 21:00:00',
        100

    UNION ALL SELECT
        'CLB Mỹ thuật & Thiết kế (Art & Design Club)',
        'Triển lãm Sắc màu sinh viên',
        'Trưng bày tranh, ảnh, thiết kế đồ họa và sản phẩm handmade.',
        'Sảnh nhà A',
        '2027-05-05 08:00:00',
        '2027-05-05 16:00:00',
        120

    UNION ALL SELECT
        'CLB Bóng đá',
        'Giải bóng đá sinh viên 2027',
        'Giải đấu giao hữu nhằm nâng cao thể lực và tinh thần đồng đội.',
        'Sân bóng trường',
        '2027-05-10 07:00:00',
        '2027-05-10 11:00:00',
        100

    UNION ALL SELECT
        'CLB Võ thuật',
        'Ngày hội võ thuật và tự vệ',
        'Giao lưu võ thuật, rèn luyện thể lực, kỷ luật và kỹ năng tự vệ.',
        'Nhà thi đấu',
        '2027-05-15 08:00:00',
        '2027-05-15 11:00:00',
        80

    UNION ALL SELECT
        'CLB Tình nguyện',
        'Chủ nhật xanh - Vì môi trường',
        'Sinh viên cùng dọn vệ sinh khuôn viên và tuyên truyền bảo vệ môi trường.',
        'Công viên gần trường',
        '2027-05-20 07:00:00',
        '2027-05-20 10:30:00',
        100

    UNION ALL SELECT
        'CLB Truyền thông',
        'Workshop sáng tạo nội dung số',
        'Chia sẻ kỹ năng chụp ảnh, quay video và xây dựng nội dung mạng xã hội.',
        'Phòng hội thảo C',
        '2027-06-05 08:30:00',
        '2027-06-05 11:30:00',
        60

    UNION ALL SELECT
        'CLB Sách',
        'Ngày hội sách và đổi sách',
        'Giới thiệu sách hay, chia sẻ cảm nhận và trao đổi sách giữa sinh viên.',
        'Thư viện trường',
        '2027-06-10 08:00:00',
        '2027-06-10 16:00:00',
        100

    UNION ALL SELECT
        'CLB Văn học',
        'Tọa đàm Văn học và tuổi trẻ',
        'Thảo luận tác phẩm văn học, sáng tác thơ và chia sẻ cảm thụ nghệ thuật.',
        'Phòng sinh hoạt chung',
        '2027-06-15 14:00:00',
        '2027-06-15 16:30:00',
        70

    UNION ALL SELECT
        'CLB Hiến máu tình nguyện',
        'Ngày hội hiến máu nhân đạo',
        'Tuyên truyền hiến máu và phối hợp tổ chức hoạt động hiến máu tình nguyện.',
        'Nhà đa năng',
        '2027-06-20 07:30:00',
        '2027-06-20 11:30:00',
        150
) AS ds
JOIN cau_lac_bo AS clb
    ON clb.ten_cau_lac_bo = ds.ten_clb
WHERE NOT EXISTS (
    SELECT 1
    FROM su_kien AS sk
    WHERE sk.ten_su_kien = ds.ten_su_kien
);