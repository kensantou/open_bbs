#GRANT ALL PRIVILEGES ON bbssys.* TO admin@localhost IDENTIFIED BY 'bbssys';
#GRANT SELECT,INSERT,UPDATE,DELETE ON bbssys.* TO melon@localhost IDENTIFIED BY 'bbssys';

CREATE DATABASE IF NOT EXISTS bbssys DEFAULT CHARACTER SET utf8;

use bbssys;

CREATE TABLE topics
(
topic_id int NOT NULL AUTO_INCREMENT,
title varchar(100),
comment_cnt int,
image_name varchar(256),
created timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (topic_id)
);

CREATE TABLE comments
(
id int NOT NULL AUTO_INCREMENT,
topic_id int,
comment_id int,
name varchar(30),
message text NOT NULL,
image_name varchar(256),
created timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
);
