USE master;
GO

-- Закриваємо всі активні з'єднання з базою даних
IF DB_ID('Encyclopedia') IS NOT NULL
BEGIN
    ALTER DATABASE Encyclopedia SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
    DROP DATABASE Encyclopedia;
END
GO

CREATE DATABASE Encyclopedia;
GO

USE Encyclopedia;
GO

-- =============================================
-- ОСНОВНІ ТАБЛИЦІ
-- =============================================

-- Таблиця студій
CREATE TABLE Studio (
    StudioID INT IDENTITY(1,1) PRIMARY KEY,
    Name NVARCHAR(100) NOT NULL UNIQUE,
    NameUA NVARCHAR(100),
    Country NVARCHAR(50),
    Founded INT CHECK (Founded BETWEEN 1900 AND YEAR(GETDATE())),
    Employees INT CHECK (Employees >= 0),
    Description NVARCHAR(MAX),
    LogoUrl NVARCHAR(500),
    Website NVARCHAR(200),
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME DEFAULT GETDATE()
);
GO

-- Таблиця жанрів
CREATE TABLE Genre (
    GenreID INT IDENTITY(1,1) PRIMARY KEY,
    Name NVARCHAR(50) NOT NULL UNIQUE,
    NameUA NVARCHAR(50),
    NameEN NVARCHAR(50),
    Description NVARCHAR(500),
    Color NVARCHAR(7) DEFAULT '#3b82f6',
    Icon NVARCHAR(50),
    CreatedAt DATETIME DEFAULT GETDATE()
);
GO

-- Таблиця авторів манги
CREATE TABLE Author (
    AuthorID INT IDENTITY(1,1) PRIMARY KEY,
    Name NVARCHAR(100) NOT NULL,
    NameUA NVARCHAR(100),
    NameEN NVARCHAR(100),
    BirthDate DATE,
    BirthPlace NVARCHAR(100),
    DeathDate DATE,
    Gender NVARCHAR(10) CHECK (Gender IN ('Male', 'Female', 'Other')),
    Biography NVARCHAR(MAX),
    ImageUrl NVARCHAR(500),
    Website NVARCHAR(200),
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME DEFAULT GETDATE()
);
GO

-- Таблиця аніме
CREATE TABLE Anime (
    AnimeID INT IDENTITY(1,1) PRIMARY KEY,
    Title NVARCHAR(200) NOT NULL,
    TitleUA NVARCHAR(200),
    TitleEN NVARCHAR(200),
    TitleJP NVARCHAR(200),
    TitleRomaji NVARCHAR(200),
    [Year] INT CHECK ([Year] BETWEEN 1960 AND YEAR(GETDATE()) + 2),
    Season NVARCHAR(20) CHECK (Season IN ('Winter', 'Spring', 'Summer', 'Fall', NULL)),
    Episodes INT CHECK (Episodes >= 0),
    EpisodeDuration INT CHECK (EpisodeDuration >= 0),
    Type NVARCHAR(20) CHECK (Type IN ('TV', 'Movie', 'OVA', 'ONA', 'Special', 'Music')),
    Status NVARCHAR(20) CHECK (Status IN ('Ongoing', 'Completed', 'Announced', 'Hiatus', 'Cancelled')),
    Source NVARCHAR(50) CHECK (Source IN ('Original', 'Manga', 'Light Novel', 'Visual Novel', 'Game', 'Book', 'Other')),
    RatingMPAA NVARCHAR(10) CHECK (RatingMPAA IN ('G', 'PG', 'PG-13', 'R', 'R+', 'Rx', NULL)),
    Description NVARCHAR(MAX),
    DescriptionUA NVARCHAR(MAX),
    DescriptionEN NVARCHAR(MAX),
    StudioID INT FOREIGN KEY REFERENCES Studio(StudioID) ON DELETE SET NULL,
    CoverUrl NVARCHAR(500),
    PosterUrl NVARCHAR(500),
    TrailerUrl NVARCHAR(500),
    BannerUrl NVARCHAR(500),
    NextEpisode DATETIME,
    Views INT DEFAULT 0,
    Favorites INT DEFAULT 0,
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME DEFAULT GETDATE()
);
GO

-- (file continues with full original content)
-- For full original script see the rest of this file.
