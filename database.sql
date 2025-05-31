-- Vytvoření databáze (pokud ještě neexistuje)
CREATE DATABASE IF NOT EXISTS `fitness_tracker_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci;
USE `fitness_tracker_db`;

-- Tabulka Uzivatele
CREATE TABLE `Uzivatele` (
  `id_uzivatele` INT AUTO_INCREMENT PRIMARY KEY,
  `jmeno_uzivatele` VARCHAR(50) NOT NULL UNIQUE,
  `heslo_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `typ_uzivatele` ENUM('bezny', 'admin') NOT NULL DEFAULT 'bezny',
  `datum_registrace` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `posledni_prihlaseni` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Tabulka Treninky
CREATE TABLE `Treninky` (
  `id_treninku` INT AUTO_INCREMENT PRIMARY KEY,
  `id_uzivatele` INT NOT NULL,
  `nazev_treninku` VARCHAR(100) NOT NULL,
  `datum_treninku` DATE NOT NULL,
  `cas_zacatku` TIME NULL,
  `doba_trvani_celkem_min` INT NULL,
  `poznamky_k_treninku` TEXT NULL,
  `vytvoreno_kdy` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `upraveno_kdy` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_uzivatele`) REFERENCES `Uzivatele`(`id_uzivatele`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Tabulka Cviky
CREATE TABLE `Cviky` (
  `id_cviku` INT AUTO_INCREMENT PRIMARY KEY,
  `nazev_cviku` VARCHAR(100) NOT NULL UNIQUE,
  `popis_cviku` TEXT,
  `svalova_partie` VARCHAR(255),
  `typ_cviku` ENUM('silovy', 'kardio', 'strecing', 'intervalovy', 'ostatni') NOT NULL DEFAULT 'ostatni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Tabulka Treninkove_Cviky
CREATE TABLE `Treninkove_Cviky` (
  `id_treninkoveho_cviku` INT AUTO_INCREMENT PRIMARY KEY,
  `id_treninku` INT NOT NULL,
  `id_cviku` INT NOT NULL,
  `poradi_cviku` INT NULL,
  `serie` INT NULL,
  `opakovani` VARCHAR(50) NULL,
  `vaha_kg` DECIMAL(6,2) NULL,
  `doba_trvani_min` INT NULL,
  `vzdalenost_km` DECIMAL(6,2) NULL,
  `poznamky_cviku` TEXT NULL,
  FOREIGN KEY (`id_treninku`) REFERENCES `Treninky`(`id_treninku`) ON DELETE CASCADE,
  FOREIGN KEY (`id_cviku`) REFERENCES `Cviky`(`id_cviku`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Vkládání ukázkových dat:

-- Uživatelé (heslo 'heslo123' - nutno hashovat v aplikaci! např. password_hash('heslo123', PASSWORD_DEFAULT))
INSERT INTO `Uzivatele` (`jmeno_uzivatele`, `heslo_hash`, `email`, `typ_uzivatele`) VALUES
('testUser', '$2y$10$examplehash1forTestUser123', 'user@example.com', 'bezny'),
('adminUser', '$2y$10$examplehash2forAdminUser456', 'admin@example.com', 'admin');

-- Cviky (katalog)
INSERT INTO `Cviky` (`nazev_cviku`, `popis_cviku`, `svalova_partie`, `typ_cviku`) VALUES
('Bench Press', 'Tlak s velkou činkou vleže na rovné lavici.', 'Hrudník, Triceps, Ramena', 'silovy'),
('Dřep s vlastní vahou', 'Základní dřep bez přidané zátěže.', 'Nohy, Hýždě', 'silovy'),
('Mrtvý tah', 'Zvedání velké činky ze země do vzpřímeného postoje.', 'Záda, Nohy, Hýždě', 'silovy'),
('Shyby nadhmatem', 'Přitahování těla k hrazdě nadhmatem.', 'Záda, Biceps', 'silovy'),
('Kliky na bradlech', 'Tlaky na bradlech pro hrudník a triceps.', 'Hrudník, Triceps', 'silovy'),
('Běh na páse', 'Vytrvalostní běh na běžeckém trenažéru.', 'Kardio, Nohy', 'kardio'),
('Jízda na rotopedu', 'Vytrvalostní jízda na stacionárním kole.', 'Kardio, Nohy', 'kardio'),
('Angličáky (Burpees)', 'Komplexní cvik zapojující celé tělo.', 'Celé tělo', 'intervalovy'),
('Protažení hamstringů (předklon)', 'Statické protažení zadní strany stehen.', 'Hamstringy', 'strecing'),
('Plank (Prkno)', 'Statická výdrž pro posílení středu těla.', 'Střed těla (Core)', 'silovy'),
('Kliky', 'Základní cvik s vlastní vahou na horní část těla.', 'Hrudník, Triceps, Ramena', 'silovy'),
('Výpady', 'Cvik na nohy a hýždě, s vlastní vahou nebo zátěží.', 'Nohy, Hýždě', 'silovy'),
('Skákání přes švihadlo', 'Kardio cvičení zlepšující koordinaci a výdrž.', 'Kardio, Lýtka', 'kardio'),
('Jóga - Pozdrav slunci', 'Sestava plynulých jógových pozic.', 'Celé tělo', 'strecing');

-- Tréninky (příklady)
INSERT INTO `Treninky` (`id_uzivatele`, `nazev_treninku`, `datum_treninku`, `cas_zacatku`, `doba_trvani_celkem_min`, `poznamky_k_treninku`) VALUES
(1, 'Ranní posilovna - Full Body', '2025-05-30', '08:00:00', 90, 'První trénink po delší pauze.'),
(1, 'Večerní běh v parku', '2025-05-30', '19:30:00', 45, 'Příjemné počasí, lehký běh.'),
(2, 'Odpolední kardio a strečink', '2025-05-31', '15:00:00', 60, 'Kombinace rotopedu a jógy.');

-- Cviky v trénincích (příklady)
-- Trénink 1 (Full Body, id_treninku = 1)
INSERT INTO `Treninkove_Cviky` (`id_treninku`, `id_cviku`, `poradi_cviku`, `serie`, `opakovani`, `vaha_kg`, `poznamky_cviku`) VALUES
(1, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Dřep s vlastní vahou'), 1, 3, '15', NULL, 'Zahřátí'),
(1, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Bench Press'), 2, 4, '8-10', 60.00, 'Postupně zvyšovat váhu'),
(1, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Mrtvý tah'), 3, 1, '5', 80.00, 'Těžká série, důraz na techniku'),
(1, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Shyby nadhmatem'), 4, 3, 'Max', NULL, 'Do selhání'),
(1, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Plank (Prkno)'), 5, 3, '60s', NULL, 'Držet 60 sekund');

-- Trénink 2 (Běh, id_treninku = 2)
INSERT INTO `Treninkove_Cviky` (`id_treninku`, `id_cviku`, `poradi_cviku`, `doba_trvani_min`, `vzdalenost_km`, `poznamky_cviku`) VALUES
(2, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Běh na páse'), 1, 40, 5.5, 'Konstantní tempo');

-- Trénink 3 (Kardio a strečink, id_treninku = 3)
INSERT INTO `Treninkove_Cviky` (`id_treninku`, `id_cviku`, `poradi_cviku`, `doba_trvani_min`, `poznamky_cviku`) VALUES
(3, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Jízda na rotopedu'), 1, 30, 'Střední zátěž'),
(3, (SELECT id_cviku FROM Cviky WHERE nazev_cviku = 'Jóga - Pozdrav slunci'), 2, 25, 'Pomalé a plynulé protažení');