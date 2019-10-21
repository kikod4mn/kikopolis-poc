<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @coral::meta_description
    @coral::meta_copyright
    @coral::meta_keywords
    @coral::meta_twitter:card
    @coral::meta_twitter:site
    @coral::meta_twitter:title
    @coral::meta_twitter:description
    @coral::meta_twitter:image
    @coral::meta_og:title
    @coral::meta_og:description
    @coral::meta_og:image
    @coral::meta_og:url
    (@x_csrf_token)
    <meta name="robots" content="index, follow">
    @coral::pairedTag_title
    (@asset('frontend', 'css'))
</head>

<body background="@coral::theme_bgPrimary">
(@section::layouts.header)
<div class="wrapper">
    (@section::extend)
</div>
(@includes::layouts.footer)
(@asset('app', 'javascript'))
</body>

</html>