<?php
function url_get_html($url) {
    // инициализируем cURL
    $ch = curl_init();
    // устанавливаем url с которого будем получать данные
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'cURL');
    // устанавливаем опцию чтобы содержимое вернулось нам в string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // выполняем запрос
    $output = curl_exec($ch);
    // закрываем cURL
    curl_close($ch);
    // возвращаем содержимое
    return $output;
}

$homepage = url_get_html('https://mbasic.facebook.com/VPNBookNews/');
preg_match('/Password: ([^<]+)/', $homepage, $matches);

// var_dump($matches);
print($matches[1])
// print_r($matches);
?>
