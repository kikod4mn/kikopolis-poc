<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{page_title}}</title>
</head>

<body>
    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
        <div style="display:flex; justify-content:center; align-items:center; margin:100px;">
            (@section::extend)
            (@includes::layouts.sidebar)
        </div>
        (@function::countDaysFromBirth)
    </div>
</body>

</html>