-- ===== CORE USERS =====
INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES
('anghel.cristi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Anghel Ionut', 'admin@securebank.local', '+1-555-0100', '2022-01-15 08:30:00'),
('john.smith', '$2y$10$qagduIIzyE8jDo8ZB3xNzOUtAvRdG4/dmrozGLW1by7mmJbBAZ4tG', 'manager', 'John Smith', 'john.smith@securebank.local', '+1-555-0101', '2022-06-20 14:15:00'),
('sarah.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Sarah Johnson', 'sarah.johnson@securebank.local', '+1-555-0102', '2022-08-10 09:45:00'),
('alice.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Alice Williams', 'alice.williams@email.com', '+1-555-0103', '2023-02-28 11:20:00'),
('bob.brown', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Bob Brown', 'bob.brown@email.com', '+1-555-0104', '2023-05-15 16:00:00'),
('charlie.davis', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Charlie Davis', 'charlie.davis@email.com', '+1-555-0105', '2023-09-01 10:30:00'),
('emily.davis', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Emily Davis', 'emily.davis@securebank.local', '+1-555-0106', '2025-09-01 10:00:00');

-- ===== 20 CLIENTS FOR MANAGER1 (id=8-27) =====
INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES
('oliver.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Oliver Williams', 'oliver_m1@email.com', '+1-555-2000', '2024-03-13 08:50:30'),
('emma.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Emma Williams', 'emma_m1@email.com', '+1-555-2001', '2024-05-31 17:56:15'),
('liam.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Liam Williams', 'liam_m1@email.com', '+1-555-2002', '2024-01-17 12:01:18'),
('sophia.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Sophia Williams', 'sophia_m1@email.com', '+1-555-2003', '2024-02-16 01:04:09'),
('noah.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Noah Williams', 'noah_m1@email.com', '+1-555-2004', '2024-04-09 23:09:38'),
('isabella.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Isabella Williams', 'isabella_m1@email.com', '+1-555-2005', '2024-04-06 21:43:06'),
('james.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'James Williams', 'james_m1@email.com', '+1-555-2006', '2024-06-19 04:20:56'),
('mia.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Mia Williams', 'mia_m1@email.com', '+1-555-2007', '2024-05-24 21:23:04'),
('lucas.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Lucas Williams', 'lucas_m1@email.com', '+1-555-2008', '2024-03-07 01:50:40'),
('charlotte.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Charlotte Williams', 'charlotte_m1@email.com', '+1-555-2009', '2024-03-22 07:48:08'),
('henry.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Henry Williams', 'henry_m1@email.com', '+1-555-2010', '2024-06-07 06:08:14'),
('amelia.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Amelia Williams', 'amelia_m1@email.com', '+1-555-2011', '2024-02-26 20:52:57'),
('alexander.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Alexander Williams', 'alexander_m1@email.com', '+1-555-2012', '2024-05-17 17:00:09'),
('harper.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Harper Williams', 'harper_m1@email.com', '+1-555-2013', '2024-04-27 16:00:58'),
('daniel.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Daniel Williams', 'daniel_m1@email.com', '+1-555-2014', '2024-01-19 11:45:35'),
('evelyn.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Evelyn Williams', 'evelyn_m1@email.com', '+1-555-2015', '2024-06-10 23:10:06'),
('matthew.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Matthew Williams', 'matthew_m1@email.com', '+1-555-2016', '2024-05-02 01:14:37'),
('abigail.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Abigail Williams', 'abigail_m1@email.com', '+1-555-2017', '2024-05-24 08:35:58'),
('jackson.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Jackson Williams', 'jackson_m1@email.com', '+1-555-2018', '2024-01-26 21:31:00'),
('ella.williams', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Ella Williams', 'ella_m1@email.com', '+1-555-2019', '2024-03-30 18:01:43');

-- ===== 15 CLIENTS FOR MANAGER2 (id=28-42) =====
INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES
('aiden.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Aiden Johnson', 'aiden_m2@email.com', '+1-555-3000', '2024-03-14 21:32:56'),
('scarlett.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Scarlett Johnson', 'scarlett_m2@email.com', '+1-555-3001', '2024-01-06 19:02:02'),
('sebastian.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Sebastian Johnson', 'sebastian_m2@email.com', '+1-555-3002', '2024-06-07 01:18:38'),
('grace.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Grace Johnson', 'grace_m2@email.com', '+1-555-3003', '2024-04-19 18:21:30'),
('benjamin.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Benjamin Johnson', 'benjamin_m2@email.com', '+1-555-3004', '2024-07-12 10:11:34'),
('chloe.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Chloe Johnson', 'chloe_m2@email.com', '+1-555-3005', '2024-03-07 15:39:11'),
('samuel.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Samuel Johnson', 'samuel_m2@email.com', '+1-555-3006', '2024-07-27 19:43:33'),
('zoey.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Zoey Johnson', 'zoey_m2@email.com', '+1-555-3007', '2024-02-06 09:39:08'),
('joseph.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Joseph Johnson', 'joseph_m2@email.com', '+1-555-3008', '2024-07-27 06:16:18'),
('penelope.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Penelope Johnson', 'penelope_m2@email.com', '+1-555-3009', '2024-04-16 06:09:14'),
('david.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'David Johnson', 'david_m2@email.com', '+1-555-3010', '2024-01-24 22:18:05'),
('riley.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Riley Johnson', 'riley_m2@email.com', '+1-555-3011', '2024-04-04 12:24:03'),
('john.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'John Johnson', 'john_m2@email.com', '+1-555-3012', '2024-07-30 06:42:15'),
('lily.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Lily Johnson', 'lily_m2@email.com', '+1-555-3013', '2024-02-10 07:15:19'),
('owen.johnson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'client', 'Owen Johnson', 'owen_m2@email.com', '+1-555-3014', '2024-04-15 16:55:02');

-- ===== MANAGERS 4-10 (id=43-49) =====
INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES
('michael.brown', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Michael Brown', 'michael.brown@securebank.local', '+1-555-0107', '2023-03-15 10:00:00'),
('jennifer.wilson', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Jennifer Wilson', 'jennifer.wilson@securebank.local', '+1-555-0108', '2024-01-10 11:30:00'),
('robert.taylor', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Robert Taylor', 'robert.taylor@securebank.local', '+1-555-0109', '2025-10-15 09:00:00'),
('lisa.anderson', '$2y$10$I7wdmH0WJks7aho/.aOeo.ieSg.aPYfucvJcFa2lYfCQxP0tqyvW.', 'manager', 'Lisa Anderson', 'lisa.anderson@securebank.local', '+1-555-0110', '2025-12-01 14:00:00'),
('david.thomas', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'David Thomas', 'david.thomas@securebank.local', '+1-555-0111', '2026-01-20 08:45:00'),
('maria.garcia', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'Maria Garcia', 'maria.garcia@securebank.local', '+1-555-0112', '2026-03-01 16:30:00'),
('james.martinez', '$2y$10$Nre1TiG81ZPdlWTiuEO./Of6g/Xq0PS27GzOAHVNc9iZxx/GWuIs6', 'manager', 'James Martinez', 'james.martinez@securebank.local', '+1-555-0113', '2026-04-15 11:15:00');

-- ===== LISA ANDERSON'S CLIENTS (id=50-54) =====
INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES
('salvar.mircea', '$2b$12$MtCR96vVrLeZ6Rz6nukh4uD0V4v9DqrI7JFm9Z.K6SSNEN3x1Zs7S', 'client', 'Salvar Mircea', 'salvar.mircea@email.com', '+1-555-4000', '2026-04-20 10:00:00'),
('anna.anderson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'Anna Anderson', 'anna.anderson@email.com', '+1-555-4001', '2026-04-22 10:00:00'),
('mark.anderson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'Mark Anderson', 'mark.anderson@email.com', '+1-555-4002', '2026-04-25 10:00:00'),
('sophie.anderson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'Sophie Anderson', 'sophie.anderson@email.com', '+1-555-4003', '2026-05-02 10:00:00'),
('lucas.anderson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'Lucas Anderson', 'lucas.anderson@email.com', '+1-555-4004', '2026-05-10 10:00:00');

-- ===== MANAGER ASSIGNMENTS =====
INSERT INTO manager_clients (manager_id, client_user_id) VALUES
(2, 4), (2, 5), (3, 6),
(2, 8), (2, 9), (2, 10), (2, 11), (2, 12), (2, 13), (2, 14), (2, 15), (2, 16), (2, 17), (2, 18), (2, 19), (2, 20), (2, 21), (2, 22), (2, 23), (2, 24), (2, 25), (2, 26), (2, 27),
(3, 28), (3, 29), (3, 30), (3, 31), (3, 32), (3, 33), (3, 34), (3, 35), (3, 36), (3, 37), (3, 38), (3, 39), (3, 40), (3, 41), (3, 42),
(46, 50), (46, 51), (46, 52), (46, 53), (46, 54);
