#
# @author      Tom Hartung <webmaster@tomhartung.com>
# @database    MySql
# @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
# @license     TBD
#
# 
#  SQL to create jos_joomoouser table
#
#  comment_posted_email: determines when user gets emails about comments to an article
#  -----------------------------------------------------------------------------------
#  NOTE: use the constants defined for these values in components/com_joomoouser/assets/constants.php
#     'E' - Entire site - send whenever comment posted to any article on site
#     'A' - Author follow-up - send whenever comment posted to article authored by user
#     'C' - Comment follow-up - send when a comment is posted to an article user has commented on
#     'F' - Follow-up (all) - send when a comment is posted to an article user has written or commented on
#     'N' - Never - never send when a comment is posted
#
DROP TABLE IF EXISTS `jos_joomoouser`;
CREATE TABLE IF NOT EXISTS `jos_joomoouser`
(
	`id` INT(11) UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNIQUE NULL DEFAULT NULL,
	`comment_posted_email` ENUM('E','A','C','F','N') NOT NULL DEFAULT 'N',
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY (`user_id`)
) CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

