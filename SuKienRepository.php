
<?php

require_once __DIR__ . '/../Core/ket_noi_csdl.php';

class SuKienRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tu dong ket thuc su kien cong khai da qua thoi gian ket thuc.
     */
    public function capNhatSuKienDaKetThuc(): void
    {
        $sql = "
            UPDATE su_kien
            SET trang_thai = 'ket_thuc'
            WHERE trang_thai = 'cong_khai'
              AND thoi_gian_ket_thuc IS NOT NULL
              AND thoi_gian_ket_thuc <= NOW()
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    /**
     * Lay danh sach su kien theo vai tro nguoi dung.
     *
     * Ban to chuc: chi xem su kien cua CLB minh quan ly.
     * Admin: xem tat ca su kien.
     * Vai tro khac: chi xem su kien cong khai.
     */
    public function danhSachTheoVaiTro(array $nguoiDung): array
    {
        $this->capNhatSuKienDaKetThuc();

        $sql = "
            SELECT
                s.id,
                s.ten_su_kien,
                s.dia_diem,
                s.thoi_gian_bat_dau,
                s.so_luong_toi_da,
                s.trang_thai,

                COALESCE(tk.so_nguoi_dang_ky, 0)
                    AS so_nguoi_dang_ky,

                COALESCE(tk.so_nguoi_da_tham_gia, 0)
                    AS so_nguoi_da_tham_gia,

                GREATEST(
                    s.so_luong_toi_da
                    - COALESCE(tk.so_nguoi_dang_ky, 0),
                    0
                ) AS so_cho_con_lai

            FROM su_kien s

            LEFT JOIN (
                SELECT
                    su_kien_id,

                    COUNT(
                        CASE
                            WHEN trang_thai IN (
                                'da_dang_ky',
                                'da_tham_gia'
                            )
                            THEN 1
                        END
                    ) AS so_nguoi_dang_ky,

                    COUNT(
                        CASE
                            WHEN trang_thai = 'da_tham_gia'
                            THEN 1
                        END
                    ) AS so_nguoi_da_tham_gia

                FROM dang_ky
                GROUP BY su_kien_id
            ) tk ON tk.su_kien_id = s.id
        ";

        if (($nguoiDung['vai_tro'] ?? '') === 'ban_to_chuc') {
            $sql .= "
                INNER JOIN cau_lac_bo clb
                    ON s.cau_lac_bo_id = clb.id

                WHERE clb.nguoi_quan_ly_id = :nguoi_dung_id
            ";

            $sql .= " ORDER BY s.ngay_tao DESC";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'nguoi_dung_id' => (int) $nguoiDung['id']
            ]);
        } elseif (($nguoiDung['vai_tro'] ?? '') === 'admin') {
            $sql .= " ORDER BY s.ngay_tao DESC";

            $stmt = $this->pdo->query($sql);
        } else {
            $sql .= "
                WHERE s.trang_thai = 'cong_khai'
                ORDER BY s.ngay_tao DESC
            ";

            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiem tra cau lac bo co ton tai hay khong.
     */
    public function cauLacBoTonTai(int $cauLacBoId): bool
    {
        $sql = "
            SELECT id
            FROM cau_lac_bo
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $cauLacBoId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Lay cau lac bo do nguoi dung quan ly.
     */
    public function layCauLacBoTheoNguoiQuanLy(
        int $nguoiDungId
    ): ?int {
        $sql = "
            SELECT id
            FROM cau_lac_bo
            WHERE nguoi_quan_ly_id = :nguoi_dung_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId
        ]);

        $cauLacBoId = $stmt->fetchColumn();

        return $cauLacBoId !== false
            ? (int) $cauLacBoId
            : null;
    }

    /**
     * Them su kien moi.
     */
    public function them(array $duLieu): bool
    {
        $sql = "
            INSERT INTO su_kien (
                cau_lac_bo_id,
                ten_su_kien,
                mo_ta,
                dia_diem,
                thoi_gian_bat_dau,
                thoi_gian_ket_thuc,
                han_dang_ky,
                so_luong_toi_da,
                trang_thai,
                nguoi_tao_id
            ) VALUES (
                :cau_lac_bo_id,
                :ten_su_kien,
                :mo_ta,
                :dia_diem,
                :bat_dau,
                :ket_thuc,
                :han_dang_ky,
                :so_luong,
                :trang_thai,
                :nguoi_tao_id
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($duLieu);
    }

    /**
     * Lay su kien theo ID va kiem tra quyen quan ly.
     *
     * Admin: duoc tim su kien theo ID.
     * Ban to chuc: chi duoc tim su kien cua CLB minh quan ly.
     */
    public function laySuKienDuocPhep(
        int $id,
        array $nguoiDung
    ): ?array {
        $vaiTro = $nguoiDung['vai_tro'] ?? '';

        if (!in_array($vaiTro, ['ban_to_chuc', 'admin'], true)) {
            return null;
        }

        if ($vaiTro === 'admin') {
            $sql = "
                SELECT *
                FROM su_kien
                WHERE id = :id
                LIMIT 1
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'id' => $id
            ]);
        } else {
            $sql = "
                SELECT s.*
                FROM su_kien s

                INNER JOIN cau_lac_bo clb
                    ON s.cau_lac_bo_id = clb.id

                WHERE s.id = :id
                  AND clb.nguoi_quan_ly_id = :nguoi_dung_id

                LIMIT 1
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'id' => $id,
                'nguoi_dung_id' => (int) $nguoiDung['id']
            ]);
        }

        $suKien = $stmt->fetch(PDO::FETCH_ASSOC);

        return $suKien ?: null;
    }

    /**
     * Cap nhat thong tin su kien.
     */
    public function capNhat(
        int $id,
        array $duLieu
    ): bool {
        $sql = "
            UPDATE su_kien
            SET
                ten_su_kien = :ten_su_kien,
                mo_ta = :mo_ta,
                dia_diem = :dia_diem,
                thoi_gian_bat_dau = :bat_dau,
                thoi_gian_ket_thuc = :ket_thuc,
                han_dang_ky = :han_dang_ky,
                so_luong_toi_da = :so_luong,
                trang_thai = :trang_thai
            WHERE id = :id
        ";

        $duLieu['id'] = $id;

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($duLieu);
    }

    /**
     * Dem so ban ghi dang ky cua su kien.
     */
    public function demDangKy(int $suKienId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM dang_ky
            WHERE su_kien_id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $suKienId
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Xoa su kien.
     */
    public function xoa(int $id): bool
    {
        $sql = "
            DELETE FROM su_kien
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    /**
     * Huy su kien.
     */
    public function huy(int $id): bool
    {
        $sql = "
            UPDATE su_kien
            SET trang_thai = 'da_huy'
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    /**
     * Lay danh sach nguoi dang ky cua su kien.
     * Khong hien nguoi da huy dang ky.
     */
    public function danhSachDangKy(int $suKienId): array
    {
        $sql = "
            SELECT
                dk.id AS dang_ky_id,
                nd.ho_ten,
                nd.email,
                dk.thoi_gian_dang_ky,
                dk.trang_thai

            FROM dang_ky dk

            INNER JOIN nguoi_dung nd
                ON dk.nguoi_dung_id = nd.id

            WHERE dk.su_kien_id = :su_kien_id
              AND dk.trang_thai IN (
                  'da_dang_ky',
                  'da_tham_gia'
              )

            ORDER BY dk.thoi_gian_dang_ky DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'su_kien_id' => $suKienId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Diem danh nguoi da dang ky.
     *
     * Chi cap nhat khi:
     * - ID dang ky dung.
     * - ID su kien dung.
     * - Trang thai dang ky hien tai la da_dang_ky.
     */
    public function diemDanh(
        int $dangKyId,
        int $suKienId
    ): bool {
        $sql = "
            UPDATE dang_ky
            SET trang_thai = 'da_tham_gia'

            WHERE id = :dang_ky_id
              AND su_kien_id = :su_kien_id
              AND trang_thai = 'da_dang_ky'
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'su_kien_id' => $suKienId
        ]);
    }

    /**
     * Lay ten cau lac bo theo ID.
     */
    public function tenCauLacBo(int $cauLacBoId): ?string
    {
        $sql = "
            SELECT ten_cau_lac_bo
            FROM cau_lac_bo
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $cauLacBoId
        ]);

        $tenCauLacBo = $stmt->fetchColumn();

        return $tenCauLacBo !== false
            ? (string) $tenCauLacBo
            : null;
    }

    /**
     * Thong ke dang ky cua mot su kien.
     */
    public function thongKeDangKy(int $suKienId): array
    {
        $sql = "
            SELECT
                COUNT(
                    CASE
                        WHEN trang_thai IN (
                            'da_dang_ky',
                            'da_tham_gia'
                        )
                        THEN 1
                    END
                ) AS so_nguoi_dang_ky,

                COUNT(
                    CASE
                        WHEN trang_thai = 'da_tham_gia'
                        THEN 1
                    END
                ) AS so_nguoi_da_tham_gia,

                COUNT(
                    CASE
                        WHEN trang_thai = 'da_huy'
                        THEN 1
                    END
                ) AS so_nguoi_huy

            FROM dang_ky
            WHERE su_kien_id = :su_kien_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'su_kien_id' => $suKienId
        ]);

        $thongKe = $stmt->fetch(PDO::FETCH_ASSOC);

        return $thongKe ?: [
            'so_nguoi_dang_ky' => 0,
            'so_nguoi_da_tham_gia' => 0,
            'so_nguoi_huy' => 0
        ];
    }
}
