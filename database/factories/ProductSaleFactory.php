<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProductSaleHistory;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(ProductSaleHistory::class, function (Faker $faker) {
    return [
        'code'           => $faker->randomNumber(),
        'merchant_id'    => 1,
        'machine_id'     => $faker->numberBetween(1, 20),
        'user_id'        => 1,
        'product_id'     => 1,
        'price'          => 10000,
        'payment_method' => 1,
        'status'         => $faker->numberBetween(0, 2),
        'checksum'       => md5($faker->name),
    ];
});

// Faker gụideline
// method
// name()
// firstName()
// title()
// creditCardNumber()
// iban()
// words(3)
// sentence(3)
// sentences(3)
// paragraph(3)
// paragraphs(3)
// text(200)
// realText()
// password()
// slug()

/*
@method string amPm($max = 'now')
@method string date($format = 'd/m/Y', $max = 'now')
@method string dayOfMonth($max = 'now')
@method string dayOfWeek($max = 'now')
@method string iso8601($max = 'now')
@method string month($max = 'now')
@method string monthName($max = 'now')
@method string time($format = 'H:i:s', $max = 'now')
@method int unixTime($max = 'now')
@method string year($max = 'now')

@method \DateTime dateTime($max = 'now', $timezone = null)
@method \DateTime dateTimeAd($max = 'now', $timezone = null)
@method \DateTime dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
@method \DateTime dateTimeInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
@method \DateTime dateTimeThisCentury($max = 'now', $timezone = null)
@method \DateTime dateTimeThisDecade($max = 'now', $timezone = null)
@method \DateTime dateTimeThisYear($max = 'now', $timezone = null)
@method \DateTime dateTimeThisMonth($max = 'now', $timezone = null)

@method int randomNumber($nbDigits = null, $strict = false)
@method int|string|null randomKey(array $array = array())
@method int numberBetween($min = 0, $max = 2147483647)
@method float randomFloat($nbMaxDecimals = null, $min = 0, $max = null)

@method mixed randomElement(array $array = array('a', 'b', 'c'))
@method array randomElements(array $array = array('a', 'b', 'c'), $count = 1, $allowDuplicates = false)
@method array|string shuffle($arg = '')
@method array shuffleArray(array $array = array())
@method string shuffleString($string = '', $encoding = 'UTF-8')
@method string numerify($string = '###')
@method string lexify($string = '????')
@method string bothify($string = '## ??')
@method string asciify($string = '****')
@method string regexify($regex = '')
@method string toLower($string = '')
@method string toUpper($string = '')

@method Generator optional($weight = 0.5, $default = null)
@method Generator unique($reset = false, $maxRetries = 10000)
@method Generator valid($validator = null, $maxRetries = 10000)
@method mixed passthrough($passthrough)

@method string file($sourceDirectory = '/tmp', $targetDirectory = '/tmp', $fullPath = true)
@method string imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null, $gray = false)
@method string image($dir = null, $width = 640, $height = 480, $category = null, $fullPath = true, $randomize = true, $word = null)
@method string randomHtml($maxDepth = 4, $maxWidth = 4)
*/
