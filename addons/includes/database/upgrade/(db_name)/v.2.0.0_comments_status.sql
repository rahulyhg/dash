ALTER TABLE `comments` CHANGE `status` `status` ENUM('approved','awaiting','unapproved','spam','deleted', 'filter') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'awaiting';