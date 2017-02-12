<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PeriodOfTime
 *
 * @author user
 */
final class PeriodOfTime extends Enum {

    const DAY = 1;
    const NIGHT = 2;

}

final class SeasonCode extends Enum {

    const WINTER = 1;
    const SPRING = 2;
    const SUMMER = 3;
    const AUTUMN = 4;

}

final class PrefCode extends Enum {

    const OKINAWA = 47;

}

final class TargetType extends Enum {

    const JAPANEASE = 1;
    const FOREIGNER = 2;

}

final class Year extends Enum {

    const Y2012 = 2012;
    const Y2015 = 2015;
    const Y2016 = 2016;

}

?>
