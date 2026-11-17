-- 004: Seed Supervisor (SPV) accounts
-- Pastikan kolom role sudah mendukung 'supervisor'
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('master','admin','leader','user','supervisor') DEFAULT 'user';

-- Masukkan akun SPV (Idempotent: menggunakan ON DUPLICATE KEY UPDATE)
INSERT INTO `users` (`nrp`, `username`, `name`, `role`, `password`) VALUES
('spv', 'spv', 'Supervisor', 'supervisor', 'spv123'),
('spv_field', 'spv_field', 'Supervisor Field', 'supervisor', 'spv123')
ON DUPLICATE KEY UPDATE 
`name` = VALUES(`name`),
`role` = VALUES(`role`),
`password` = VALUES(`password`);
