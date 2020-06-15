<?php

namespace App\Util\Helper;

class Generator {

    public static function genRandomSixDigitNumber() : int {

        $today = date('YmdHi');
        $startDate = date('YmdHi', strtotime('-10 days'));
        $range = $today - $startDate;
        $rand1 = rand(0, $range);
        $rand2 = rand(0, 600000);

        return  $value = ($rand1 + $rand2);
    }

}
