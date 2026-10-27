
<?php
function hienThiChiTiet($giaTri): string
{
    return htmlspecialchars((string) ($giaTri ?? ''), ENT_QUOTES, 'UTF-8');
}

$trangThai = [
    'nhap' => 'Bản nháp',
    'cong_khai' => 'Đang công khai',
    'da_huy' => 'Đã hủy',
    'ket_thuc' => 'Đã kết thúc'
];

$idSuKien = (int) $suKien['id'];
$tenSuKien = $suKien['ten_su_kien'] ?? 'Sự kiện';
$sucChua = max(0, (int) ($suKien['so_luong_toi_da'] ?? 0));
$soNguoiDangKy = max(0, (int) ($suKien['so_nguoi_dang_ky'] ?? 0));
$soNguoiDaThamGia = max(0, (int) ($suKien['so_nguoi_da_tham_gia'] ?? 0));
$soChoConLai = max(0, (int) ($suKien['so_cho_con_lai'] ?? ($sucChua - $soNguoiDangKy)));
$soNguoiChuaDiemDanh = max(0, $soNguoiDangKy - $soNguoiDaThamGia);
$soNguoiHuy = max(0, (int) ($suKien['so_nguoi_huy'] ?? 0));

$tyLeLapDay = $sucChua > 0
    ? min(100, round($soNguoiDangKy / $sucChua * 100))
    : 0;

$tyLeThamDu = $soNguoiDangKy > 0
    ? min(100, round($soNguoiDaThamGia / $soNguoiDangKy * 100))
    : 0;

$batDau = !empty($suKien['thoi_gian_bat_dau'])
    ? strtotime($suKien['thoi_gian_bat_dau'])
    : false;

$ketThuc = !empty($suKien['thoi_gian_ket_thuc'])
    ? strtotime($suKien['thoi_gian_ket_thuc'])
    : false;
$hanDangKy = !empty($suKien['han_dang_ky'])
    ? strtotime($suKien['han_dang_ky'])
    : false;

$daHetHanDangKy = $hanDangKy && $hanDangKy < time();
$bayGio = time();
$trangThaiHienTai = $suKien['trang_thai'] ?? 'nhap';

if ($trangThaiHienTai === 'da_huy') {
    $tinhTrangThoiGian = 'Sự kiện đã bị hủy';
} elseif ($trangThaiHienTai === 'ket_thuc' || ($ketThuc && $ketThuc < $bayGio)) {
    $tinhTrangThoiGian = 'Sự kiện đã kết thúc';
} elseif ($batDau && $batDau > $bayGio) {
    $thoiGianConLai = $batDau - $bayGio;
    $soNgay = intdiv($thoiGianConLai, 86400);
    $soGio = intdiv($thoiGianConLai % 86400, 3600);
    $soPhut = intdiv($thoiGianConLai % 3600, 60);

    if ($soNgay > 0) {
        $tinhTrangThoiGian = "Còn {$soNgay} ngày {$soGio} giờ đến khi bắt đầu";
    } elseif ($soGio > 0) {
        $tinhTrangThoiGian = "Còn {$soGio} giờ {$soPhut} phút đến khi bắt đầu";
    } else {
        $tinhTrangThoiGian = "Còn {$soPhut} phút đến khi bắt đầu";
    }
} elseif ($batDau && $batDau <= $bayGio && (!$ketThuc || $ketThuc >= $bayGio)) {
    $tinhTrangThoiGian = 'Sự kiện đang trong khung thời gian tổ chức';
} else {
    $tinhTrangThoiGian = 'Chưa xác định lịch trình';
}

$canhBao = [];

if ($trangThaiHienTai === 'nhap') {
    $canhBao[] = ['info', 'Sự kiện đang ở trạng thái bản nháp, chưa được công khai.'];
}
if (
    $trangThaiHienTai === 'cong_khai'
    && $hanDangKy
    && !$daHetHanDangKy
    && ($hanDangKy - $bayGio) <= 86400
) {
    $canhBao[] = [
        'warning',
        'Hạn đăng ký còn dưới 24 giờ. Hãy theo dõi số lượng đăng ký và thông báo cho người tham gia.'
    ];
}
if ($trangThaiHienTai === 'cong_khai' && $sucChua > 0 && $soChoConLai === 0) {
    $canhBao[] = ['warning', 'Sự kiện đã hết chỗ theo số đăng ký hợp lệ hiện tại.'];
} elseif ($trangThaiHienTai === 'cong_khai' && $tyLeLapDay >= 80) {
    $canhBao[] = ['warning', 'Sức chứa đã đạt từ 80%. Hãy theo dõi số lượng đăng ký.'];
}

if ($soNguoiChuaDiemDanh > 0 && $batDau && $bayGio >= $batDau) {
    $canhBao[] = ['info', 'Còn ' . $soNguoiChuaDiemDanh . ' người chưa được điểm danh.'];
}

