INSERT INTO Role (id, název) VALUES
(1, 'autor'),
(2, 'redaktor'),
(3, 'recenzent'),
(4, 'šéfredaktor'),
(5, 'administrátor');

INSERT INTO Uživatelé (id, Jméno, Příjmení, login, password, role) VALUES
(1, 'John', 'Doe', 'john_doe', 'password123', 1),
(2, 'Jane', 'Smith', 'jane_smith', 'pass456', 2),
(3, 'Alice', 'Johnson', 'alice_j', 'securepass', 3),
(4, 'Bob', 'Brown', 'bob_brown', 'qwerty', 4),
(5, 'Admin', 'Adminson', 'admin', 'adminpass', 5),
(6, 'Jane', 'Smith', 'jane_smith', 'securepass456', 1),
(7, 'Bob', 'Johnson', 'bob_johnson', 'strongpassword789', 1),
(8, 'Alice', 'Johnson', 'alice_johnson', 'pass1234', 1),
(9, 'Charlie', 'Brown', 'charlie_brown', 'securepass789', 1);

INSERT INTO články (id, Název, autor, text, status) VALUES
(1, 'Introduction to SQL', 1, 'This is a sample article about SQL.', 0),
(2, 'Data Modeling Techniques', 7, 'Learn about various data modeling techniques.', 1),
(3, 'Journalistic Writing Tips', 7, 'Improve your journalistic writing skills.', 0),
(4, 'Database Security Best Practices', 8, 'Secure your databases with these practices.', 2),
(5, 'Inženír Malinda', 8, 'This is a sample article about SQL.', 2),
(6, 'David Malinda', 7, 'GImprove your journalistic writing skills.', 2),
(7, 'David Lišska', 1, 'Learn about various data modeling techniques.', 2);

INSERT INTO kontrola (id, článek, redaktor) VALUES
(1, 2, 2);

INSERT INTO recenze (id, kontrola, text, recenzent) VALUES
(1, 1, 'Good article overall.', 3);

INSERT INTO vydání (id, datum, označení, nadpis) VALUES
(1, '2023-01-15', 'číslo-1', 'January 2023'),
(2, '2023-02-20', 'číslo-2', 'February 2023');

INSERT INTO tagy (id, název) VALUES
(1, 'exotické zvířata'),
(2, 'domáce zvířata'),
(3, 'chovatelké potřeby'),
(4, 'chované zvířata');

INSERT INTO článek_vydání (id, článek, vydání, pořadí) VALUES
(1, 4, 1, 1),
(2, 2, 1, 2),
(3, 6, 1, 3),
(4, 7, 2, 1);

INSERT INTO článek_tag (článek, tag) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 3),
(5, 4),
(6, 4),
(7, 4);