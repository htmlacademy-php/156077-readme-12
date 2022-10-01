CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  register_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(30) UNIQUE NOT NULL,
  login VARCHAR(20) UNIQUE NOT NULL,
  password TEXT NOT NULL,
  avatar TEXT
);

CREATE TABLE post_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20) UNIQUE NOT NULL,
  alter_name VARCHAR(20) UNIQUE NOT NULL,
  icon VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type_id INT,
  create_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  header VARCHAR(128) NOT NULL,
  post_text TEXT DEFAULT NULL,
  quote_author TEXT DEFAULT NULL,
  post_image TEXT DEFAULT NULL,
  post_video TEXT DEFAULT NULL,
  post_link TEXT DEFAULT NULL,
  views_count INT DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (type_id) REFERENCES post_types (id) ON DELETE SET NULL
);

CREATE TABLE hashtags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hashtag VARCHAR(10) UNIQUE NOT NULL
);

CREATE TABLE hashtags_posts (
  post_id INT NOT NULL,
  hashtag_id INT NOT NULL,
  UNIQUE post_hashtag (post_id, hashtag_id),
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
  FOREIGN KEY (hashtag_id) REFERENCES hashtags (id) ON DELETE CASCADE
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT NOT NULL,
  create_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  comment TEXT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE INDEX comment_user_id ON comments(user_id);
CREATE INDEX comment_post_id ON comments(post_id);

CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  UNIQUE liked_post (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE TABLE subscribes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subscriber_id INT NOT NULL,
  subscribed_user_id INT NOT NULL,
  UNIQUE subsription (subscriber_id, subscribed_user_id),
  FOREIGN KEY (subscriber_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (subscribed_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  recipient_id INT NOT NULL,
  create_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  message_text TEXT NOT NULL,
  FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (recipient_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE INDEX message_sender_id ON messages(sender_id);
CREATE INDEX message_recipient_id ON messages(recipient_id);