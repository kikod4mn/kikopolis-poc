<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{page_title}}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    (@asset('style', 'css'))
</head>

<body>
    <div class="container justify-content-center align-items-center">
        <div class="row">
            <h4 class="col-12 text-center"><u>Limited html allowed</u></h4>
            (@section::extend)
        </div>
        <div class="row">
            <h4 class="col-12 text-center"><u>No Html allowed</u></h4>
            <div class="col-9">
                <h1 class="text-center">{{ heading_title }}</h1>
                <p class="text-justify">{{ content }}</p>
                (@includes::layouts.pointless.section-main)
                (@function::countDaysFromBirth)
            </div>
            <div class="col-3">(@includes::layouts.pointless.sidebar)</div>
        </div>
        <div class="row justify-content-center">(@includes::layouts.footer)</div>
        (@asset('main', 'javascript'))
    </div>
</body>

</html>