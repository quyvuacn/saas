<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('foo')) {
    function foo()
    {
        return 'foo';
    }
}

if (!function_exists('can')) {
    function can($can)
    {
        return auth(ADMIN)->user();
    }
}

if (!function_exists('likeEscape')) {
    /**
     * likeEscape
     *
     * SQLのLIKE句で特別な意味を持つ文字をエスケープする。
     * ESCAPE句ではなく文字列内のエスケープで解決する。
     * シングルクォートなどのエスケープは行われないため、
     * SQLとして組み立てる際はプレースホルダを使うなど別途のエスケープが必要。
     *
     * 典型的な使用例
     *  $sql = "… XxxName LIKE :SearchText …";
     *  $param['SearchText'] = '%' . likeEscape($searchText) . '%';
     *
     * CIのドライバでは escape_str($str, TRUE) メソッドが存在している
     *
     * @param string $inputStr 入力文字列
     * @return string エスケープ後の文字列
     */
    function likeEscape($inputStr = '')
    {
        //mysql準拠
        return strtr($inputStr, [
            '%'  => '\%',
            '_'  => '\_',
            '\\' => '\\\\',
        ]);
    }
}

if (!function_exists('machineProductWarning')) {
    function machineProductWarning($percent)
    {
        if ($percent <= 20) {
            return 'bg-danger';
        }
        if ($percent <= 40) {
            return 'bg-warning';
        }
        if ($percent <= 60) {
            return '';
        }
        if ($percent <= 80) {
            return 'bg-info';
        }
        if ($percent <= 100) {
            return 'bg-success';
        }
    }
}

if (!function_exists('sortSearchDate')) {
    function sortSearchDate($date, $time = false)
    {
        if ($time) {
            return '<span class="d-none">' . strtotime($date) . ' ' . $date->format('d-m-Y H:i:s') . '</span><span>' . $date->format('d/m/Y H:i:s') . '</span>';
        } else {
            return '<span class="d-none">' . strtotime($date) . ' ' . $date->format('d-m-Y') . '</span><span>' . $date->format('d/m/Y') . '</span>';
        }
    }
}

if (!function_exists('sortSearchCoin')) {
    function sortSearchCoin($coin)
    {
        return '<span class="d-none">' . $coin . '</span><span>' . number_format($coin) . ' coin</span>';
    }
}

if (!function_exists('sortSearchPrice')) {
    function sortSearchPrice($price, $text = false)
    {
        if ($text) {
            return '<span class="d-none">0</span><span>' . $price . '</span>';
        } else {
            return '<span class="d-none">' . $price . '</span><span>' . number_format($price) . ' <sup>đ</sup></span>';
        }
    }
}

if (!function_exists('sortSearchText')) {
    function sortSearchText($text)
    {
        return '<span class="d-none">' . $text . '</span><span>' . $text . '</span>';
    }
}

if (!function_exists('callApiNotifyFirebase')) {
    /***
     * @param $title
     * @param $message
     * @param $token, user firebase Token
     * @return mixed
     */
    function callApiNotifyFirebase($title, $message, $token)
    {
        try {
            $curl = curl_init();
            $data = [
                'token' => $token,
                'topic' => '',
                'title' => $title,
                'message' => $message
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('api.base_url') . config('api.url_api.notify_firebase'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $response = json_decode($response, true);

            if(!isset($response['code']) || $response['code'] !== 'ES200'){
                Log::info("[callApiNotifyFirebase][PushFailed][user - {$token}][$title]--" . json_encode($response));
            }

            return $response;
        } catch (\Exception $e){
            Log::error('[callApiNotifyFirebase][PushError]--' . $e->getMessage());
        }
    }
}
