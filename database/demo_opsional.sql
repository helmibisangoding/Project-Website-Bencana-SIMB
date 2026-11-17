-- OPSIONAL: hanya untuk database latihan kosong. Seluruh data FIKTIF.
-- Tidak otomatis dijalankan oleh aplikasi. Jangan import berulang.
USE siaga_lereng;
START TRANSACTION;
INSERT INTO incidents (name,event_date,province,district,location,type,status,deaths,injured,missing,damaged_houses,notes) VALUES
('[SIMULASI] Longsor lereng permukiman','2026-02-12','Jawa Barat','Cianjur','Desa fiktif A','Longsoran Translasi','Penanganan',0,3,1,4,'Data demonstrasi fiktif, bukan laporan nyata.');
SET @kejadian = LAST_INSERT_ID();
INSERT INTO shelters (incident_id,name,location,capacity,occupancy,needs) VALUES (@kejadian,'[SIMULASI] Posko Balai Desa','Zona aman fiktif',100,42,'Selimut, air bersih');
SET @posko = LAST_INSERT_ID();
INSERT INTO people (incident_id,shelter_id,name,age,status,last_location,notes) VALUES
(@kejadian,NULL,'Warga Fiktif A',32,'Hilang','Area fiktif timur','Identitas fiktif untuk latihan'),
(@kejadian,@posko,'Warga Fiktif B',25,'Mengungsi','Posko contoh','Identitas fiktif untuk latihan');
INSERT INTO incidents (name,event_date,province,district,location,type,status,deaths,injured,missing,damaged_houses,notes) VALUES
('[SIMULASI] Gerakan tanah','2025-11-03','Jawa Barat','Bogor','Desa fiktif B','Rayapan Tanah','Pemulihan',0,1,0,2,'Data demonstrasi fiktif'),
('[SIMULASI] Runtuhan tebing','2025-04-20','Jawa Barat','Bandung Barat','Jalan fiktif C','Runtuhan Batu','Selesai',0,2,0,0,'Data demonstrasi fiktif'),
('[SIMULASI] Longsor kebun','2024-01-15','Jawa Barat','Garut','Kebun fiktif D','Longsoran Rotasi','Selesai',0,0,0,0,'Data demonstrasi fiktif');
COMMIT;
