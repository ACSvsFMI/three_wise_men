<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
session_start();
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_TasksService.php';

$client = new Google_Client();
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
 $client->setClientId('1049554536267.apps.googleusercontent.com');
 $client->setClientSecret('iRqUSPdijenV_LT7CksQUMen');
 $client->setRedirectUri('http://localhost/hackathon/index.php');
 //$client->setApplicationName("Tasks_Example_App7");
$tasksService = new Google_TasksService($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $client->setAccessToken($client->authenticate($_GET['code']));
  $_SESSION['access_token'] = $client->getAccessToken();
}

if (isset($_GET['code'])) {
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
?>
<!doctype html>
<html>
<head>
  <title>Tasks API Sample</title>
  <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans:regular,bold' />
  <link rel='stylesheet' href='css/style.css' />
  <style>
    html, body, iframe, ul, li {
        padding: 0;
        border: 0;
        margin: 0;
    }
    ul {
        list-style: none;
    }
  </style>
</head>
<body>
<div id='container'>
  <div id='main'>

<ul>
<?php

  $list = $tasksService->tasklists->listTasklists();
  $list = $list['items'][0];

  if(isset($_GET['insert'])) {
      // add task
      try {
          $task = new Google_Task();
          $task->setTitle($_GET['insert']);
          $result = $tasksService->tasks->insert($list['id'], $task);
      } catch(Exception $e) {
          echo $e;
      }
      header('Location: /hackathon/');
  }

  $tasks = $tasksService->tasks->listTasks($list['id']);
  foreach ($tasks['items'] as $item) {
    echo "<li>{$item['title']}</li>";
  }
?>
</ul>
  </div>
</div>
</body>
</html>
<?php $_SESSION['access_token'] = $client->getAccessToken(); ?>
