<?php
$manifestJson = file_get_contents(url('/js/phmoney_assets/manifest.json'));
$manifest = json_decode($manifestJson, true);
$main_ts = url('/js/phmoney_assets/' . $manifest['src/main.ts']['file']);
$main_css = url('/js/phmoney_assets/' . $manifest['src/main.ts']['css'][0]);
?>

  <head>
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
    </script>

    <!-- Google Captcha -->
    <script src="https://www.google.com/recaptcha/api.js"></script>

    <script type="module" crossorigin src="{{ $main_ts }}"></script>
    <link rel="stylesheet" href="{{ $main_css }}" />
  </head>
  <body>
    <div id="app_gnucash_component"></div>
  </body>
</html>
