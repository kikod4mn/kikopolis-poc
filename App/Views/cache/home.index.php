<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($page_title, 'escape', ''); ?></title>
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
            <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($no_escape, 'no-escape', ''); ?>
            11856
            
<div>
    <h1 class="text-center"><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($heading_title, 'allow-html', ''); ?></h1>
    <p class="text-justify"><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($content, 'allow-html', ''); ?></p>
</div>

        </div>
        <div class="row">
            <h4 class="col-12 text-center"><u>No Html allowed</u></h4>
            <div class="col-9">
                <h1 class="text-center"><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($heading_title, 'escape', ''); ?></h1>
                <p class="text-justify"><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($content, 'escape', ''); ?></p>
                
<ul>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
    <li><a href="#">Link Item</a></li>
</ul>

                <div class="row">
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <div class="col-12 border border-danger">
                        
                <?php foreach($users as $user): ?>
                        <h4 class="text-danger">User data</h4>
                        <span>User ID : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($user, 'no-escape', 'id'); ?></span><br>
                        <span>User name : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($user, 'no-escape', 'first_name'); ?> <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($user, 'no-escape', 'first_name'); ?></span><br>
                        <span>User email : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($user, 'no-escape', 'email'); ?></span><br>
                        <img src="<?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($user, 'escape', 'image'); ?>" alt=""><br>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        
                <?php foreach($posts as $post): ?>
                        <h4 class="text-danger">Post info</h4>
                        <span>Post ID : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_id'); ?></span><br>
                        <span>Post title : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_title'); ?></span><br>
                        <span>Post body : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_body'); ?></span><br>
                        <span>Post tags : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_tags'); ?></span><br>
                        <img src="<?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_image'); ?>" alt=""><br>
                        <span>Post author : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_author_name'); ?></span><br>
                        <span>Posted on : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_created_at'); ?></span><br>
                        <span>Modified on : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($post, 'allow-html', 'post_modified_at'); ?></span><br>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        
                <?php $var = range('0', '32'); ?>
                <?php foreach($var as $num): ?>
                
                        <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($num, 'escape', ''); ?>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        
                <?php $var = range('a', 'z'); ?>
                <?php foreach($var as $letter): ?>
                
                        <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($letter, 'escape', ''); ?>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        
                <?php foreach($teams as $team): ?>
                        <h5 class="text-center"><?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($team, 'escape', ''); ?></h5><br>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        <h4 class="text-danger">User data with keys</h4>
                        
                <?php foreach($users as $user): ?>
                        <?php foreach($user as $key => $value): ?>
                        <span>User <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($key, 'escape', ''); ?> : <?php echo \Kikopolis\Core\Aurora\Aurora::k_echo($value, 'no-escape', ''); ?></span><br>
                        <?php endforeach ?>
                        <?php endforeach ?>
                    </div>
                    <div class="col-12 border border-danger">
                        (@if::users)
                        <h4 class="text-danger">User data is present and can be echoed</h4>
                        (@endif)
                    </div>
                    <div class="col-12 border border-danger">
                        (@if::not cars)
                        <h4 class="text-danger">Car data is not present!!!!</h4>
                        (@endif)
                    </div>
                </div>
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