<?php
/**
 * This file will handle all the different pictureOrders for all experiments.
 */

require_once('../../db.php');
include_once('../../functions.php');

if (!isset($_SESSION['user'])) {
	header("Location: ../../login.php");
	exit;
}

if ($_SESSION['user']['userType'] > 2) {
	exit;
}

/**
 * A function that will create a pictureQueue.
 * @param $images an array with imageId's of which to create a queue from.
 * @param $images ShownRightAndLeft 1/0 of whether to show a picture on both sides.
 * @return $pairs, an array with the new queue.
 */
function makeQueue($images, $imagesShownRightAndLeft) {
	$pairs = array();
	$index = 1;
	$arrIndex = 0;

	foreach($images as $image) {
		for($i = $index;$i < count($images);$i++ ) {
			$pairs[$arrIndex][0] = $image['id'];
			$pairs[$arrIndex][1] = $images[$i]['id'];
			if($imagesShownRightAndLeft == 1) {
				$arrIndex++;
				$pairs[$arrIndex][0] = $images[$i]['id'];
				$pairs[$arrIndex][1] = $image['id'];
			}
			$arrIndex++;
		}
		$index++;
	}
	shuffle($pairs);
	return $pairs;
}

/**
 * Create queue for a triplet comparison experiment.
 */