if ($trangThaiHienTai === 'da_huy') {
    $canhBao[] = ['danger', 'Sự kiện đã bị hủy. Kiểm tra thông báo và danh sách đăng ký liên quan.'];
}

$nhanTrangThai = $trangThai[$trangThaiHienTai] ?? $trangThaiHienTai;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết sự kiện | <?= hienThiChiTiet($tenSuKien) ?></title>

    <style>
        * { box-sizing: border-box; }

        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --text: #172033;
            --muted: #64748b;
            --border: #e5eaf2;
            --surface: #ffffff;
            --background: #f5f7fb;
            --green: #059669;
            --orange: #d97706;
            --red: #dc2626;
        }

        body {
            margin: 0;
            padding: 28px;
            background: var(--background);
            color: var(--text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            line-height: 1.55;
        }

        .container { max-width: 1180px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }

        .eyebrow {
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        h1 { margin: 5px 0 8px; font-size: clamp(25px, 4vw, 34px); line-height: 1.2; }
        h2 { font-size: 18px; margin: 0 0 18px; }
        h3 { font-size: 15px; margin: 0 0 8px; }
        p { margin: 0; }
        .muted { color: var(--muted); }
        .wrap { overflow-wrap: anywhere; }

        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, .035);
            min-width: 0;
        }

        .hero {
            color: white;
            border: 0;
            background: linear-gradient(125deg, #312e81, #4f46e5 58%, #818cf8);
            padding: 30px;
        }

        .hero .eyebrow { color: #c7d2fe; }
        .hero .muted { color: #e0e7ff; }

        .hero-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
        }

        .status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 800;
            background: #eef2ff;
            color: #4338ca;
            white-space: nowrap;
        }

        .status.da_huy { background: #fee2e2; color: #b91c1c; }
        .status.ket_thuc { background: #e2e8f0; color: #334155; }
        .status.cong_khai { background: #d1fae5; color: #065f46; }

        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            padding: 10px 15px;
            min-height: 42px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid transparent;
            background: var(--primary);
            color: white;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }

        .btn:hover { filter: brightness(.96); }
        .btn-light { background: white; color: #3730a3; }
        .btn-outline { background: white; border-color: var(--border); color: var(--text); }
        .btn:focus-visible { outline: 3px solid #c7d2fe; outline-offset: 2px; }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            min-width: 0;
        }

        .stat-label { color: var(--muted); font-size: 13px; font-weight: 600; }
        .stat-number { font-size: clamp(25px, 3vw, 34px); font-weight: 800; margin: 9px 0 4px; line-height: 1.15; }
        .stat-note { color: var(--muted); font-size: 12px; }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: grid; place-items: center; font-size: 20px;
            background: var(--primary-light);
            margin-bottom: 16px;
        }
        .green .stat-icon { background: #d1fae5; }
        .orange .stat-icon { background: #fef3c7; }
        .blue .stat-icon { background: #dbeafe; }

        .main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, .85fr);
            gap: 20px;
            align-items: start;
        }

        .detail-list { display: grid; gap: 0; }

        .detail-row {
            display: grid;
            grid-template-columns: 175px minmax(0, 1fr);
            gap: 15px;
            padding: 14px 0;
            border-bottom: 1px solid #edf0f5;
        }

        .detail-row:last-child { border-bottom: 0; }
        .detail-label { color: var(--muted); font-size: 13px; }
        .detail-value { font-weight: 650; overflow-wrap: anywhere; }

        .progress-block { margin: 22px 0; }
        .progress-heading { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 9px; font-size: 13px; }
        .progress-heading strong { font-size: 14px; }
        .track { height: 10px; background: #edf0f7; border-radius: 99px; overflow: hidden; }
        .fill { height: 100%; width: 0; border-radius: 99px; transition: width .3s; background: var(--primary); }
        .fill-green { background: var(--green); }

        .insight {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            margin-top: 12px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 13px;
        }

        .insight.warning { background: #fffbeb; border-color: #fde68a; color: #92400e; }
        .insight.danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .insight.info { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }

        .timeline { display: grid; gap: 0; }
        .timeline-item { display: grid; grid-template-columns: 22px minmax(0, 1fr); gap: 12px; padding-bottom: 22px; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot { width: 12px; height: 12px; margin-top: 5px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 0 5px var(--primary-light); }
        .timeline-date { font-size: 12px; color: var(--muted); margin-top: 3px; }

        .empty {
            color: var(--muted); background: #f8fafc;
            padding: 16px; border-radius: 12px; font-size: 13px;
        }

        .footer-note { color: var(--muted); font-size: 12px; margin-top: 12px; }

        @media (max-width: 900px) {
            body { padding: 18px; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            body { padding: 12px; }
            .panel, .hero { padding: 19px; border-radius: 14px; }
            .topbar, .hero-top { align-items: flex-start; flex-direction: column; }
            .stats { gap: 10px; }
            .stat { padding: 15px; }
            .detail-row { grid-template-columns: 1fr; gap: 4px; }
            .actions { flex-direction: column; }
            .actions .btn { width: 100%; }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="topbar">
        <div>
            <div class="eyebrow">Không gian ban tổ chức</div>
            <p class="muted">Báo cáo và theo dõi hiệu quả sự kiện</p>
        </div>
        <a class="btn btn-outline"
           href="/Nhom-4-LTW/public/index.php?trang=danh_sach">
            ← Danh sách sự kiện
        </a>
    </div>

    <section class="panel hero">
        <div class="hero-top">
            <div>
                <div class="eyebrow">Hồ sơ sự kiện #<?= $idSuKien ?></div>
                <h1 class="wrap"><?= hienThiChiTiet($tenSuKien) ?></h1>
                <p class="muted">
                    <?= hienThiChiTiet($suKien['ten_cau_lac_bo'] ?? 'Chưa cập nhật câu lạc bộ') ?>
                </p>
            </div>
            <span class="status <?= hienThiChiTiet($trangThaiHienTai) ?>">
                <?= hienThiChiTiet($nhanTrangThai) ?>
            </span>
        </div>

        <p style="margin-top: 20px; color: #eef2ff;">
            <?= hienThiChiTiet($tinhTrangThoiGian) ?>
        </p>

        <div class="actions">
            <a class="btn btn-light"
               href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id=<?= $idSuKien ?>">
                Quản lý người tham gia
            </a>

            <?php if (!in_array($trangThaiHienTai, ['da_huy', 'ket_thuc'], true)): ?>
                <a class="btn btn-outline"
                   href="/Nhom-4-LTW/public/index.php?trang=sua_su_kien&id=<?= $idSuKien ?>">
                    Chỉnh sửa sự kiện
                </a>
            <?php endif; ?>

            <button class="btn btn-outline" type="button" onclick="window.print()">
                In trang tổng quan
            </button>
        </div>
    </section>

    <section class="stats" aria-label="Các chỉ số sự kiện">
        <article class="stat">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Đăng ký hợp lệ</div>
            <div class="stat-number"><?= number_format($soNguoiDangKy) ?></div>
            <div class="stat-note">Trên tổng <?= number_format($sucChua) ?> chỗ</div>
        </article>

        <article class="stat green">
            <div class="stat-icon">✓</div>
            <div class="stat-label">Đã tham dự</div>
            <div class="stat-number"><?= number_format($soNguoiDaThamGia) ?></div>
            <div class="stat-note"><?= $tyLeThamDu ?>% số đăng ký hợp lệ</div>
        </article>

        <article class="stat blue">
            <div class="stat-icon">◷</div>
            <div class="stat-label">Chưa điểm danh</div>
            <div class="stat-number"><?= number_format($soNguoiChuaDiemDanh) ?></div>
            <div class="stat-note">Cần theo dõi khi sự kiện diễn ra</div>
        </article>

        <article class="stat orange">
            <div class="stat-icon">▤</div>
            <div class="stat-label">Chỗ còn lại</div>
            <div class="stat-number"><?= number_format($soChoConLai) ?></div>
            <div class="stat-note"><?= $tyLeLapDay ?>% sức chứa đã được lấp đầy</div>
        </article>
    </section>

    <div class="main-grid">
        <div>
            <section class="panel">
                <h2>📊 Phân tích sức chứa và tham dự</h2>

                <div class="progress-block">
                    <div class="progress-heading">
                        <span>Tỷ lệ lấp đầy sức chứa</span>
                        <strong><?= $tyLeLapDay ?>%</strong>
                    </div>
                    <div class="track"
                         role="progressbar"
                         aria-label="Tỷ lệ lấp đầy"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-valuenow="<?= $tyLeLapDay ?>">
                        <div class="fill" style="width: <?= $tyLeLapDay ?>%"></div>
                    </div>
                    <p class="footer-note">
                        <?= number_format($soNguoiDangKy) ?> / <?= number_format($sucChua) ?> chỗ đã được đăng ký hợp lệ.
                    </p>
                </div>

                <div class="progress-block">
                    <div class="progress-heading">
                        <span>Tỷ lệ tham dự</span>
                        <strong><?= $tyLeThamDu ?>%</strong>
                    </div>
                    <div class="track"
                         role="progressbar"
                         aria-label="Tỷ lệ tham dự"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-valuenow="<?= $tyLeThamDu ?>">
                        <div class="fill fill-green" style="width: <?= $tyLeThamDu ?>%"></div>
                    </div>
                    <p class="footer-note">
                        <?= number_format($soNguoiDaThamGia) ?> người đã tham dự trên <?= number_format($soNguoiDangKy) ?> đăng ký hợp lệ.
                    </p>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Đăng ký đã hủy</span>
                    <span class="detail-value"><?= number_format($soNguoiHuy) ?> người</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Chưa điểm danh</span>
                    <span class="detail-value"><?= number_format($soNguoiChuaDiemDanh) ?> người</span>
                </div>

                <p class="footer-note">
                    Lưu ý: các tỷ lệ được tính từ dữ liệu đăng ký hiện có. Người hủy đăng ký không được tính vào số đăng ký hợp lệ.
                </p>
            </section>

            <section class="panel">
                <h2>🗂 Thông tin chi tiết</h2>

                <div class="detail-list">
                    <div class="detail-row">
                        <span class="detail-label">Tên sự kiện</span>
                        <span class="detail-value"><?= hienThiChiTiet($tenSuKien) ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Câu lạc bộ phụ trách</span>
                        <span class="detail-value"><?= hienThiChiTiet($suKien['ten_cau_lac_bo'] ?? 'Chưa cập nhật') ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Địa điểm</span>
                        <span class="detail-value"><?= hienThiChiTiet($suKien['dia_diem'] ?: 'Chưa cập nhật') ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Sức chứa tối đa</span>
                        <span class="detail-value"><?= number_format($sucChua) ?> người</span>
                    </div>
                    <div class="detail-row">
    <span class="detail-label">Hạn đăng ký</span>
    <span class="detail-value">
        <?= hienThiChiTiet(
            $hanDangKy ? date('H:i - d/m/Y', $hanDangKy) : 'Chưa cập nhật'
        ) ?>
    </span>
</div>

<div class="detail-row">
    <span class="detail-label">Trạng thái đăng ký</span>
    <span class="detail-value">
        <?php if (!$hanDangKy): ?>
            Chưa xác định hạn đăng ký
        <?php elseif ($daHetHanDangKy): ?>
            Đã hết hạn đăng ký
        <?php elseif ($trangThaiHienTai !== 'cong_khai'): ?>
            Chưa nhận đăng ký công khai
        <?php else: ?>
            Đang trong thời gian đăng ký
        <?php endif; ?>
    </span>
</div>

                    <div class="detail-row">
                        <span class="detail-label">Mô tả</span>
                        <span class="detail-value" style="font-weight: 400;">
                            <?= nl2br(hienThiChiTiet($suKien['mo_ta'] ?: 'Chưa có mô tả cho sự kiện này.')) ?>
                        </span>
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="panel">
                <h2>🗓 Lịch trình sự kiện</h2>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div>
                            <h3>Bắt đầu sự kiện</h3>
                            <p>
                                <?= hienThiChiTiet(
                                    $batDau ? date('H:i - d/m/Y', $batDau) : 'Chưa cập nhật'
                                ) ?>
                            </p>
                            <p class="timeline-date">Thời điểm dự kiến bắt đầu</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div>
                            <h3>Kết thúc sự kiện</h3>
                            <p>
                                <?= hienThiChiTiet(
                                    $ketThuc ? date('H:i - d/m/Y', $ketThuc) : 'Chưa cập nhật'
                                ) ?>
                            </p>
                            <p class="timeline-date">Thời điểm dự kiến kết thúc</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2>🔔 Cảnh báo và đề xuất</h2>

                <?php if (empty($canhBao)): ?>
                    <div class="empty">
                        Chưa có cảnh báo nổi bật dựa trên trạng thái, thời gian và số liệu hiện tại.
                    </div>
                <?php else: ?>
                    <?php foreach ($canhBao as $mucCanhBao): ?>
                        <div class="insight <?= hienThiChiTiet($mucCanhBao[0]) ?>">
                            <span aria-hidden="true">
                                <?= $mucCanhBao[0] === 'warning' ? '⚠️' : ($mucCanhBao[0] === 'danger' ? '⛔' : 'ℹ️') ?>
                            </span>
                            <span><?= hienThiChiTiet($mucCanhBao[1]) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($trangThaiHienTai === 'cong_khai' && $soNguoiChuaDiemDanh > 0): ?>
                    <div class="insight info">
                        <span aria-hidden="true">💡</span>
                        <span>Hãy mở danh sách người tham gia để rà soát trạng thái đăng ký và điểm danh.</span>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <a class="btn"
                       href="/Nhom-4-LTW/public/index.php?trang=danh_sach_dang_ky&id=<?= $idSuKien ?>">
                        Mở danh sách người tham gia
                    </a>
                </div>
            </section>
        </aside>
    </div>

    <p class="footer-note" style="text-align: center; margin: 25px 0;">
        Trang tổng quan sự kiện · Dữ liệu hiển thị theo thông tin hiện có trong hệ thống
    </p>
</div>
</body>
</html>
