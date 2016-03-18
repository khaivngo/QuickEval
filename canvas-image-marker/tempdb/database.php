<?php
/* 
 *  Testing heatmap
 */
$sql = "CREATE DATABASE IF NOT EXISTS heatmap; 
		USE heatmap;
		CREATE TABLE IF NOT EXISTS shape 
		( 
			id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			image_src VARCHAR(128) NOT NULL,
			fill LONGTEXT NOT NULL,
			annotation VARCHAR(128)
		)";
$sth = $db->prepare($sql);
$sth->execute();
										