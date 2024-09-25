CREATE DATABASE auto_dekor;
USE auto_dekor;

CREATE TABLE autok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipus VARCHAR(255) NOT NULL,
    km_allas INT NOT NULL,
    le INT NOT NULL,
    ar INT NOT NULL,
    uzemanyag_tipus VARCHAR(50) NOT NULL,
    valto_tipus VARCHAR(50) NOT NULL,
    image_path VARCHAR(255)
);
