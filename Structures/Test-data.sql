INSERT INTO roles (roleID, name) VALUES
(1, 'autor'),
(2, 'redaktor'),
(3, 'recenzent'),
(4, 'šéfredaktor'),
(5, 'administrátor');

INSERT INTO users (userID, firstname, lastname, login, password, role) VALUES
(1, 'Vojtěch', 'Kratochvíl', 'kratas', '$2y$10$Mr8Ru0nsIDllYlppDnm3z.sOCNJe394AJhNS7eylgdRZJDudwum9y', 1),
(2, 'David', 'Malinha', 'cavemanspongebob', '$2y$10$ctaglWluI5M2lkqMflvJCOrfkQs4FSuZ.kb7yB8GFolb50N4viigO', 2),
(3, 'Dominik', 'Dořák', 'gramer', '$2y$10$GVXpKdurW3xBUy5zNpYcK.lXB3//xLtQpkCbdb0TYdaPHHi1iWjde', 3),
(4, 'Michal', 'Kuřák', 'darilin', '$2y$10$0dpDzJ6YsLS73EgS0xSDceXQg6Czj4z6A01oXpjvmQNZzKhuwOEt2', 4),
(5, 'Rudolf', 'Doležal', 'rudys', '$2y$10$35DosTLOXnInebzEg0ZMRemljo7cQM0qR9ZOSQYLpD8JIjhRBAKyq', 5),
(6, 'Jane', 'Smith', 'jane_smith', '$2y$10$aQ.XAQLtWYae4loR6CIolOieaVSTkBZfbs1eR2VfgGrwd5pifKBjS', 1),
(7, 'Bob', 'Johnson', 'bob_johnson', '$2y$10$w/iUe4SIPTd5qlSqvXN4aergLb6py0uiwnr5SNVLsUBDApBVGxLuK', 1),
(8, 'Alice', 'Johnson', 'alice_johnson', '$2y$10$t0cVOwgsj9ihTIwoiA85QubkybrdQF/RE/RsPOr9NDDTudW/sBwMC', 1),
(9, 'Charlie', 'Brown', 'charlie_brown', '$2y$10$44M6mCjJ2Ob5Jp9er1MnYOHns4Av4URymxgGOjIX9wxysYMYjYw0.', 1);

INSERT INTO articles (articleID, title, author, text, status) VALUES
(1, 'Introduction to SQL', 1, 'This is a sample article about SQL.', 0),
(2, 'Data Modeling Techniques', 7, 'Learn about various data modeling techniques.', 1),
(3, 'Journalistic Writing Tips', 7, 'Improve your journalistic writing skills.', 0),
(4, 'Database Security Best Practices', 8, 'Secure your databases with these practices.', 2),
(5, 'Inženír Malinda', 8, 'This is a sample article about SQL.', 2),
(6, 'David Malinda', 7, 'GImprove your journalistic writing skills.', 2),
(7, 'David Liška', 1, 'Learn about various data modeling techniques.', 2);

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