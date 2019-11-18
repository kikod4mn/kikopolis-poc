<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @x_csrf_token
    <meta name="robots" content="index, follow">
    @asset('https://use.fontawesome.com/releases/v5.3.1/css/all.css', 'css')
    @asset('jquery', 'javascript')
    @asset('frontend', 'css')
    <link rel="shortcut icon" type="image/x-icon" href="@@urlroot/favicon.ico" />
    <link rel="shortcut icon" href="@@urlroot/favicon.ico" />
    <title>kikopolis.tech</title>
</head>

<body class="leading-normal tracking-normal text-white bg-gray-700">
@section::layouts.header
@section::layouts.nav
<div class="container mx-auto mt-5">
    @extend::template
</div>
@section::layouts.footer
    @asset('app', 'javascript')
</body>

</html>