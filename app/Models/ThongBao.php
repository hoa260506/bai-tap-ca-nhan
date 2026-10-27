
<?php

class ThongBao
{
    public static function taoNeuChuaCo(
        PDO $pdo,
        int $nguoiDungId,
        int $suKienId,
        string $loai,
        string $tieuDe,
        string $noiDung
    ): void {
        // Kiem tra thong bao cung loai cho su kien da ton tai chua.
        $kiemTra = $pdo->prepare("
            SELECT id
            FROM thong_bao
            WHERE nguoi_dung_id = :nguoi_dung_id
              AND su_kien_id = :su_kien_id
              AND loai = :loai
            LIMIT 1
        ");

        $kiemTra->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'loai' => $loai
        ]);

        if ($kiemTra->fetchColumn()) {
            return;
        }

        $them = $pdo->prepare("
            INSERT INTO thong_bao
                (nguoi_dung_id, su_kien_id, tieu_de, noi_dung, loai)
            VALUES
                (:nguoi_dung_id, :su_kien_id, :tieu_de, :noi_dung, :loai)
        ");

        $them->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'su_kien_id' => $suKienId,
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
            'loai' => $loai
        ]);
    }
}
