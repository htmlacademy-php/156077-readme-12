CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  register_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(30) NOT NULL,
  login VARCHAR(20) NOT NULL,
  password VARCHAR(50) NOT NULL,
  avatar TEXT,
  UNIQUE user_data (email, login, password)
);

CREATE TABLE post_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_type VARCHAR(20) UNIQUE NOT NULL,
  icon_class VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  type VARCHAR(20),
  create_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  header VARCHAR(128) NOT NULL,
  post_text TEXT NOT NULL,
  quote_author TEXT,
  post_image TEXT,
  post_video TEXT,
  post_link TEXT,
  views_count INT,
  UNIQUE (user_id),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
  FOREIGN KEY (type) REFERENCES post_types (post_type) ON DELETE SET NULL
);

CREATE TABLE hashtags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hashtag VARCHAR(10) NOT NULL,
  UNIQUE (hashtag)
);

CREATE TABLE hashtags_posts (
  post_id INT NOT NULL ,
  hashtag_id INT NOT NULL ,
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
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
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

CREATE TABLE user_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  is_authorized BOOLEAN NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);