function makeTripletQueue($images) {
	$pairs = array();

	if (count($images) == 7) {
		// the 7 triplets from the equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[3]['id'] ]; // (1, 2, 4)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[4]['id'] ]; // (2, 3, 5)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[5]['id'] ]; // (3, 4, 6)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[6]['id'] ]; // (4, 5, 7)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[0]['id'] ]; // (5, 6, 1)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[1]['id'] ]; // (6, 7, 2)
		$pairs[] = [ $images[6]['id'], $images[0]['id'], $images[2]['id'] ]; // (7, 1, 3)
	}

	if (count($images) == 9) {
		// the first 3 triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[3]['id'] ]; // (1, 2, 4)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[6]['id'] ]; // (4, 5, 7)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[0]['id'] ]; // (7, 8, 1)

		// the next 3 triplets from the second equation
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[4]['id'] ]; // (2, 3, 5)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[7]['id'] ]; // (5, 6, 8)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[1]['id'] ]; // (8, 9, 2)

		// the next 3 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[5]['id'] ]; // (1, 3, 6)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[8]['id'] ]; // (4, 6, 9)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[2]['id'] ]; // (7, 9, 3)

		// the last 3 triplets from the fourth equation
		$pairs[] = [ $images[0]['id'], $images[4]['id'], $images[8]['id'] ]; // (1, 5, 9)
		$pairs[] = [ $images[3]['id'], $images[7]['id'], $images[2]['id'] ]; // (4, 8, 3)
		$pairs[] = [ $images[6]['id'], $images[1]['id'], $images[5]['id'] ]; // (7, 2, 6)
	}

	if (count($images) == 13) {
		// the 13 first triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[7]['id'] ]; // (1, 3, 8)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[8]['id'] ]; // (2, 4, 9)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[9]['id'] ]; // (3, 5, 10)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[10]['id'] ]; // (4, 6, 11)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[11]['id'] ]; // (5, 7, 12)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[12]['id'] ]; // (6, 8, 13)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[0]['id'] ]; // (7, 9, 1)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[1]['id'] ]; // (8, 10, 2)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[2]['id'] ]; // (9, 11, 3)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[3]['id'] ]; // (10, 12, 4)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[4]['id'] ]; // (11, 13, 5)
		$pairs[] = [ $images[11]['id'], $images[0]['id'], $images[5]['id'] ]; // (12, 1, 6)
		$pairs[] = [ $images[12]['id'], $images[1]['id'], $images[6]['id'] ]; // (13, 2, 7)

		// the last 13 triplets from the second equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[4]['id'] ]; // (1, 2, 5)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[5]['id'] ]; // (2, 3, 6)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[6]['id'] ]; // (3, 4, 7)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[7]['id'] ]; // (4, 5, 8)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[8]['id'] ]; // (5, 6, 9)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[9]['id'] ]; // (6, 7, 10)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[10]['id'] ]; // (7, 8, 11)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[11]['id'] ]; // (8, 9, 12)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[12]['id'] ]; // (9, 10, 13)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[0]['id'] ]; // (10, 11, 1)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[1]['id'] ]; // (11, 12, 2)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[2]['id'] ]; // (12, 13, 3)
		$pairs[] = [ $images[12]['id'], $images[0]['id'], $images[3]['id'] ]; // (13, 1, 4)
	}

	if (count($images) == 15) {
		// the first 15 triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[8]['id'] ]; // (1, 3, 9)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[9]['id'] ]; // (2, 4, 10)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[10]['id'] ]; // (3, 5, 11)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[11]['id'] ]; // (4, 6, 12)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[12]['id'] ]; // (5, 7, 13)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[13]['id'] ]; // (6, 8, 14)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[14]['id'] ]; // (7, 9, 15)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[0]['id'] ]; // (8, 10, 1)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[1]['id'] ]; // (9, 11, 2)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[2]['id'] ]; // (10, 12, 3)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[3]['id'] ]; // (11, 13, 4)
		$pairs[] = [ $images[11]['id'], $images[13]['id'], $images[4]['id'] ]; // (12, 14, 5)
		$pairs[] = [ $images[12]['id'], $images[14]['id'], $images[5]['id'] ]; // (13, 15, 6)
		$pairs[] = [ $images[13]['id'], $images[0]['id'], $images[6]['id'] ]; // (14, 1, 7)
		$pairs[] = [ $images[14]['id'], $images[1]['id'], $images[7]['id'] ]; // (15, 2, 8)

		// the next 15 triplets from the second equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[4]['id'] ]; // (1, 2, 5)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[5]['id'] ]; // (2, 3, 6)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[6]['id'] ]; // (3, 4, 7)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[7]['id'] ]; // (4, 5, 8)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[8]['id'] ]; // (5, 6, 9)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[9]['id'] ]; // (6, 7, 10)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[10]['id'] ]; // (7, 8, 11)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[11]['id'] ]; // (8, 9, 12)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[12]['id'] ]; // (9, 10, 13)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[13]['id'] ]; // (10, 11, 14)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[14]['id'] ]; // (11, 12, 15)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[0]['id'] ]; // (12, 13, 1)
		$pairs[] = [ $images[12]['id'], $images[13]['id'], $images[1]['id'] ]; // (13, 14, 2)
		$pairs[] = [ $images[13]['id'], $images[14]['id'], $images[2]['id'] ]; // (14, 15, 3)
		$pairs[] = [ $images[14]['id'], $images[0]['id'], $images[3]['id'] ]; // (15, 1, 4)

		// the last 5 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[5]['id'], $images[10]['id'] ]; // (1, 6, 11)
		$pairs[] = [ $images[1]['id'], $images[6]['id'], $images[11]['id'] ]; // (2, 7, 12)
		$pairs[] = [ $images[2]['id'], $images[7]['id'], $images[12]['id'] ]; // (3, 8, 13)
		$pairs[] = [ $images[3]['id'], $images[8]['id'], $images[13]['id'] ]; // (4, 9, 14)
		$pairs[] = [ $images[4]['id'], $images[9]['id'], $images[14]['id'] ]; // (5, 10, 15)
	}

	if (count($images) == 19) {
		// the first 19 triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[10]['id'] ]; // (1, 3, 11)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[11]['id'] ]; // (2, 4, 12)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[12]['id'] ]; // (3, 5, 13)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[13]['id'] ]; // (4, 6, 14)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[14]['id'] ]; // (5, 7, 15)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[15]['id'] ]; // (6, 8, 16)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[16]['id'] ]; // (7, 9, 17)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[17]['id'] ]; // (8, 10, 18)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[18]['id'] ]; // (9, 11, 19)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[0]['id'] ]; // (10, 12, 1)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[1]['id'] ]; // (11, 13, 2)
		$pairs[] = [ $images[11]['id'], $images[13]['id'], $images[2]['id'] ]; // (12, 14, 3)
		$pairs[] = [ $images[12]['id'], $images[14]['id'], $images[3]['id'] ]; // (13, 15, 4)
		$pairs[] = [ $images[13]['id'], $images[15]['id'], $images[4]['id'] ]; // (14, 16, 5)
		$pairs[] = [ $images[14]['id'], $images[16]['id'], $images[5]['id'] ]; // (15, 17, 6)
		$pairs[] = [ $images[15]['id'], $images[17]['id'], $images[6]['id'] ]; // (16, 18, 7)
		$pairs[] = [ $images[16]['id'], $images[18]['id'], $images[7]['id'] ]; // (17, 19, 8)
		$pairs[] = [ $images[17]['id'], $images[0]['id'], $images[8]['id'] ]; // (18, 1, 9)
		$pairs[] = [ $images[18]['id'], $images[1]['id'], $images[9]['id'] ]; // (19, 2, 10)

		// the next 19 triplets from the second equation
		$pairs[] = [ $images[0]['id'],  $images[3]['id'],  $images[7]['id'] ];  // (1, 4, 8)
		$pairs[] = [ $images[1]['id'],  $images[4]['id'],  $images[8]['id'] ];  // (2, 5, 9)
		$pairs[] = [ $images[2]['id'],  $images[5]['id'],  $images[9]['id'] ];  // (3, 6, 10)
		$pairs[] = [ $images[3]['id'],  $images[6]['id'],  $images[10]['id'] ]; // (4, 7, 11)
		$pairs[] = [ $images[4]['id'],  $images[7]['id'],  $images[11]['id'] ]; // (5, 8, 12)
		$pairs[] = [ $images[5]['id'],  $images[8]['id'],  $images[12]['id'] ]; // (6, 9, 13)
		$pairs[] = [ $images[6]['id'],  $images[9]['id'],  $images[13]['id'] ]; // (7, 10, 14)
		$pairs[] = [ $images[7]['id'],  $images[10]['id'], $images[14]['id'] ]; // (8, 11, 15)
		$pairs[] = [ $images[8]['id'],  $images[11]['id'], $images[15]['id'] ]; // (9, 12, 16)
		$pairs[] = [ $images[9]['id'],  $images[12]['id'], $images[16]['id'] ]; // (10, 13, 17)
		$pairs[] = [ $images[10]['id'], $images[13]['id'], $images[17]['id'] ]; // (11, 14, 18)
		$pairs[] = [ $images[11]['id'], $images[14]['id'], $images[18]['id'] ]; // (12, 15, 19)
		$pairs[] = [ $images[12]['id'], $images[15]['id'], $images[0]['id'] ];  // (13, 16, 1)
		$pairs[] = [ $images[13]['id'], $images[16]['id'], $images[1]['id'] ];  // (14, 17, 2)
		$pairs[] = [ $images[14]['id'], $images[17]['id'], $images[2]['id'] ];  // (15, 18, 3)
		$pairs[] = [ $images[15]['id'], $images[18]['id'], $images[3]['id'] ];  // (16, 19, 4)
		$pairs[] = [ $images[16]['id'], $images[0]['id'],  $images[4]['id'] ];  // (17, 1, 5)
		$pairs[] = [ $images[17]['id'], $images[1]['id'],  $images[5]['id'] ];  // (18, 2, 6)
		$pairs[] = [ $images[18]['id'], $images[2]['id'],  $images[6]['id'] ];  // (19, 3, 7)

		// the last 19 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[6]['id'] ]; // (1, 2, 7)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[7]['id'] ]; // (2, 3, 8)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[8]['id'] ]; // (3, 4, 9)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[9]['id'] ]; // (4, 5, 10)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[10]['id'] ]; // (5, 6, 11)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[11]['id'] ]; // (6, 7, 12)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[12]['id'] ]; // (7, 8, 13)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[13]['id'] ]; // (8, 9, 14)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[14]['id'] ]; // (9, 10, 15)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[15]['id'] ]; // (10, 11, 16)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[16]['id'] ]; // (11, 12, 17)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[17]['id'] ]; // (12, 13, 18)
		$pairs[] = [ $images[12]['id'], $images[13]['id'], $images[18]['id'] ]; // (13, 14, 19)
		$pairs[] = [ $images[13]['id'], $images[14]['id'], $images[0]['id'] ]; // (14, 15, 1)
		$pairs[] = [ $images[14]['id'], $images[15]['id'], $images[1]['id'] ]; // (15, 16, 2)
		$pairs[] = [ $images[15]['id'], $images[16]['id'], $images[2]['id'] ]; // (16, 17, 3)
		$pairs[] = [ $images[16]['id'], $images[17]['id'], $images[3]['id'] ]; // (17, 18, 4)
		$pairs[] = [ $images[17]['id'], $images[18]['id'], $images[4]['id'] ]; // (18, 19, 5)
		$pairs[] = [ $images[18]['id'], $images[0]['id'], $images[5]['id'] ]; // (19, 1, 6)
	}

	if (count($images) == 21) {
		// the first 21 triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[10]['id'] ]; // (1, 2, 11)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[11]['id'] ]; // (2, 3, 12)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[12]['id'] ]; // (3, 4, 13)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[13]['id'] ]; // (4, 5, 14)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[14]['id'] ]; // (5, 6, 15)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[15]['id'] ]; // (6, 7, 16)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[16]['id'] ]; // (7, 8, 17)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[17]['id'] ]; // (8, 9, 18)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[18]['id'] ]; // (9, 10, 19)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[19]['id'] ]; // (10, 11, 20)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[20]['id'] ]; // (11, 12, 21)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[0]['id'] ]; // (12, 13, 1)
		$pairs[] = [ $images[12]['id'], $images[13]['id'], $images[1]['id'] ]; // (13, 14, 2)
		$pairs[] = [ $images[13]['id'], $images[14]['id'], $images[2]['id'] ]; // (14, 15, 3)
		$pairs[] = [ $images[14]['id'], $images[15]['id'], $images[3]['id'] ]; // (15, 16, 4)
		$pairs[] = [ $images[15]['id'], $images[16]['id'], $images[4]['id'] ]; // (16, 17, 5)
		$pairs[] = [ $images[16]['id'], $images[17]['id'], $images[5]['id'] ]; // (17, 18, 6)
		$pairs[] = [ $images[17]['id'], $images[18]['id'], $images[6]['id'] ]; // (18, 19, 7)
		$pairs[] = [ $images[18]['id'], $images[19]['id'], $images[7]['id'] ]; // (19, 20, 8)
		$pairs[] = [ $images[19]['id'], $images[20]['id'], $images[8]['id'] ]; // (20, 21, 9)
		$pairs[] = [ $images[20]['id'], $images[0]['id'], $images[9]['id'] ]; // (21, 1, 10)

		// the next 21 triplets from the second equation
		$pairs[] = [ $images[0]['id'], $images[3]['id'], $images[8]['id'] ]; // (1, 4, 9)
		$pairs[] = [ $images[1]['id'], $images[4]['id'], $images[9]['id'] ]; // (2, 5, 10)
		$pairs[] = [ $images[2]['id'], $images[5]['id'], $images[10]['id'] ]; // (3, 6, 11)
		$pairs[] = [ $images[3]['id'], $images[6]['id'], $images[11]['id'] ]; // (4, 7, 12)
		$pairs[] = [ $images[4]['id'], $images[7]['id'], $images[12]['id'] ]; // (5, 8, 13)
		$pairs[] = [ $images[5]['id'], $images[8]['id'], $images[13]['id'] ]; // (6, 9, 14)
		$pairs[] = [ $images[6]['id'], $images[9]['id'], $images[14]['id'] ]; // (7, 10, 15)
		$pairs[] = [ $images[7]['id'], $images[10]['id'], $images[15]['id'] ]; // (8, 11, 16)
		$pairs[] = [ $images[8]['id'], $images[11]['id'], $images[16]['id'] ]; // (9, 12, 17)
		$pairs[] = [ $images[9]['id'], $images[12]['id'], $images[17]['id'] ]; // (10, 13, 18)
		$pairs[] = [ $images[10]['id'], $images[13]['id'], $images[18]['id'] ]; // (11, 14, 19)
		$pairs[] = [ $images[11]['id'], $images[14]['id'], $images[19]['id'] ]; // (12, 15, 20)
		$pairs[] = [ $images[12]['id'], $images[15]['id'], $images[20]['id'] ]; // (13, 16, 21)
		$pairs[] = [ $images[13]['id'], $images[16]['id'], $images[0]['id'] ]; // (14, 17, 1)
		$pairs[] = [ $images[14]['id'], $images[17]['id'], $images[1]['id'] ]; // (15, 18, 2)
		$pairs[] = [ $images[15]['id'], $images[18]['id'], $images[2]['id'] ]; // (16, 19, 3)
		$pairs[] = [ $images[16]['id'], $images[19]['id'], $images[3]['id'] ]; // (17, 20, 4)
		$pairs[] = [ $images[17]['id'], $images[20]['id'], $images[4]['id'] ]; // (18, 21, 5)
		$pairs[] = [ $images[18]['id'], $images[0]['id'], $images[5]['id'] ]; // (19, 1, 6)
		$pairs[] = [ $images[19]['id'], $images[1]['id'], $images[6]['id'] ]; // (20, 2, 7)
		$pairs[] = [ $images[20]['id'], $images[2]['id'], $images[7]['id'] ]; // (21, 3, 8)

		// the next 21 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[6]['id'] ]; // (1, 3, 7)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[7]['id'] ]; // (2, 4, 8)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[8]['id'] ]; // (3, 5, 9)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[9]['id'] ]; // (4, 6, 10)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[10]['id'] ]; // (5, 7, 11)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[11]['id'] ]; // (6, 8, 12)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[12]['id'] ]; // (7, 9, 13)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[13]['id'] ]; // (8, 10, 14)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[14]['id'] ]; // (9, 11, 15)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[15]['id'] ]; // (10, 12, 16)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[16]['id'] ]; // (11, 13, 17)
		$pairs[] = [ $images[11]['id'], $images[13]['id'], $images[17]['id'] ]; // (12, 14, 18)
		$pairs[] = [ $images[12]['id'], $images[14]['id'], $images[18]['id'] ]; // (13, 15, 19)
		$pairs[] = [ $images[13]['id'], $images[15]['id'], $images[19]['id'] ]; // (14, 16, 20)
		$pairs[] = [ $images[14]['id'], $images[16]['id'], $images[20]['id'] ]; // (15, 17, 21)
		$pairs[] = [ $images[15]['id'], $images[17]['id'], $images[0]['id'] ]; // (16, 18, 1)
		$pairs[] = [ $images[16]['id'], $images[18]['id'], $images[1]['id'] ]; // (17, 19, 2)
		$pairs[] = [ $images[17]['id'], $images[19]['id'], $images[2]['id'] ]; // (18, 20, 3)
		$pairs[] = [ $images[18]['id'], $images[20]['id'], $images[3]['id'] ]; // (19, 21, 4)
		$pairs[] = [ $images[19]['id'], $images[0]['id'], $images[4]['id'] ]; // (20, 1, 5)
		$pairs[] = [ $images[20]['id'], $images[1]['id'], $images[5]['id'] ]; // (21, 2, 6)

		// the last 7 triplets from the fourth equation
		$pairs[] = [ $images[0]['id'], $images[7]['id'], $images[14]['id'] ]; // (1, 8, 15)
		$pairs[] = [ $images[1]['id'], $images[8]['id'], $images[15]['id'] ]; // (2, 9, 16)
		$pairs[] = [ $images[2]['id'], $images[9]['id'], $images[16]['id'] ]; // (3, 10, 17)
		$pairs[] = [ $images[3]['id'], $images[10]['id'], $images[17]['id'] ]; // (4, 11, 18)
		$pairs[] = [ $images[4]['id'], $images[11]['id'], $images[18]['id'] ]; // (5, 12, 19)
		$pairs[] = [ $images[5]['id'], $images[12]['id'], $images[19]['id'] ]; // (6, 13, 20)
		$pairs[] = [ $images[6]['id'], $images[13]['id'], $images[20]['id'] ]; // (7, 14, 21)
	}

	if (count($images) == 25) {
		// the first 25 triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[12]['id'] ]; // (1, 3, 13)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[13]['id'] ]; // (2, 4, 14)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[14]['id'] ]; // (3, 5, 15)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[15]['id'] ]; // (4, 6, 16)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[16]['id'] ]; // (5, 7, 17)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[17]['id'] ]; // (6, 8, 18)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[18]['id'] ]; // (7, 9, 19)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[19]['id'] ]; // (8, 10, 20)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[20]['id'] ]; // (9, 11, 21)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[21]['id'] ]; // (10, 12, 22)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[22]['id'] ]; // (11, 13, 23)
		$pairs[] = [ $images[11]['id'], $images[13]['id'], $images[23]['id'] ]; // (12, 14, 24)
		$pairs[] = [ $images[12]['id'], $images[14]['id'], $images[24]['id'] ]; // (13, 15, 25)
		$pairs[] = [ $images[13]['id'], $images[15]['id'], $images[0]['id'] ]; // (14, 16, 1)
		$pairs[] = [ $images[14]['id'], $images[16]['id'], $images[1]['id'] ]; // (15, 17, 2)
		$pairs[] = [ $images[15]['id'], $images[17]['id'], $images[2]['id'] ]; // (16, 18, 3)
		$pairs[] = [ $images[16]['id'], $images[18]['id'], $images[3]['id'] ]; // (17, 19, 4)
		$pairs[] = [ $images[17]['id'], $images[19]['id'], $images[4]['id'] ]; // (18, 20, 5)
		$pairs[] = [ $images[18]['id'], $images[20]['id'], $images[5]['id'] ]; // (19, 21, 6)
		$pairs[] = [ $images[19]['id'], $images[21]['id'], $images[6]['id'] ]; // (20, 22, 7)
		$pairs[] = [ $images[20]['id'], $images[22]['id'], $images[7]['id'] ]; // (21, 23, 8)
		$pairs[] = [ $images[21]['id'], $images[23]['id'], $images[8]['id'] ]; // (22, 24, 9)
		$pairs[] = [ $images[22]['id'], $images[24]['id'], $images[9]['id'] ]; // (23, 25, 10)
		$pairs[] = [ $images[23]['id'], $images[0]['id'], $images[10]['id'] ]; // (24, 1, 11)
		$pairs[] = [ $images[24]['id'], $images[1]['id'], $images[11]['id'] ]; // (25, 2, 12)

		// the next 25 triplets from the second equation
		$pairs[] = [ $images[0]['id'], $images[3]['id'], $images[11]['id'] ]; // (1, 4, 12)
		$pairs[] = [ $images[1]['id'], $images[4]['id'], $images[12]['id'] ]; // (2, 5, 13)
		$pairs[] = [ $images[2]['id'], $images[5]['id'], $images[13]['id'] ]; // (3, 6, 14)
		$pairs[] = [ $images[3]['id'], $images[6]['id'], $images[14]['id'] ]; // (4, 7, 15)
		$pairs[] = [ $images[4]['id'], $images[7]['id'], $images[15]['id'] ]; // (5, 8, 16)
		$pairs[] = [ $images[5]['id'], $images[8]['id'], $images[16]['id'] ]; // (6, 9, 17)
		$pairs[] = [ $images[6]['id'], $images[9]['id'], $images[17]['id'] ]; // (7, 10, 18)
		$pairs[] = [ $images[7]['id'], $images[10]['id'], $images[18]['id'] ]; // (8, 11, 19)
		$pairs[] = [ $images[8]['id'], $images[11]['id'], $images[19]['id'] ]; // (9, 12, 20)
		$pairs[] = [ $images[9]['id'], $images[12]['id'], $images[20]['id'] ]; // (10, 13, 21)
		$pairs[] = [ $images[10]['id'], $images[13]['id'], $images[21]['id'] ]; // (11, 14, 22)
		$pairs[] = [ $images[11]['id'], $images[14]['id'], $images[22]['id'] ]; // (12, 15, 23)
		$pairs[] = [ $images[12]['id'], $images[15]['id'], $images[23]['id'] ]; // (13, 16, 24)
		$pairs[] = [ $images[13]['id'], $images[16]['id'], $images[24]['id'] ]; // (14, 17, 25)
		$pairs[] = [ $images[14]['id'], $images[17]['id'], $images[0]['id'] ]; // (15, 18, 1)
		$pairs[] = [ $images[15]['id'], $images[18]['id'], $images[1]['id'] ]; // (16, 19, 2)
		$pairs[] = [ $images[16]['id'], $images[19]['id'], $images[2]['id'] ]; // (17, 20, 3)
		$pairs[] = [ $images[17]['id'], $images[20]['id'], $images[3]['id'] ]; // (18, 21, 4)
		$pairs[] = [ $images[18]['id'], $images[21]['id'], $images[4]['id'] ]; // (19, 22, 5)
		$pairs[] = [ $images[19]['id'], $images[22]['id'], $images[5]['id'] ]; // (20, 23, 6)
		$pairs[] = [ $images[20]['id'], $images[23]['id'], $images[6]['id'] ]; // (21, 24, 7)
		$pairs[] = [ $images[21]['id'], $images[24]['id'], $images[7]['id'] ]; // (22, 25, 8)
		$pairs[] = [ $images[22]['id'], $images[0]['id'], $images[8]['id'] ]; // (23, 1, 9)
		$pairs[] = [ $images[23]['id'], $images[1]['id'], $images[9]['id'] ]; // (24, 2, 10)
		$pairs[] = [ $images[24]['id'], $images[2]['id'], $images[10]['id'] ]; // (25, 3, 11)

		// the next 25 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[4]['id'], $images[9]['id'] ]; // (1, 5, 10)
		$pairs[] = [ $images[1]['id'], $images[5]['id'], $images[10]['id'] ]; // (2, 6, 11)
		$pairs[] = [ $images[2]['id'], $images[6]['id'], $images[11]['id'] ]; // (3, 7, 12)
		$pairs[] = [ $images[3]['id'], $images[7]['id'], $images[12]['id'] ]; // (4, 8, 13)
		$pairs[] = [ $images[4]['id'], $images[8]['id'], $images[13]['id'] ]; // (5, 9, 14)
		$pairs[] = [ $images[5]['id'], $images[9]['id'], $images[14]['id'] ]; // (6, 10, 15)
		$pairs[] = [ $images[6]['id'], $images[10]['id'], $images[15]['id'] ]; // (7, 11, 16)
		$pairs[] = [ $images[7]['id'], $images[11]['id'], $images[16]['id'] ]; // (8, 12, 17)
		$pairs[] = [ $images[8]['id'], $images[12]['id'], $images[17]['id'] ]; // (9, 13, 18)
		$pairs[] = [ $images[9]['id'], $images[13]['id'], $images[18]['id'] ]; // (10, 14, 19)
		$pairs[] = [ $images[10]['id'], $images[14]['id'], $images[19]['id'] ]; // (11, 15, 20)
		$pairs[] = [ $images[11]['id'], $images[15]['id'], $images[20]['id'] ]; // (12, 16, 21)
		$pairs[] = [ $images[12]['id'], $images[16]['id'], $images[21]['id'] ]; // (13, 17, 22)
		$pairs[] = [ $images[13]['id'], $images[17]['id'], $images[22]['id'] ]; // (14, 18, 23)
		$pairs[] = [ $images[14]['id'], $images[18]['id'], $images[23]['id'] ]; // (15, 19, 24)
		$pairs[] = [ $images[15]['id'], $images[19]['id'], $images[24]['id'] ]; // (16, 20, 25)
		$pairs[] = [ $images[16]['id'], $images[20]['id'], $images[0]['id'] ]; // (17, 21, 1)
		$pairs[] = [ $images[17]['id'], $images[21]['id'], $images[1]['id'] ]; // (18, 22, 2)
		$pairs[] = [ $images[18]['id'], $images[22]['id'], $images[2]['id'] ]; // (19, 23, 3)
		$pairs[] = [ $images[19]['id'], $images[23]['id'], $images[3]['id'] ]; // (20, 24, 4)
		$pairs[] = [ $images[20]['id'], $images[24]['id'], $images[4]['id'] ]; // (21, 25, 5)
		$pairs[] = [ $images[21]['id'], $images[0]['id'], $images[5]['id'] ]; // (22, 1, 6)
		$pairs[] = [ $images[22]['id'], $images[1]['id'], $images[6]['id'] ]; // (23, 2, 7)
		$pairs[] = [ $images[23]['id'], $images[2]['id'], $images[7]['id'] ]; // (24, 3, 8)
		$pairs[] = [ $images[24]['id'], $images[3]['id'], $images[8]['id'] ]; // (25, 4, 9)

		// the last 25 triplets from the fourth equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[7]['id'] ]; // (1, 2, 8)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[8]['id'] ]; // (2, 3, 9)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[9]['id'] ]; // (3, 4, 10)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[10]['id'] ]; // (4, 5, 11)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[11]['id'] ]; // (5, 6, 12)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[12]['id'] ]; // (6, 7, 13)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[13]['id'] ]; // (7, 8, 14)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[14]['id'] ]; // (8, 9, 15)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[15]['id'] ]; // (9, 10, 16)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[16]['id'] ]; // (10, 11, 17)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[17]['id'] ]; // (11, 12, 18)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[18]['id'] ]; // (12, 13, 19)
		$pairs[] = [ $images[12]['id'], $images[13]['id'], $images[19]['id'] ]; // (13, 14, 20)
		$pairs[] = [ $images[13]['id'], $images[14]['id'], $images[20]['id'] ]; // (14, 15, 21)
		$pairs[] = [ $images[14]['id'], $images[15]['id'], $images[21]['id'] ]; // (15, 16, 22)
		$pairs[] = [ $images[15]['id'], $images[16]['id'], $images[22]['id'] ]; // (16, 17, 23)
		$pairs[] = [ $images[16]['id'], $images[17]['id'], $images[23]['id'] ]; // (17, 18, 24)
		$pairs[] = [ $images[17]['id'], $images[18]['id'], $images[24]['id'] ]; // (18, 19, 25)
		$pairs[] = [ $images[18]['id'], $images[19]['id'], $images[0]['id'] ]; // (19, 20, 1)
		$pairs[] = [ $images[19]['id'], $images[20]['id'], $images[1]['id'] ]; // (20, 21, 2)
		$pairs[] = [ $images[20]['id'], $images[21]['id'], $images[2]['id'] ]; // (21, 22, 3)
		$pairs[] = [ $images[21]['id'], $images[22]['id'], $images[3]['id'] ]; // (22, 23, 4)
		$pairs[] = [ $images[22]['id'], $images[23]['id'], $images[4]['id'] ]; // (23, 24, 5)
		$pairs[] = [ $images[23]['id'], $images[24]['id'], $images[5]['id'] ]; // (24, 25, 6)
		$pairs[] = [ $images[24]['id'], $images[0]['id'], $images[6]['id'] ]; // (25, 1, 7)
	}

	// $order = [
	// 	[0, 1, 13],
	// 	[1, 2, 14],
	// 	[2, 3, 15]
	// 	....
	// ]

	if (count($images) == 27) {
		// the 27 first triplets from the first equation
		$pairs[] = [ $images[0]['id'], $images[1]['id'], $images[13]['id'] ]; // (1, 2, 14)
		$pairs[] = [ $images[1]['id'], $images[2]['id'], $images[14]['id'] ]; // (2, 3, 15)
		$pairs[] = [ $images[2]['id'], $images[3]['id'], $images[15]['id'] ]; // (3, 4, 16)
		$pairs[] = [ $images[3]['id'], $images[4]['id'], $images[16]['id'] ]; // (4, 5, 17)
		$pairs[] = [ $images[4]['id'], $images[5]['id'], $images[17]['id'] ]; // (5, 6, 18)
		$pairs[] = [ $images[5]['id'], $images[6]['id'], $images[18]['id'] ]; // (6, 7, 19)
		$pairs[] = [ $images[6]['id'], $images[7]['id'], $images[19]['id'] ]; // (7, 8, 20)
		$pairs[] = [ $images[7]['id'], $images[8]['id'], $images[20]['id'] ]; // (8, 9, 21)
		$pairs[] = [ $images[8]['id'], $images[9]['id'], $images[21]['id'] ]; // (9, 10, 22)
		$pairs[] = [ $images[9]['id'], $images[10]['id'], $images[22]['id'] ]; // (10, 11, 23)
		$pairs[] = [ $images[10]['id'], $images[11]['id'], $images[23]['id'] ]; // (11, 12, 24)
		$pairs[] = [ $images[11]['id'], $images[12]['id'], $images[24]['id'] ]; // (12, 13, 25)
		$pairs[] = [ $images[12]['id'], $images[13]['id'], $images[25]['id'] ]; // (13, 14, 26)
		$pairs[] = [ $images[13]['id'], $images[14]['id'], $images[26]['id'] ]; // (14, 15, 27)
		$pairs[] = [ $images[14]['id'], $images[15]['id'], $images[0]['id'] ]; // (15, 16, 1)
		$pairs[] = [ $images[15]['id'], $images[16]['id'], $images[1]['id'] ]; // (16, 17, 2)
		$pairs[] = [ $images[16]['id'], $images[17]['id'], $images[2]['id'] ]; // (17, 18, 3)
		$pairs[] = [ $images[17]['id'], $images[18]['id'], $images[3]['id'] ]; // (18, 19, 4)
		$pairs[] = [ $images[18]['id'], $images[19]['id'], $images[4]['id'] ]; // (19, 20, 5)
		$pairs[] = [ $images[19]['id'], $images[20]['id'], $images[5]['id'] ]; // (20, 21, 6)
		$pairs[] = [ $images[20]['id'], $images[21]['id'], $images[6]['id'] ]; // (21, 22, 7)
		$pairs[] = [ $images[21]['id'], $images[22]['id'], $images[7]['id'] ]; // (22, 23, 8)
		$pairs[] = [ $images[22]['id'], $images[23]['id'], $images[8]['id'] ]; // (23, 24, 9)
		$pairs[] = [ $images[23]['id'], $images[24]['id'], $images[9]['id'] ]; // (24, 25, 10)
		$pairs[] = [ $images[24]['id'], $images[25]['id'], $images[10]['id'] ]; // (25, 26, 11)
		$pairs[] = [ $images[25]['id'], $images[26]['id'], $images[11]['id'] ]; // (26, 27, 12)
		$pairs[] = [ $images[26]['id'], $images[0]['id'], $images[12]['id'] ]; // (27, 1, 13)

		// the next 27 triplets from the second equation
		$pairs[] = [ $images[0]['id'], $images[3]['id'], $images[11]['id'] ]; // (1, 4, 12)
		$pairs[] = [ $images[1]['id'], $images[4]['id'], $images[12]['id'] ]; // (2, 5, 13)
		$pairs[] = [ $images[2]['id'], $images[5]['id'], $images[13]['id'] ]; // (3, 6, 14)
		$pairs[] = [ $images[3]['id'], $images[6]['id'], $images[14]['id'] ]; // (4, 7, 15)
		$pairs[] = [ $images[4]['id'], $images[7]['id'], $images[15]['id'] ]; // (5, 8, 16)
		$pairs[] = [ $images[5]['id'], $images[8]['id'], $images[16]['id'] ]; // (6, 9, 17)
		$pairs[] = [ $images[6]['id'], $images[9]['id'], $images[17]['id'] ]; // (7, 10, 18)
		$pairs[] = [ $images[7]['id'], $images[10]['id'], $images[18]['id'] ]; // (8, 11, 19)
		$pairs[] = [ $images[8]['id'], $images[11]['id'], $images[19]['id'] ]; // (9, 12, 20)
		$pairs[] = [ $images[9]['id'], $images[12]['id'], $images[20]['id'] ]; // (10, 13, 21)
		$pairs[] = [ $images[10]['id'], $images[13]['id'], $images[21]['id'] ]; // (11, 14, 22)
		$pairs[] = [ $images[11]['id'], $images[14]['id'], $images[22]['id'] ]; // (12, 15, 23)
		$pairs[] = [ $images[12]['id'], $images[15]['id'], $images[23]['id'] ]; // (13, 16, 24)
		$pairs[] = [ $images[13]['id'], $images[16]['id'], $images[24]['id'] ]; // (14, 17, 25)
		$pairs[] = [ $images[14]['id'], $images[17]['id'], $images[25]['id'] ]; // (15, 18, 26)
		$pairs[] = [ $images[15]['id'], $images[18]['id'], $images[26]['id'] ]; // (16, 19, 27)
		$pairs[] = [ $images[16]['id'], $images[19]['id'], $images[0]['id'] ]; // (17, 20, 1)
		$pairs[] = [ $images[17]['id'], $images[20]['id'], $images[1]['id'] ]; // (18, 21, 2)
		$pairs[] = [ $images[18]['id'], $images[21]['id'], $images[2]['id'] ]; // (19, 22, 3)
		$pairs[] = [ $images[19]['id'], $images[22]['id'], $images[3]['id'] ]; // (20, 23, 4)
		$pairs[] = [ $images[20]['id'], $images[23]['id'], $images[4]['id'] ]; // (21, 24, 5)
		$pairs[] = [ $images[21]['id'], $images[24]['id'], $images[5]['id'] ]; // (22, 25, 6)
		$pairs[] = [ $images[22]['id'], $images[25]['id'], $images[6]['id'] ]; // (23, 26, 7)
		$pairs[] = [ $images[23]['id'], $images[26]['id'], $images[7]['id'] ]; // (24, 27, 8)
		$pairs[] = [ $images[24]['id'], $images[0]['id'], $images[8]['id'] ]; // (25, 1, 9)
		$pairs[] = [ $images[25]['id'], $images[1]['id'], $images[9]['id'] ]; // (26, 2, 10)
		$pairs[] = [ $images[26]['id'], $images[2]['id'], $images[10]['id'] ]; // (27, 3, 11)

		// the next 27 triplets from the third equation
		$pairs[] = [ $images[0]['id'], $images[4]['id'], $images[10]['id'] ]; // (1, 5, 11)
		$pairs[] = [ $images[1]['id'], $images[5]['id'], $images[11]['id'] ]; // (2, 6, 12)
		$pairs[] = [ $images[2]['id'], $images[6]['id'], $images[12]['id'] ]; // (3, 7, 13)
		$pairs[] = [ $images[3]['id'], $images[7]['id'], $images[13]['id'] ]; // (4, 8, 14)
		$pairs[] = [ $images[4]['id'], $images[8]['id'], $images[14]['id'] ]; // (5, 9, 15)
		$pairs[] = [ $images[5]['id'], $images[9]['id'], $images[15]['id'] ]; // (6, 10, 16)
		$pairs[] = [ $images[6]['id'], $images[10]['id'], $images[16]['id'] ]; // (7, 11, 17)
		$pairs[] = [ $images[7]['id'], $images[11]['id'], $images[17]['id'] ]; // (8, 12, 18)
		$pairs[] = [ $images[8]['id'], $images[12]['id'], $images[18]['id'] ]; // (9, 13, 19)
		$pairs[] = [ $images[9]['id'], $images[13]['id'], $images[19]['id'] ]; // (10, 14, 20)
		$pairs[] = [ $images[10]['id'], $images[14]['id'], $images[20]['id'] ]; // (11, 15, 21)
		$pairs[] = [ $images[11]['id'], $images[15]['id'], $images[21]['id'] ]; // (12, 16, 22)
		$pairs[] = [ $images[12]['id'], $images[16]['id'], $images[22]['id'] ]; // (13, 17, 23)
		$pairs[] = [ $images[13]['id'], $images[17]['id'], $images[23]['id'] ]; // (14, 18, 24)
		$pairs[] = [ $images[14]['id'], $images[18]['id'], $images[24]['id'] ]; // (15, 19, 25)
		$pairs[] = [ $images[15]['id'], $images[19]['id'], $images[25]['id'] ]; // (16, 20, 26)
		$pairs[] = [ $images[16]['id'], $images[20]['id'], $images[26]['id'] ]; // (17, 21, 27)
		$pairs[] = [ $images[17]['id'], $images[21]['id'], $images[0]['id'] ]; // (18, 22, 1)
		$pairs[] = [ $images[18]['id'], $images[22]['id'], $images[1]['id'] ]; // (19, 23, 2)
		$pairs[] = [ $images[19]['id'], $images[23]['id'], $images[2]['id'] ]; // (20, 24, 3)
		$pairs[] = [ $images[20]['id'], $images[24]['id'], $images[3]['id'] ]; // (21, 25, 4)
		$pairs[] = [ $images[21]['id'], $images[25]['id'], $images[4]['id'] ]; // (22, 26, 5)
		$pairs[] = [ $images[22]['id'], $images[26]['id'], $images[5]['id'] ]; // (23, 27, 6)
		$pairs[] = [ $images[23]['id'], $images[0]['id'], $images[6]['id'] ]; // (24, 1, 7)
		$pairs[] = [ $images[24]['id'], $images[1]['id'], $images[7]['id'] ]; // (25, 2, 8)
		$pairs[] = [ $images[25]['id'], $images[2]['id'], $images[8]['id'] ]; // (26, 3, 9)
		$pairs[] = [ $images[26]['id'], $images[3]['id'], $images[9]['id'] ]; // (27, 4, 10)

		//the next 27 triplets from the fourth equation
		$pairs[] = [ $images[0]['id'], $images[2]['id'], $images[7]['id'] ]; // (1, 3, 8)
		$pairs[] = [ $images[1]['id'], $images[3]['id'], $images[8]['id'] ]; // (2, 4, 9)
		$pairs[] = [ $images[2]['id'], $images[4]['id'], $images[9]['id'] ]; // (3, 5, 10)
		$pairs[] = [ $images[3]['id'], $images[5]['id'], $images[10]['id'] ]; // (4, 6, 11)
		$pairs[] = [ $images[4]['id'], $images[6]['id'], $images[11]['id'] ]; // (5, 7, 12)
		$pairs[] = [ $images[5]['id'], $images[7]['id'], $images[12]['id'] ]; // (6, 8, 13)
		$pairs[] = [ $images[6]['id'], $images[8]['id'], $images[13]['id'] ]; // (7, 9, 14)
		$pairs[] = [ $images[7]['id'], $images[9]['id'], $images[14]['id'] ]; // (8, 10, 15)
		$pairs[] = [ $images[8]['id'], $images[10]['id'], $images[15]['id'] ]; // (9, 11, 16)
		$pairs[] = [ $images[9]['id'], $images[11]['id'], $images[16]['id'] ]; // (10, 12, 17)
		$pairs[] = [ $images[10]['id'], $images[12]['id'], $images[17]['id'] ]; // (11, 13, 18)
		$pairs[] = [ $images[11]['id'], $images[13]['id'], $images[18]['id'] ]; // (12, 14, 19)
		$pairs[] = [ $images[12]['id'], $images[14]['id'], $images[19]['id'] ]; // (13, 15, 20)
		$pairs[] = [ $images[13]['id'], $images[15]['id'], $images[20]['id'] ]; // (14, 16, 21)
		$pairs[] = [ $images[14]['id'], $images[16]['id'], $images[21]['id'] ]; // (15, 17, 22)
		$pairs[] = [ $images[15]['id'], $images[17]['id'], $images[22]['id'] ]; // (16, 18, 23)
		$pairs[] = [ $images[16]['id'], $images[18]['id'], $images[23]['id'] ]; // (17, 19, 24)
		$pairs[] = [ $images[17]['id'], $images[19]['id'], $images[24]['id'] ]; // (18, 20, 25)
		$pairs[] = [ $images[18]['id'], $images[20]['id'], $images[25]['id'] ]; // (19, 21, 26)
		$pairs[] = [ $images[19]['id'], $images[21]['id'], $images[26]['id'] ]; // (20, 22, 27)
		$pairs[] = [ $images[20]['id'], $images[22]['id'], $images[0]['id'] ]; // (21, 23, 1)
		$pairs[] = [ $images[21]['id'], $images[23]['id'], $images[1]['id'] ]; // (22, 24, 2)
		$pairs[] = [ $images[22]['id'], $images[24]['id'], $images[2]['id'] ]; // (23, 25, 3)
		$pairs[] = [ $images[23]['id'], $images[25]['id'], $images[3]['id'] ]; // (24, 26, 4)
		$pairs[] = [ $images[24]['id'], $images[26]['id'], $images[4]['id'] ]; // (25, 27, 5)
		$pairs[] = [ $images[25]['id'], $images[0]['id'], $images[5]['id'] ]; // (26, 1, 6)
		$pairs[] = [ $images[26]['id'], $images[1]['id'], $images[6]['id'] ]; // (27, 2, 7)

		// the last 9 triplets from the fifth equation
		$pairs[] = [ $images[0]['id'], $images[9]['id'], $images[18]['id'] ]; // (1, 10, 19)
		$pairs[] = [ $images[1]['id'], $images[10]['id'], $images[19]['id'] ]; // (2, 11, 20)
		$pairs[] = [ $images[2]['id'], $images[11]['id'], $images[20]['id'] ]; // (3, 12, 21)
		$pairs[] = [ $images[3]['id'], $images[12]['id'], $images[21]['id'] ]; // (4, 13, 22)
		$pairs[] = [ $images[4]['id'], $images[13]['id'], $images[22]['id'] ]; // (5, 14, 23)
		$pairs[] = [ $images[5]['id'], $images[14]['id'], $images[23]['id'] ]; // (6, 15, 24)
		$pairs[] = [ $images[6]['id'], $images[15]['id'], $images[24]['id'] ]; // (7, 16, 25)
		$pairs[] = [ $images[7]['id'], $images[16]['id'], $images[25]['id'] ]; // (8, 17, 26)
		$pairs[] = [ $images[8]['id'], $images[17]['id'], $images[26]['id'] ]; // (9, 18, 27)
	}

	return $pairs;
}

