DROP TABLE IF EXISTS e_demo;
CREATE TABLE e_demo
(
demo_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
demo_name VARCHAR(20) NOT NULL DEFAULT 'Enozoomstudio',
UNIQUE(demo_name)
);
INSERT INTO e_demo(demo_name) VALUES
('Google'),
('Apple'),
('Twitter'),
('Facebook');