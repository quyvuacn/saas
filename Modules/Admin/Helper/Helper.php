<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

if (!function_exists('bar')) {
    function bar()
    {
        return 'bar';
    }
}

if (!function_exists('makeImageProduct')) {
    function makeImageProduct($img)
    {
        $url = (strpos($img, 'http') === 0) ? '' : env('IMG_URL');
        $imgName = $url . $img;
        return $imgName;
    }
}

if (!function_exists('makeImageAds')) {
    function makeImageAds($img)
    {
        $url = (strpos($img, 'http') === 0) ? '' : env('ADS_URL');
        $imgName = $url . $img;
        return $imgName;
    }
}

if (!function_exists('getLinkAppVersion')) {
    function getLinkAppVersion($link)
    {
        $url = (strpos($link, 'http') === 0) ? '' : env('APP_VERSION');
        $file = $url . $link;
        return $file;
    }
}

if (!function_exists('convertDateFlatpickr')) {
    function convertDateFlatpickr($date, $format = 'Y-m-d')
    {
        $valueDateFlatpickr = explode('/', $date);
        $dateDefault = implode('-', array_reverse($valueDateFlatpickr));
        return date($format, strtotime($dateDefault));
    }
}

if (!function_exists('convertDateTimeFlatpickr')) {
    function convertDateTimeFlatpickr($date, $format = 'Y-m-d H:i:s')
    {
        $valueDateTimeFlatpickr = explode(' ', $date);
        $dateDefault = implode('-', array_reverse(explode('/', $valueDateTimeFlatpickr[0])));
        return date($format, strtotime($dateDefault . ' ' . $valueDateTimeFlatpickr[1]));
    }
}

if (!function_exists('sendMailCustom')) {
    function sendMailCustom($dataMail){
        $view = $dataMail['view'];
        $to = $dataMail['to'];
        $subject = $dataMail['subject'];
        $data = $dataMail['data'] ?? [];
        $from = $dataMail['from'] ?? 'no-reply@1giay.vn';
        $title = $dataMail['title'] ?? '[1giay.vn] System Notifications';
        try {
            Mail::send($view, $data, function ($m) use ($to, $subject, $data, $from, $title) {
                $m->from($from, $title);
                $m->to($to)->subject($subject);
            });
            return true;
        } catch (Exception $e){
            Log::error("[SendMailError][{$view}][{$to}][{$subject}]-- " . $e->getMessage());
            return false;
        }
    }
}
