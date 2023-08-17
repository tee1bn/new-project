<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- jQuery library -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>

  <!-- Popper JS -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <link href="<?= asset; ?>/guest/assets/flags/css/flag-icon.css" rel="stylesheet">
  <link href="<?= asset; ?>/guest/lib/%40fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">


  <title> <?= $page_title; ?> | <?= project_name; ?></title>
  <meta content='<?= domain; ?>' property='og:url' />

  <meta name='description' content="<?= ($page_description != '') ? $page_description : $default_description; ?>">
  <link rel="stylesheet" href="<?= asset; ?>/guest/assets/css/dashforge.css">
  <link rel="stylesheet" href="<?= asset; ?>/guest/assets/css/dashforge.dashboard.css">
  <link rel="stylesheet" href="<?= asset; ?>/guest/assets/css/dashforge.demo.css">

  <!-- 
  <script src="<?= asset; ?>/angulars/angularjs.js"></script>
  <script src="<?= asset; ?>/angulars/angular-sanitize.js"></script>
  <link href="<?php echo $asset; ?>select2/dist/css/select2.min.css" rel="stylesheet">
 -->
  <script>
    // let $base_url = "<?= domain; ?>";
    // var app = angular.module('app', ['ngSanitize']);
  </script>
</head>

<body>

  <div ng-app='app' id="content" class=" pd-20">
    <nav class="navbar navbar-expand-md bg-dark navbar-dark mb-3 sticky-top">
      <!-- Brand -->
      <a class="navbar-brand" href="#"><?= $this->app_name(); ?></a>

      <!-- Toggler/collapsibe Button -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar links -->
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="<?= domain; ?>/embed/predictions_page">Hot predictions🔥</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= domain; ?>/embed/converted_codes">Conversions</a>
          </li>
        </ul>
      </div>
    </nav>