$option = $_GET['option'];

if ($option == "generateRandom") {
	$rightAndLeft = $_GET['rightAndLeft']; // In case the scientist chooses to view pictures both right and left.
	$imagesetId = $_GET['imagesetId'];

	$sql = "SELECT * FROM picture WHERE pictureSet = ? AND isOriginal = 0";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imagesetId);
	$sth->execute();
	$images = $sth->fetchAll();

	$imagesToCheck = array(); // CheckOwnerForPicture expects simply id, not a whole class
	foreach($images as $image) {
		$imagesToCheck[] = $image['id'];
	}

	if ($_GET['experimentType'] == "Triplet Comparison") {
		$pairs = makeTripletQueue($images, $rightAndLeft);
	} else {
		$pairs = makeQueue($images, $rightAndLeft);
	}

	$db->beginTransaction();

	$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, NULL);";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pictureQueueId =  $db->lastInsertId();
	$order = 0;

	if ($_GET['experimentType'] == "Triplet Comparison") {
		foreach($pairs as $pair) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[0]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[1]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[2]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$order++;
		}
	} else {
		foreach($pairs as $pair) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[0]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[1]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$order++;
		}
	}

	$db->commit();
	echo json_encode($pictureQueueId);
}

else if ($option == "notRandom") {
	/**
	 * F�r medsendt en array med to bildeideer.  Disse skal inn i en kanskje eksisterendes pictureQueue OM DEN FINNES
	 * Har ikke addet sikkerhetssjekker her.
	 * Kan vel egentlig g� ut i fra at denne funksjonen blir kj�rt en gang for hvert enkelt bildesett som lages. typ ganske mange ganger
	 */
 	$rightAndLeft = $_GET['rightAndLeft'];	//In case the scientist chooses to view pictures both right and left.
	$images = $_GET['images'];
	$pictureQueueId = $_GET['pictureQueueId'];	//This is 0 if it is for a new set.
	$imagesArray = array();

	foreach ($images as $image) {
		$sql = "SELECT * FROM picture WHERE id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$image);
		$sth->execute();
		$imagesArray[] = $sth->fetch();
	}

	$pairs = makeQueue($imagesArray, $rightAndLeft);
	if (count($pairs > 1)) {
		$db->beginTransaction();
		$order;
		if($pictureQueueId == 0) {	//Generate new pictureQueue
			$name = $_GET['name'];
			$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$name);
			$sth->execute();

			$pictureQueueId =  $db->lastInsertId();
			$order = 0;

		} else {					//PictureQueue exists.  Need to get order from pictureOrder
			$sql = "SELECT * FROM pictureorder where pictureQueue = ?
			ORDER BY pOrder DESC
			LIMIT 1";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$pictureQueueId);
			$sth->execute();
			$result = $sth->fetch();
			$order = ($result['pOrder']+1);
		}

		foreach($pairs as $pair) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[0]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();

			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[1]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();
			$order++;
		}
		echo json_encode($pictureQueueId);
	}
	$db->commit();
}

else if($option == "ratingCategory") {
	$db->beginTransaction();
	try {
		//Trenger bare å lagre med eOrder 0
		$imagesetId = $_GET['imagesetId'];

		$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, NULL);";
		$sth = $db->prepare($sql);
		$sth->execute();
		$pictureQueueId =  $db->lastInsertId();

		$sql = "SELECT * FROM picture WHERE pictureSet = ? AND isOriginal = 0;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1, $imagesetId);
		$sth->execute();
		$result = $sth->fetchAll();

		foreach($result as $image) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (0, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$image['id']);
			$sth->bindParam(2,$pictureQueueId);
			$sth->execute();
		}
		$db->commit();
		echo json_encode($pictureQueueId);
		exit;
	} catch(Exception $e) {
		$db->rollBack();
		echo json_encode(0);
		exit;
	}

}

?>
