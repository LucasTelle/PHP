CREATE DATABASE IF NOT EXISTS loja
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE loja;

CREATE TABLE IF NOT EXISTS produtos (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(100)  NOT NULL,
    categoria  VARCHAR(50)   NOT NULL,
    preco      DECIMAL(10,2) NOT NULL,
    quantidade INT UNSIGNED  NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;