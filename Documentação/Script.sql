--CREATE DATABASE league;
--USE league;

CREATE TABLE tb_seasons(
	id INT IDENTITY(0,1) PRIMARY KEY,
	status INT NOT NULL DEFAULT 1
);

CREATE TABLE tb_teams(
	id INT IDENTITY(1,1) PRIMARY KEY,
	name VARCHAR(50) NOT NULL UNIQUE, 
	country VARCHAR(50) NOT NULL,
	countryinitials CHAR(3) NOT NULL,
	rating FLOAT NOT NULL
);

CREATE TABLE tb_competitions(
	id INT IDENTITY(1,1) PRIMARY KEY,
	name VARCHAR(50) NOT NULL UNIQUE, 
	rules TEXT
);

CREATE TABLE tb_groupstages(
	id INT IDENTITY(1,1) PRIMARY KEY,
	season INT NOT NULL,
	competition INT NOT NULL,
	nrgroup INT NOT NULL,
	team INT NOT NULL,
	points INT NOT NULL DEFAULT 0,
	matches INT NOT NULL DEFAULT 0,
	wins INT NOT NULL DEFAULT 0,
	draws INT NOT NULL DEFAULT 0,
	looses INT NOT NULL DEFAULT 0,
	GF INT NOT NULL DEFAULT 0,
	GA INT NOT NULL DEFAULT 0,
	GD INT NOT NULL DEFAULT 0,
	nrpercent FLOAT NOT NULL DEFAULT 0.00

	CONSTRAINT fk_season FOREIGN KEY(season) REFERENCES tb_seasons(id),
	CONSTRAINT fk_competition FOREIGN KEY(competition) REFERENCES tb_competitions(id),
	CONSTRAINT fk_team FOREIGN KEY(team) REFERENCES tb_teams(id),
);

CREATE TABLE tb_groupmatches(
	id INT IDENTITY(1,1) PRIMARY KEY,
	season INT NOT NULL,
	competition INT NOT NULL,
	nrgroup INT NOT NULL,
	nrround	INT NOT NULL DEFAULT 1,
	team1 INT NOT NULL,
	team2 INT NOT NULL,
	goals1 INT,
	goals2 INT,
	matchtime CHAR(11),
	isfinished BIT DEFAULT 0,

	CONSTRAINT fk_season_tbgroupstage FOREIGN KEY(season) REFERENCES tb_seasons(id),
	CONSTRAINT fk_competition_tbgroupstage FOREIGN KEY(competition) REFERENCES tb_competitions(id),
	CONSTRAINT fk_team1_tbgroupstage FOREIGN KEY(team1) REFERENCES tb_teams(id),
	CONSTRAINT fk_team2_tbgroupstage FOREIGN KEY(team2) REFERENCES tb_teams(id),
);

CREATE TABLE tb_playoffs(
	id INT IDENTITY(1,1) PRIMARY KEY,

	season INT NOT NULL,
	competition INT NOT NULL,

	stage INT NOT NULL DEFAULT 1,
	match INT NOT NULL DEFAULT 1,
	team1 INT NOT NULL,
	team2 INT NOT NULL,
	goals1 INT,
	goals2 INT,
	matchtime CHAR(11),
	isfinished BIT DEFAULT 0,

	CONSTRAINT fk_season_tbplayoffs FOREIGN KEY(season) REFERENCES tb_seasons(id),
	CONSTRAINT fk_competition_tbplayoffs FOREIGN KEY(competition) REFERENCES tb_competitions(id),
	CONSTRAINT fk_team1_tbplayoffs FOREIGN KEY(team1) REFERENCES tb_teams(id),
	CONSTRAINT fk_team2_tbplayoffs FOREIGN KEY(team2) REFERENCES tb_teams(id)
);


INSERT INTO tb_teams (name, country, countryinitials, rating) VALUES 


('Torín', 'Paraguai', 'PRY', 2), ('Jardim Mayer', 'Paraguai', 'PRY', 2),


 ('Corrientes', 'Chile', 'CHL', 2.5),  
