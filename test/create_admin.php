<?php
use Las\Core\User\User;
use Las\Tools\Mongo\MongoDao;
use Las\Core\Oauth\OauthAuthorizationCode;
use Las\Core\Oauth\OauthAccessToken;
use Las\Core\Oauth\OauthClient;
use Las\Core\Oauth\OauthRefreshToken;
use Las\Core\Oauth\OauthSession;
require_once(dirname(__FILE__) . '/../inc.php');

MongoDao::ensureIndex(User::COLLECTION, array('loginname' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthAuthorizationCode::COLLECTION, array('authorization_code' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthAccessToken::COLLECTION, array('access_token' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthClient::COLLECTION, array('client_id' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthRefreshToken::COLLECTION, array('refresh_token' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthSession::COLLECTION, array('sessionkey' => 1),array('dropDups' => 1,'unique' => 1));

MongoDao::ensureIndex(OauthSession::COLLECTION, array('sessionkey' => 1, 'sessiontoken'=>1),array('dropDups' => 1,'unique' => 1));



$login= "admin";
$password = "lmsDEV-0";

$cname = "Admin";
$ename = "Admin";

User::createUser($login, $password, $cname, $ename);




?>