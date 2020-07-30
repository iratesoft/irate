<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$app->param('siteTitle')}</title>
    {$asset->styles()}
    <script>window.BASE_URL = '{$baseUrl}';</script>
  </head>
  <body>
  {include 'base/navigation.tpl'}
