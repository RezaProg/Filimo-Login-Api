<?php

# Helper functions
# -------------------------------------------------------------------
function isNullOrEmpty($value)
{
    return $value == null || $value == "";
}
function generateGUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
function getStringBetween($string, $start, $end)
{
    $string = ' ' . $string;
    $ini    = strpos($string, $start);
    if ($ini == 0)
        return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
# -------------------------------------------------------------------

$user = $_GET["user"];
$pass = $_GET["pass"];
$guid = generateGUID();

if (isNullOrEmpty($user))
    return;
if (isNullOrEmpty($pass))
    return;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.filimo.com/_/api/fa/v1/user/Authenticate/auth?v=4&guid=$guid");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$response = curl_exec($ch);
curl_close($ch);
//echo $response;
$json    = json_decode($response, true);
$temp_id = $json["data"]["attributes"]["temp_id"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.filimo.com/_/api/fa/v1/user/Authenticate/signin_step1?v=4&account=$user&temp_id=$temp_id&guid=$guid");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$response = curl_exec($ch);
curl_close($ch);
//echo $response;
$json    = json_decode($response, true);
$temp_id = $json["data"]["attributes"]["temp_id"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.filimo.com/_/api/fa/v1/user/Authenticate/signin_step2?v=4&temp_id=$temp_id&account=$user&codepass_type=pass&code=$pass&guid=$guid");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$response = curl_exec($ch);
curl_close($ch);
//$json = json_decode($response, true);
header("Content-type: application/json");
echo $response;
