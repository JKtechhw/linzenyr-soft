CREATE TABLE roles (
    roleID INT PRIMARY KEY,
    name VARCHAR(255)
);

CREATE TABLE users (
    userID INT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    login VARCHAR(255),
	password VARCHAR(255),
    role INT,
    FOREIGN KEY (role) REFERENCES roles(roleID)
);

CREATE TABLE articles (
    articleID INT PRIMARY KEY,
    title VARCHAR(255),
    author INT,
    banner VARCHAR(255) DEFAULT NULL,
    text TEXT,
    status INT,
    FOREIGN KEY (author) REFERENCES users(userID)
);

CREATE TABLE validations (
    validationID INT PRIMARY KEY,
    article INT,
    redactor INT,
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (redactor) REFERENCES users(userID)
);

CREATE TABLE reviews (
    reviewID INT PRIMARY KEY,
    validation INT,
    text TEXT,
    reviewer INT,
    FOREIGN KEY (validation) REFERENCES validations(validationID),
    FOREIGN KEY (reviewer) REFERENCES users(userID)
);

CREATE TABLE editions (
    editionID INT PRIMARY KEY,
    date DATE,
    basename VARCHAR(255),
    title VARCHAR(255)
);

CREATE TABLE article_edition (
    `article_editionID` INT PRIMARY KEY,
    `article` INT,
    `editionID` INT,
    `order` INT,
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (editionID) REFERENCES editions(editionID)
);

CREATE TABLE tags (
    tagID INT PRIMARY KEY,
    name VARCHAR(255)
);

CREATE TABLE article_tag (
    article INT,
    tag INT,
    PRIMARY KEY (article, tag),
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (tag) REFERENCES tags(tagID)
);