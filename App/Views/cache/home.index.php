<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo escape($page_title); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link href='http://localhost/kikopolis_poc/public/css/style.css' rel='stylesheet'>
</head>

<body>
    
<header>
    <nav>
        <div class="text-center container-fluid">
            <h1>The site header</h1>
            <ul class="nav justify-content-center">
                <li class="nav-item"><a class="nav-link text-secondary" href="#">Nav item</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="#">Nav item</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="#">Nav item</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="#">Nav item</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="#">Nav item</a></li>
            </ul>
        </div>
    </nav>
</header>

    <div class="container justify-content-center align-items-center">
        <div class="row">
            <h4 class="col-12 text-center"><u>Limited html allowed</u></h4>
            <?php echo $no_escape; ?>
            
<div>
    <h1 class="text-center"><?php echo outputSafeHtml($heading_title); ?></h1>
    <p class="text-justify"><?php echo outputSafeHtml($content); ?></p>
</div>

        </div>
        <div class="row">
            <h4 class="col-12 text-center"><u>No Html allowed</u></h4>
            <div class="col-9">
                <h1 class="text-center"><?php echo escape($heading_title); ?></h1>
                <p class="text-justify"><?php echo escape($content); ?></p>
                
<ul>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
</ul>

                
            <?php foreach($users as $user): ?>
                
                <?php echo outputMiscValue($user, 'id'); ?>
                <?php echo outputMiscValue($user, 'name'); ?>
                <?php echo outputMiscValue($user, 'email'); ?>
                
            <?php endforeach ?>
                
            <?php foreach($posts as $post): ?>
                
                <?php echo outputMiscValue($post, 'id'); ?>
                <?php echo outputMiscValue($post, 'title'); ?>
                
            <?php endforeach ?>
            </div>
            <div class="col-3">
<div class="bg-secondary">
    <h4>The sidebar</h4>
    <ul>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
        <li style="color:bisque;">Sidebar item</li>
    </ul>
</div>
</div>
        </div>
        <div class="row justify-content-center">
<h3 class="text-center">The footer</h3>
</div>
        <script src='http://localhost/kikopolis_poc/public/js/main.js'></script>
    </div>
</body>

</html>