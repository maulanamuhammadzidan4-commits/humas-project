-- Run once against an existing database after taking a backup.
-- The legacy column is dropped only after every status has been copied and verified.

DELIMITER $$

DROP PROCEDURE IF EXISTS `migrate_perusahaan_mou`$$

CREATE PROCEDURE `migrate_perusahaan_mou`()
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'perusahaan'
      AND COLUMN_NAME = 'status_mou'
  ) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Kolom perusahaan.status_mou tidak ditemukan; migrasi dibatalkan.';
  END IF;

  IF EXISTS (
    SELECT 1
    FROM `perusahaan`
    WHERE `status_mou` IS NOT NULL
      AND BINARY `status_mou` NOT IN (BINARY 'Proses', BINARY 'Aktif', BINARY 'Kadaluarsa')
  ) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Ada status MoU lama di luar enum yang didukung; perbaiki data sebelum migrasi.';
  END IF;

  CREATE TABLE IF NOT EXISTS `perusahaan_mou` (
    `perusahaan_id` int NOT NULL,
    `status` enum('Proses','Aktif','Kadaluarsa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Proses',
    `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_size` bigint unsigned DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`perusahaan_id`),
    CONSTRAINT `fk_perusahaan_mou_perusahaan`
      FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id`)
      ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  INSERT INTO `perusahaan_mou` (`perusahaan_id`, `status`)
  SELECT `id`, COALESCE(NULLIF(`status_mou`, ''), 'Proses')
  FROM `perusahaan`
  ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);

  IF EXISTS (
    SELECT 1
    FROM `perusahaan` AS p
    LEFT JOIN `perusahaan_mou` AS pm ON pm.`perusahaan_id` = p.`id`
    WHERE pm.`perusahaan_id` IS NULL
       OR BINARY pm.`status` <> BINARY COALESCE(NULLIF(p.`status_mou`, ''), 'Proses')
  ) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Verifikasi migrasi status MoU gagal; kolom lama tidak dihapus.';
  END IF;

  ALTER TABLE `perusahaan` DROP COLUMN `status_mou`;
END$$

CALL `migrate_perusahaan_mou`()$$
DROP PROCEDURE `migrate_perusahaan_mou`$$

DELIMITER ;
