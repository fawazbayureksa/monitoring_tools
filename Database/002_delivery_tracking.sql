-- 002: Feature "Monitoring Pengiriman ke Site"
-- Waktu alat dirilep toolkeeper (aksi "Release"), dipakai menghitung
-- umur pengiriman. NULL = request lama sebelum fitur ini.
ALTER TABLE requests ADD COLUMN shipped_at DATETIME NULL AFTER `status`;
