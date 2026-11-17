-- 003: Add 'supervisor' role to users table
ALTER TABLE `users` MODIFY COLUMN `role` ENUM('master','admin','leader','user','supervisor') DEFAULT 'user';
