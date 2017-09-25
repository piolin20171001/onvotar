<?php

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;


$baseUrl = 'https://onvotar.garantiespelreferendum.com/db/'; // 46/28.db
$chars = array_merge(range('0', '9'), range('a', 'z'));
$combinations = [];
$client = new Client();

foreach ($chars as $c1) {
    foreach ($chars as $c2) {
        $combinations[] = $c1 . $c2;
    }
}

foreach ($combinations as $comb1) {
    foreach ($combinations as $comb2) {
        $path = "$comb1/$comb2.db";
        if (!file_exists($comb1)) {
            mkdir($comb1);
        }
        if (file_exists($path)) {
            continue;
        }
        $url = $baseUrl . $path;
        echo $url, "\n";
        try {
            $response = $client->request('GET', $url, ['sink' => $path]);
            if ($response->getStatusCode() == 404) {
                echo "Not found? Save as empty: $path\n";
                unlink($path);
                touch($path);
            } else if (filesize($path) < 10000) {
                echo "Timeout? Deleting: $path\n";
                unlink($path);
            } else {
                echo "Success\n";
            }
        } catch (RequestException $e) {
            if (!$e->hasResponse() || $e->getResponse()->getStatusCode() == '404') {
                echo "Not found? Save as empty: $path\n";
                unlink($path);
                touch($path);
            } else {
                echo "Exception\n";
                unlink($path);
                echo Psr7\str($e->getRequest());
            }
        }
    }
}
