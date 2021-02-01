<?php
namespace App\Classes;

class PairedComparison
{
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
}
