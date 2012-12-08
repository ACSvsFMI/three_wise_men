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
  <link rel='stylesheet' href='css/style.css' />
  <link href='http://fonts.googleapis.com/css?family=Gafata' rel='stylesheet' type='text/css'>

  <style>

    html, body, iframe, ul, li {
        padding: 0;
        border: 0;
        margin: 0;
        font-family: 'Gafata', sans-serif;
        width: 210px;
    }

    ul {
        list-style: none;
    }
    li{

      margin-left:10px;
    }
  </style>
</head>
<body>
<div id='container'>
<div id='main'>
    <form action="." method="get">
    <div id="header_tasks" style="min-height:65px; width:265px;">
    <div id="add_task" style="margin-top:10px;padding-left:10px;min-height:60px;border-bottom:1px solid #ccc; width:160px;float:left;">
     <input style="height:20px;width:150px;" placeholder="Your task here" type="text" id="insert" name="insert"/> 
      <div style="margin-top:5px;"><input type="date" style="width:150px;height:20px;" name="due"></div>
      </div>
      <div id="add" style="margin-top:30px;width:20px;float:left;"><a href="javascript:void(0);" style="text-decoration:none;font-size:20px;text-decoration:bold;color:#2fb7ea;margin-top:3px;" id="add" onclick="document.forms[0].submit()">Add</a> </div>
    </form>
</div>
<h3>Later</h3>
<ul>
<?php

  $list = $tasksService->tasklists->listTasklists();
  $list = $list['items'][0];

  if(isset($_GET['insert'])) {
      // add task
      try {
          $task = new Google_Task();
          $task->setTitle($_GET['insert']);
          if(isset($_GET['due']) && $_GET['due']) {
            $task->setDue(date(DateTime::RFC3339, strtotime($_GET['due'])));
          }
          $result = $tasksService->tasks->insert($list['id'], $task);
      } catch(Exception $e) {
          echo $e;
      }
      header('Location: /hackathon/');
  }

  // no date 
  $tasks = $tasksService->tasks->listTasks($list['id']);

  foreach ($tasks['items'] as $item) {
      if(!isset($item['due'])) {
        echo "<li>{$item['title']}</li>"; 
      }
  }

?>
</ul>
<h3>Today</h3>
<ul>
<?php
  // today
  $day_start = strtotime('today');
  $day_end = strtotime('tomorrow');
  foreach ($tasks['items'] as $item) {
      if(isset($item['due']) && $day_start <= strtotime($item['due']) && strtotime($item['due']) <= $day_end) {
        echo "<li>{$item['title']}</li>"; 
      }
  }
?>
</ul>
<h3>This Week</h3>
<ul>
<?php
  // this week
  $week_start = strtotime('last Monday');
  $week_end = strtotime('next Monday');

  foreach($tasks['items'] as $item) {
      if(isset($item['due']) && $week_start <= strtotime($item['due']) && strtotime($item['due']) <= $week_end) {
        echo "<li>{$item['title']}</li>"; 
      }
  }
?>
</ul>
  </div>
</div>
</body>
</html>
<?php $_SESSION['access_token'] = $client->getAccessToken(); ?>
