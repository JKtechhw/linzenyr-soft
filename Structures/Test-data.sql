INSERT INTO roles (roleID, name) VALUES
(1, 'Autor'),
(2, 'Redaktor'),
(3, 'Recenzent'),
(4, 'Šéfredaktor'),
(5, 'Administrátor');

INSERT INTO users (firstname, lastname, login, password, role) VALUES
('Vojtěch', 'Kratochvíl', 'autor', '$2y$10$3VKjih4Up.s0rRxcEfeyau2pfnFukUKqTkg9ZprL1Of6B7ceYOpdu', 1),
('David', 'Malinha', 'redaktor', '$2y$10$9TdN60hsBJc5hMugn3ICjursWFpcYwRWaRboV.xUiv9W.y.O9gypS', 2),
('Dominik', 'Dořák', 'recenzent', '$2y$10$tuq5pnJeZNoR6UDl5IIKquWiObnxdVgn8WiY9fUNKxxUNoyuMTRji', 3),
('Michal', 'Kuřák', 'sefredaktor', '$2y$10$4ZHglYlb.xW2UXZJHsWjA.irlQmlwpODT9nRYSLeLL99zRNFEFebK', 4),
('Rudolf', 'Doležal', 'administrator', '$2y$10$dzjqXvKRGvoqU8RxP/T0Le1pI3TN2PSgd3Yh.2OQQhWANOp/MUTHq', 5),
('Jane', 'Smith', 'jane_smith', '$2y$10$aQ.XAQLtWYae4loR6CIolOieaVSTkBZfbs1eR2VfgGrwd5pifKBjS', 1),
('Bob', 'Johnson', 'bob_johnson', '$2y$10$w/iUe4SIPTd5qlSqvXN4aergLb6py0uiwnr5SNVLsUBDApBVGxLuK', 1),
('Alice', 'Johnson', 'alice_johnson', '$2y$10$t0cVOwgsj9ihTIwoiA85QubkybrdQF/RE/RsPOr9NDDTudW/sBwMC', 1),
('Charlie', 'Brown', 'charlie_brown', '$2y$10$44M6mCjJ2Ob5Jp9er1MnYOHns4Av4URymxgGOjIX9wxysYMYjYw0.', 1);

INSERT INTO articles (title, author, text, status) VALUES
('Introduction to SQL', 1, 'This is a sample article about SQL.', 0),
('Data Modeling Techniques', 7, 'Learn about various data modeling techniques.', 1),
('Journalistic Writing Tips', 7, 'Improve your journalistic writing skills.', 0),
('Database Security Best Practices', 8, 'Secure your databases with these practices.', 2),
('Inženír Malinda', 8, 'This is a sample article about SQL.', 2),
('David Malinda', 7, 'GImprove your journalistic writing skills.', 2),
('David Liška', 1, 'Learn about various data modeling techniques.', 2);

INSERT INTO validations (validationID, article, redactor) VALUES
(1, 2, 2);

INSERT INTO reviews (reviewID, validation, text, reviewer) VALUES
(1, 1, 'Good article overall.', 3);

INSERT INTO editions (editionID, date, basename, title) VALUES
(1, '2023-01-15', 'číslo-1', 'January 2023'),
(2, '2023-02-20', 'číslo-2', 'February 2023');

INSERT INTO tags (tagID, name) VALUES
(1, 'exotické zvířata'),
(2, 'domáce zvířata'),
(3, 'chovatelké potřeby'),
(4, 'chované zvířata');

INSERT INTO article_edition (article_editionID, article, editionID, `order`) VALUES
(1, 4, 1, 1),
(2, 2, 1, 2),
(3, 6, 1, 3),
(4, 7, 2, 1);

INSERT INTO article_tag (article, tag) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 3),
(5, 4),
(6, 4),
(7, 4);