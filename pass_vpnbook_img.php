<?php
// размер символа
$wchar = 9;
$hchar = 13;
$strDict = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ';
$imgDict = imagecreatetruecolor(2 + strlen($strDict)* $wchar, $hchar);
$bg = imagecolorallocate($imgDict, 0xF6, 0xF6, 0xF6);
$textcolor = imagecolorallocate($imgDict, 0x4C, 0x4C, 0x4C);
imagefill($imgDict, 0, 0, $bg);
imagestring($imgDict, 5, 2, 0, $strDict, $textcolor);


// инициализируем cURL
$ch = curl_init();
// устанавливаем url, с которого будем получать данные
curl_setopt($ch, CURLOPT_URL, 'https://www.vpnbook.com/password.php');
// устанавливаем опцию, чтобы содержимое вернулось нам в string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
// выполняем запрос
$output = curl_exec($ch);
// закрываем cURL
curl_close($ch);

$imgOCR = imagecreatefromstring($output);
// $imgOCR = imageCreateFromPng('password.png');
// в текущее изображение может поместиться 10 полных символов. 2 + 10*9 = 92 < 100
$maxchar = floor((imagesx($imgOCR) - 2) / 9);
$imgBox = imagecreatetruecolor($wchar, $hchar);
$hashDict = Array();

// генерируем словарь
for ($k = 0; $k < strlen($strDict) ; $k++) {
	imagecopy($imgBox, $imgDict, 0, 0, 2 + $k * $wchar, 0, $wchar, $hchar);
	$hashStr = "";
	for($y = 0; $y < $hchar ; $y++)
		for($x = 0; $x < $wchar; $x++) $hashStr .= (imagecolorat($imgBox, $x, $y) != 0xF6F6F6)? '1': '0';
	$hashDict[$hashStr] = $strDict[$k];
}

// ищем символы по словарю
  
for ($k = 0; $k < $maxchar ; $k++) {
	imagecopy($imgBox, $imgOCR, 0, 0, 2 + $k * $wchar, 0, $wchar, $hchar);
	$hashStr = "";
	for($y = 0; $y < $hchar ; $y++)
		for($x = 0; $x < $wchar; $x++) $hashStr .= (imagecolorat($imgBox, $x, $y) != 0xF6F6F6)? '1': '0';
//    $tempchar = $hashDict[$hashStr];
//    if ($tempchar==' ') break;
//-----------------------------------------------------------------------
    $tempchar = $hashDict[$hashStr];
    if ($tempchar=='u' || $tempchar=='y') // проблема совпадения символов
      $tempchar = (mt_rand(0, 1))? 'u': 'y';
      //$tempchar = (time() / 60 % 60 % 2)? 'u': 'y';
    elseif ($tempchar==' ') break;
    $ntempchar = rtrim($tempchar," \t\n\r\0\x0B\n");
//-----------------------------------------------------------------------
    print($ntempchar);
}

/*header('Content-type: image/png');
imagepng($imgOCR);
*/
//var_dump($hashDict);
imagedestroy($imgDict);
imagedestroy($imgOCR);
imagedestroy($imgBox);
?>

