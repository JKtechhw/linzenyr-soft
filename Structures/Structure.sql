CREATE TABLE roles (
    roleID INT NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE users (
    userID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    avatar VARCHAR(255) DEFAULT NULL,
    login VARCHAR(255),
	password VARCHAR(255),
    role INT,
    FOREIGN KEY (role) REFERENCES roles(roleID)
);

CREATE TABLE articles (
    articleID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR(255),
    author INT,
    banner VARCHAR(255) DEFAULT NULL,
    text TEXT,
    status INT,
    FOREIGN KEY (author) REFERENCES users(userID)
);

CREATE TABLE validations (
    validationID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    article INT,
    redactor INT DEFAULT NULL,
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (redactor) REFERENCES users(userID)
);

CREATE TABLE reviews (
    reviewID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    validation INT,
    text TEXT,
    reviewer INT,
    FOREIGN KEY (validation) REFERENCES validations(validationID),
    FOREIGN KEY (reviewer) REFERENCES users(userID)
);

CREATE TABLE editions (
    editionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date DATE,
    basename VARCHAR(255),
    title VARCHAR(255)
);

CREATE TABLE article_edition (
    `article_editionID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `article` INT,
    `editionID` INT,
    `order` INT,
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (editionID) REFERENCES editions(editionID)
);

CREATE TABLE tags (
    tagID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

CREATE TABLE article_tag (
    article INT,
    tag INT,
    PRIMARY KEY (article, tag),
    FOREIGN KEY (article) REFERENCES articles(articleID),
    FOREIGN KEY (tag) REFERENCES tags(tagID)
);

CREATE TABLE helpdesk (
    helpdeskID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    user INT,
    solved INT DEFAULT NULL,
    FOREIGN KEY (user) REFERENCES users(userID)
);

CREATE TABLE messages (
    messageID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP NOT NULL DEFAULT NOW(),
    helpdesk INT,
    author INT,
    readed INT DEFAULT NULL,
    message TEXT NOT NULL,
    FOREIGN KEY (helpdesk) REFERENCES helpdesk(helpdeskID),
    FOREIGN KEY (author) REFERENCES users(userID)
);