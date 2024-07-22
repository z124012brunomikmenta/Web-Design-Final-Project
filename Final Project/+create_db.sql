DROP DATABASE IF EXISTS games_database;
CREATE DATABASE games_database;
USE games_database;


CREATE TABLE gamesMainList (
    gameID  INT   UNSIGNED     NOT NULL    AUTO_INCREMENT,
    gameTitle   VARCHAR(255)    NOT NULL,
    gameReleaseYear YEAR    NOT NULL,
    gameGenres  VARCHAR(255)    NOT NULL,
    gameOverallRating  DECIMAL(2,1)  NULL,
	gamePlayCount  INT   UNSIGNED     NULL 	DEFAULT 0,
    PRIMARY KEY (gameID)
);

CREATE TABLE users (
    userID    INT	UNSIGNED     NOT NULL    AUTO_INCREMENT,
    userName    VARCHAR(255)	NOT NULL,
    email   VARCHAR(255)    NOT NULL,
    password    VARCHAR(255) NOT NULL,
    PRIMARY KEY (userID)
);

CREATE TABLE gamesUserList (
    listID  INT		UNSIGNED	NOT NULL	AUTO_INCREMENT,
	userID  INT 	UNSIGNED    NOT NULL,
	gameID  INT		UNSIGNED	NOT NULL,
    gameUserRating  TINYINT  NULL,
    reviewComment   VARCHAR(280)    NULL,
    statusInList    TINYINT  NOT NULL,
	PRIMARY KEY (listID),
	FOREIGN KEY (gameID) REFERENCES gamesMainList(gameID),
	FOREIGN KEY (userID) REFERENCES users(userID)
);



INSERT INTO gamesMainList (gameID, gameTitle, gameReleaseYear, gameGenres, gameOverallRating) VALUES
(1, 'The Legend of Zelda: Ocarina of Time', 1998, 'Open-World Action', 9.1),
(2, 'Super Mario Galaxy', 2007, '3D Platformer', 9.1),
(3, 'Red Dead Redemption 2', 2018, 'Open-World Action', 8.8),
(4, 'Baldur''s Gate 3', 2023, 'Western RPG', 8.9),
(5, 'Stardew Valley', 2016, 'RPG, Farm Life Sim, Indie', 8.8),
(6, 'Celeste', 2018, '2D Platformer, Indie', 8.7),
(7, 'Assassins''s Creed Unity', 2014, 'Open-World Action', 5.7),
(8, 'Street Fighter 6', 2023, '2D Fighting', 7.0),
(9, 'Trails to Azure', 2011, 'JRPG', 8.1),
(10, 'FIFA 18', 2017, 'Soccer Sim', 3.9),
(11, 'The Witcher 3', 2015, 'Action RPG', 9.2),
(12, 'Elden Ring', 2022, 'Action RPG', 8.1),
(13, 'Castle Crashers', 2008, '2D Beat-''Em-Up', 8.5),
(14, 'Outer Wilds', 2019, 'Open-World Action', 8.8),
(15, 'New Super Mario Bros. Wii', 2009, '2D Platformer', 8.3),
(16, 'Resident Evil 4', 2005, 'Survival, Horror', 9.1),
(17, 'Risk of Rain 2', 2020, 'Third Person Shooter, Roguelite', 8.4),
(18, 'Forza Horizon 3', 2016, 'Auto Racing Sim', 8.2),
(19, 'Monster Hunter: World', 2018, 'Action RPG', 7.7),
(20, 'Castlevania: Symphony of the Night', 1997, 'Metroidvania', 8.8);
