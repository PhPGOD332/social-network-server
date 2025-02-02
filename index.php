<?php

require __DIR__ . '/vendor/autoload.php';

use pumast3r\api\routers;

//function getFormData($method): array {
//	if (isset($_SERVER['HTTP_ORIGIN'])) {
//		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//		header('Access-Control-Allow-Credentials: true');
//		header('Access-Control-Max-Age: 86400');
//	}
//
//	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
//			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//		}
//
//		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
//			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
//		}
//
//		exit(0);
//	}
//
//  if ($method === 'GET') return $_GET;
//  if ($method === 'POST') return $_POST;
//
//  $data = array();
//  $exploded = explode('&', file_get_contents('php://input'));
//
//  foreach($exploded as $pair) {
//      $item = explode('=', $pair);
//      if (count($item) == 2) {
//          $data[urldecode($item[0])] = urldecode($item[1]);
//      }
//  }
//
//  return $data;
//}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    exit(0);
}

header('Content-Type: application/json');


$method = $_SERVER['REQUEST_METHOD'];
//$formData = getFormData($method);
$formData = json_decode(file_get_contents('php://input'), true);

$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

$router = $urls[0];
$urlData = array_slice($urls, 1);

include_once 'src/routers/' . $router . '.php';
routers\route($method, $urlData, $formData);