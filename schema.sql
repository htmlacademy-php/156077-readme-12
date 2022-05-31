CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  register_date TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(30) UNIQUE NOT NULL ,
  login VARCHAR(20) UNIQUE NOT NULL ,
  password VARCHAR(50) NOT NULL UNIQUE,
  avatar TEXT
);

CREATE UNIQUE INDEX user_id ON users(id);
CREATE UNIQUE INDEX user_email ON users(email);
CREATE UNIQUE INDEX user_login ON users(login);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNIQUE,
  post_type_id INT NOT NULL ,
  create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  header VARCHAR(128) NOT NULL ,
  post_text TEXT NOT NULL ,
  quote_author TEXT,
  post_image TEXT,
  post_video TEXT,
  post_link TEXT,
  views_count INT,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);

CREATE UNIQUE INDEX post_id ON posts(id);
CREATE UNIQUE INDEX post_user ON posts(user_id);
CREATE UNIQUE INDEX post_type ON posts(post_type_id);

CREATE TABLE post_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_type VARCHAR(20) NOT NULL UNIQUE ,
  icon_class TEXT,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX post_type ON post_types(post_type);

CREATE TABLE hashtags_posts (
  post_id INT NOT NULL ,
  hashtag_id INT NOT NULL ,
  UNIQUE post_hashtag (post_id, hashtag_id),
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
  FOREIGN KEY (hashtag_id ) REFERENCES hashtags (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX hashtag_post_id ON hashtags_posts(post_id);

CREATE TABLE hashtags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hashtag VARCHAR(10) NOT NULL UNIQUE
);

CREATE UNIQUE INDEX hashtag_name ON hashtags(hashtag);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT NOT NULL,
  create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  comment TEXT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX comment_user_id ON comments(user_id);
CREATE UNIQUE INDEX comment_post_id ON comments(post_id);

CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX like_user_id ON likes(user_id);
CREATE UNIQUE INDEX like_post_id ON likes(post_id);

CREATE TABLE subscribes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subscriber_id INT NOT NULL,
  subscribed_user_id INT NOT NULL,
  FOREIGN KEY (subscriber_id, subscribed_user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX subscribes_subscriber_id ON subscribes(subscriber_id);
CREATE UNIQUE INDEX subscribes_subscribed_user_id ON subscribes(subscribed_user_id);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  recipient_id INT NOT NULL,
  create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  message_text TEXT NOT NULL,
  FOREIGN KEY (sender_id, recipient_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX message_sender_id ON subscribes(sender_id);
CREATE UNIQUE INDEX message_recipient_id ON subscribes(recipient_id);

CREATE TABLE user_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE ,
  is_authorized BOOLEAN NOT NULL ,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX user_status_id ON user_status(user_id);