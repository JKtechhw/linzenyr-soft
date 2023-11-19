CREATE TABLE Role (
    id INT PRIMARY KEY,
    název VARCHAR(255)
);

CREATE TABLE Uživatelé (
    id INT PRIMARY KEY,
    Jméno VARCHAR(255),
    Příjmení VARCHAR(255),
    login VARCHAR(255),
	password VARCHAR(255),
    role INT,
    FOREIGN KEY (role) REFERENCES Role(id)
);

CREATE TABLE články (
    id INT PRIMARY KEY,
    Název VARCHAR(255),
    autor INT,
    text TEXT,
    status INT,
    FOREIGN KEY (autor) REFERENCES Uživatelé(id)
);

CREATE TABLE kontrola (
    id INT PRIMARY KEY,
    článek INT,
    redaktor INT,
    FOREIGN KEY (článek) REFERENCES články(id),
    FOREIGN KEY (redaktor) REFERENCES Uživatelé(id)
);

CREATE TABLE recenze (
    id INT PRIMARY KEY,
    kontrola INT,
    text TEXT,
    recenzent INT,
    FOREIGN KEY (kontrola) REFERENCES kontrola(id),
    FOREIGN KEY (recenzent) REFERENCES Uživatelé(id)
);

CREATE TABLE vydání (
    id INT PRIMARY KEY,
    datum DATE,
    označení VARCHAR(255),
    nadpis VARCHAR(255)
);

CREATE TABLE článek_vydání (
    id INT PRIMARY KEY,
    článek INT,
    vydání INT,
    pořadí INT,
    FOREIGN KEY (článek) REFERENCES články(id),
    FOREIGN KEY (vydání) REFERENCES vydání(id)
);

CREATE TABLE tagy (
    id INT PRIMARY KEY,
    název VARCHAR(255)
);

CREATE TABLE článek_tag (
    článek INT,
    tag INT,
    PRIMARY KEY (článek, tag),
    FOREIGN KEY (článek) REFERENCES články(id),
    FOREIGN KEY (tag) REFERENCES tagy(id)
);