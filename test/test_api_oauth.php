<?php

//get request url
if (array_key_exists('url',$_REQUEST['data'])) {
    $apiurl = $_REQUEST['data']['url'];
    unset($_REQUEST['data']['url']);
} else {
    $apiurl = $_REQUEST['submitUrl'];
    unset($_REQUEST['submitUrl']);
}
$apiPara = $_REQUEST;
if (isset($_FILES['paper_data'])) {
    $filePath = '/tmp/'.$_FILES['paper_data']['name'];
    move_uploaded_file($_FILES['paper_data']['tmp_name'], $filePath);
    $apiPara['paper_data'] = '@'.$filePath;
}

$ch = curl_init();

$oauthServerUrl = 'https://demo.elana.hklms.org/oauth.php';
$oauthSettings->client_id = '19301769210302d5f082.las_api';
$oauthSettings->client_secret = 'fdd43ec220c02cfb50d370530657b2dc59ce5631';
/*
$oauthServerUrl = 'https://cls.hkteducation.com/clms/api/oauth.php';
$oauthSettings->client_id = 'b3afcceddb9289b91cfd.test.api';
$oauthSettings->client_secret = '825d2c481fbf0a53504916ba3bee4626c6e089e2';
*/
$url = "$oauthServerUrl/access";
$para->grant_type = 'client_credentials';
$para->client_id = $oauthSettings->client_id;
$para->client_secret = $oauthSettings->client_secret;
$para->scope = '';
//$para->redirect_uri = $lemoUrl;
//$para->code = $code;
$para = http_build_query($para);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
$info = curl_exec($ch);
if (!curl_errno($ch)) {
    $accessInfo = json_decode($info);
    $accessInfo->expires = time() + $accessInfo->expires_in;
    $accessInfo->token_type = ucwords($accessInfo->token_type);
    unset($accessInfo->expires_in);
    if ($accessInfo) {
        $accessCode = $accessInfo;
    }
} else {
    echo 'Get access token fail.';
    exit();
}

curl_close($ch);

unset($ch);
unset($para);
$ch = curl_init();
if (isset($apiPara['paper_data'])) {
    $para = $apiPara;
} else {
    $para = http_build_query($apiPara);
}
$curl_header = array(
    "Authorization: $accessCode->token_type $accessCode->access_token"
);

curl_setopt($ch, CURLOPT_URL, $apiurl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
$info = curl_exec($ch);
if (!curl_errno($ch)) {
    echo $info;
} else {
    echo 'Fail';
}
curl_close($ch);
?>
