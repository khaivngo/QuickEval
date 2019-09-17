<?php

namespace App\Classes;

class Algorithms {
  public function make_queue ($images, $imagesShownRightAndLeft) {
    $pairs = [];
    $index = 1;
    $arrIndex = 0;

    foreach ($images as $image) {
      for ($i = $index; $i < count($images); $i++ ) {
        $pairs[$arrIndex][0] = $image['id'];
        $pairs[$arrIndex][1] = $images[$i]['id'];
        if ($imagesShownRightAndLeft == 1) {
          $arrIndex++;
          $pairs[$arrIndex][0] = $images[$i]['id'];
          $pairs[$arrIndex][1] = $image['id'];
        }
        $arrIndex++;
      }
      $index++;
    }
    shuffle($pairs); # https://www.php.net/manual/en/function.shuffle.php

    return $pairs;
  }

  /**
   * 
   */
  public function shuffle_the_cards ($cards) {
    $sorted = [];
    for ($i = 0; $i < count($cards); $i += 2) {
        $sorted[] = array($cards[$i], $cards[$i + 1]);
    }
    shuffle($sorted);

    $c = count($sorted) - 1;
    for ($i = 0; $i <= $c; $i++) {
        while ($this->trade_card(
            $sorted[$i],
            $sorted[rand(0, $c)])
        );
    }

    $final = [];
    foreach ($sorted as $pictures) {
        foreach ($pictures as $picture) {
            $final[] = $picture;
        }
    }

    return $final;
  }

  private function trade_card (&$card1, &$card2) {
    if ( $card1[0] == $card2[0] && $card1[1] == $card2[1] ) {
        return true;
    } else {
        $temp = $card1;
        $card1 = $card2;
        $card2 = $temp;

        return false;
    }
  }
}
