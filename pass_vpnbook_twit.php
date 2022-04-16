<?php
function url_get_html($url) {
    // инициализируем cURL
    $ch = curl_init();
    // устанавливаем url с которого будем получать данные
    curl_setopt($ch, CURLOPT_URL, $url);
    // устанавливаем опцию чтобы содержимое вернулось нам в string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // выполняем запрос
    $output = curl_exec($ch);
    // закрываем cURL
    curl_close($ch);
    // возвращаем содержимое
    return $output;
}

$homepage = url_get_html('https://twitter.com/vpnbook');
preg_match('/Password: ([^<]+)/', $homepage, $matches);

// Print the entire match result
// var_dump($matches);
print($matches[1])
// print_r($matches);
?>