('Vila Maulin', 'Chile', 'CHL', 2.5), ('Vila Amarylla', 'Chile', 'CHL', 2.5),

('Vila Hayes', 'Colômbia', 'COL', 2.5), ('', 'Colômbia', 'COL', 3), ('Vila Colmuya', 'Colômbia', 'COL', 2.5),
('Jardim Guayaquil', 'Colômbia', 'COL', 2.5), ('', 'Colômbia', 'COL', 3),


('Vila Potosí', 'Uruguai', 'URY', 2.5),
 


INSERT INTO tb_competitions(name, rules) VALUES 

('Campeonato Brasileiro Série A', 'Participantes: 20 
 Fase 1: 1 grupo (ida e volta = 38 rodadas), 17º, 18º, 19º, 20º rebaixados'),

('Campeonato Brasileiro Série B', 'Participantes: 20 
 Fase 1: 2 grupos (ida e volta = 18 rodadas), 1º ao 4º se classificam, 8º, 9º, 10º rebaixados 
 Fase 2: eliminatórias (ida e volta), 4 vencedores são promovidos'),

('Campeonato Brasileiro Série C', 'Participantes: 24 
 Fase 1: 4 grupos (ida = 5 rodadas), 1º e 2º classificados 
 Fase 2: 1 grupo (ida e volta = 14 rodadas), 1º ao 6º promovidos'),

('Copa do Brasil', 'Participantes: 64 
 6 fases eliminatórias (ida e volta)'),

('Torneio São Bernardo', 'Participantes: 32 
 Fase 1: 4 grupos (ida = 7 rodadas), 1º ao 4º se classificam 
 Fase 2: eliminatórias (ida)'),

('Torneio São Paulo', 'Participantes: 32 
 Fase 1: 4 grupos (ida = 7 rodadas), 1º ao 4º se classificam 
 Fase 2: eliminatórias (ida)'),

('Recopa - Brasil', 'Participantes: 2 
 Vagas: Campeão Nacional, Campeão Copa Nacional'),

('Libertadores', 'Participantes: 32 
 Fase 1: pré-libertadores eliminatória (ida e volta) Vagas: (5º e 6º colocado de nacionais de Brasil, Argentina, México e Estados Unidos)
Fase 2; 8 grupos (ida e volta = 6 rodadas), 1º e 2º se classificam 
Fase 3: eliminatórias (ida e volta) 

Vagas: 4 primieiros nacional (Brasil, Argentina, México, Estados Unidos) = 16
+ 1 campeão copa nacional (Brasil, Argentina, México, Estados Unidos) = 4
+ 1 campeão última libertadores + 1 campeão última sul-americana = 2
+ 4 pré-libertadores = 4'),

('Sul-Americana', 'Participantes: 32 
 Fases: eliminatórias (ida e volta) 
Vagas: Brasil 7, México 5, Argentina 5, Estados Unidos 5, Liga Internacional 10'),

('Recopa - Internacional', 'Participantes: 2 
 Vagas: Campeão Libertadores, Campeão Sul-Americana'),

('Liga Internacional', 'Participantes: 20 
 Fase: 1 grupo (ida = 19 rodadas)'),

('Campeonato Argentino', 'Participantes: 16 
 Fase: 1 grupo (ida e volta = 30 rodadas)'),
('Copa da Argentina', 'Participantes/; 16 
 eliminatórias ida e volta'),
('Recopa - Argentina', 'Participantes: 2 (último campeoão nacional e último campeão da copa nacional)'),

('Campeonato Mexicano', 'Participantes: 16 
 Fase: 1 grupo (ida e volta = 30 rodadas)'),
('Copa do México', 'Participantes/; 16 
 eliminatórias ida e volta'),
('Recopa - México', 'Participantes: 2 (último campeoão nacional e último campeão da copa nacional)'),

('Campeonato Americano', 'Participantes: 16 
 Fase: 1 grupo (ida e volta = 30 rodadas)'),
('Copa Americana', 'Participantes/; 16 
 eliminatórias ida e volta'),
('Recopa - Estados Unidos', 'Participantes: 2 (último campeoão nacional e último campeão da copa nacional)');

INSERT INTO tb_seasons (status) VALUES (2);

-- Brasilero S�rie A
INSERT INTO tb_groupstages (season, competition, nrgroup, team, points, matches, wins, draws, looses, GF, GA, GD, nrpercent) VALUES 
(0, 1, 1, 1, 20, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 2, 19, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 3, 18, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 4, 17, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 5, 16, 38, 38, 0, 0, 100, 100, 0, 100),

(0, 1, 1, 17, 15, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 18, 14, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 19, 13, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 33, 12, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 20, 11, 38, 38, 0, 0, 100, 100, 0, 100),

(0, 1, 1, 8, 10, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 9, 9, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 10, 8, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 23, 7, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 24, 6, 38, 38, 0, 0, 100, 100, 0, 100),

(0, 1, 1, 25, 5, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 27, 4, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 36, 3, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 30, 2, 38, 38, 0, 0, 100, 100, 0, 100),
(0, 1, 1, 31, 1, 38, 38, 0, 0, 100, 100, 0, 100);

-- Brasileiro B
INSERT INTO tb_groupstages (season, competition, nrgroup, team, points, matches, wins, draws, looses, GF, GA, GD, nrpercent) VALUES

(0, 2, 1, 37, 10, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  38, 9, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  39, 8, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  40, 7, 19, 19, 0, 0, 0, 0, 0, 100),

(0, 2, 1,  41, 6, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  42, 5, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  21, 4, 19, 19, 0, 0, 0, 0, 0, 100),

(0, 2, 1,  13, 3, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  14, 2, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 1,  15, 1, 19, 19, 0, 0, 0, 0, 0, 100),

(0, 2, 2,  6, 10, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  7, 9, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  16, 8, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  12, 7, 19, 19, 0, 0, 0, 0, 0, 100),

(0, 2, 2,  48, 6, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  52, 5, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  35, 4, 19, 19, 0, 0, 0, 0, 0, 100),

(0, 2, 2,  43, 3, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  44, 2, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 2, 2,  45, 1, 19, 19, 0, 0, 0, 0, 0, 100);

INSERT INTO tb_playoffs (season, competition, stage, match, team1, team2, goals1, goals2, matchtime) VALUES

-- Quartas de Final

(0, 2, 4, 1, 37, 38, 2, 0, 'DOM - 16:00'),
(0, 2, 4, 2, 38, 37, 1, 1, 'DOM - 16:00'),

(0, 2, 4, 1, 39, 40, 2, 1, 'DOM - 16:00'),
(0, 2, 4, 2, 40, 39, 1, 1, 'DOM - 16:00'),

(0, 2, 4, 1, 6, 7, 4, 1, 'DOM - 16:00'),
(0, 2, 4, 2, 7, 6, 1, 1, 'DOM - 16:00'),

(0, 2, 4, 1, 16, 12, 3, 1, 'DOM - 16:00'),
(0, 2, 4, 2, 12, 16, 1, 1, 'DOM - 16:00'),

-- Semi-final

(0, 2, 5, 1, 37, 39, 3, 0, 'DOM - 16:00'),
(0, 2, 5, 2, 39, 37, 1, 0, 'DOM - 16:00'),

(0, 2, 5, 1, 6, 16, 0, 2, 'DOM - 16:00'),
(0, 2, 5, 2, 16, 6, 4, 0, 'DOM - 16:00')

INSERT INTO tb_groupstages (season, competition, nrgroup, team, points, matches, wins, draws, looses, GF, GA, GD, nrpercent) VALUES
-- Brasileiro C
(0, 3, 1,  34, 6, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  11, 5, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  22, 4, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 1,  26, 3, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  28, 2, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  32, 1, 5, 5, 0, 0, 1, 0, 1, 100),


(0, 3, 1,  46, 6, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  47, 5, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  49, 4, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 1,  50, 3, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  51, 2, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 1,  53, 1, 5, 5, 0, 0, 1, 0, 1, 100),


(0, 3, 2,  54, 6, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  55, 5, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  56, 4, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 2,  57, 3, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  58, 2, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  59, 1, 5, 5, 0, 0, 1, 0, 1, 100),


(0, 3, 2,  60, 6, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  61, 5, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  62, 4, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 2,  63, 3, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  64, 2, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 2,  29, 1, 5, 5, 0, 0, 1, 0, 1, 100),

-- Fase 2 (grupo de 8)

(0, 3, 3,  34, 8, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 3,  11, 7, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 3,  46, 6, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 3,  47, 5, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 3,  54, 4, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 3,  55, 3, 5, 5, 0, 0, 1, 0, 1, 100),

(0, 3, 3,  60, 2, 5, 5, 0, 0, 1, 0, 1, 100),
(0, 3, 3,  61, 2, 5, 5, 0, 0, 1, 0, 1, 100);

INSERT INTO tb_playoffs (season, competition, stage, match, team1, team2, goals1, goals2, matchtime) VALUES

(0, 4, 6, 1, 1, 2, 0, 2, 'DOM - 17:00'), --Copa do Brasil

(0, 8, 6, 1, 79, 78, 4, 0, 'DOM - 17:00'), --Libertadores
(0, 9, 6, 1, 81, 82, 2, 1, 'QUA - 21:00'), --Sul-Americana

(0, 13, 6, 1, 90, 96, 2, 1, 'DOM - 17:00'), --Copa da Argentina
(0, 16, 6, 1, 101, 102, 3, 0, 'DOM - 17:00'), --Copa do México
(0, 19, 6, 1, 117, 127, 4, 0, 'DOM - 17:00'); --Copa Americana

INSERT INTO tb_groupstages (season, competition, nrgroup, team, points, matches, wins, draws, looses, GF, GA, GD, nrpercent) VALUES 

-- Liga Internacional
(0, 11, 1,  65, 21, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  66, 20, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  67, 19, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  68, 18, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  69, 17, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  70, 16, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  71, 15, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  72, 14, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  73, 13, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  74, 12, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  75, 11, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  76, 10, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  77, 9, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  78, 8, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  79, 7, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  80, 6, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  81, 5, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  82, 4, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  83, 3, 19, 19, 0, 0, 0, 0, 0, 100),
(0, 11, 1,  84, 2, 19, 19, 0, 0, 0, 0, 0, 100),

-- Campeonato Argentino
(0, 12, 1,  85, 16, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  86, 15, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  87, 14, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  88, 13, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  89, 12, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  90, 11, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  91, 10, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  92, 9, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  93, 8, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  94, 7, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  95, 6, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  96, 5, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  97, 4, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  98, 3, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  99, 2, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 12, 1,  100, 1, 30, 0, 0, 0, 0, 0, 0, 100),

-- Campeonato Mexicano

(0, 15, 1,  101, 16, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  102, 15, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  103, 14, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  104, 13, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  105, 12, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  106, 11, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  107, 10, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  108, 9, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  109, 8, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  110, 7, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  111, 6, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  112, 5, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  113, 4, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  114, 3, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  115, 2, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 15, 1,  116, 1, 30, 0, 0, 0, 0, 0, 0, 100),

-- Campeonato Americano

(0, 18, 1,  117, 16, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  118, 15, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  119, 14, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  120, 13, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  121, 12, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  122, 11, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  123, 10, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  124, 9, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  125, 8, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  126, 7, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  127, 6, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  128, 5, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  129, 4, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  130, 3, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  131, 2, 30, 0, 0, 0, 0, 0, 0, 100),
(0, 18, 1,  132, 1, 30, 0, 0, 0, 0, 0, 0, 100);