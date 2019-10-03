<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="(@csrf_token())">
    <meta name="description" content="<?php echo '$template_description with ca 300, only alphanumeric chars';?>">
    <?php echo '<meta name="og:? property="og:? content="content of the tag here'; ?>
    <meta name="og:<?php echo '$template_og_property_name'; ?>" property="<?php echo '$template_og_property_name'; ?>"
          type="<?php echo '$template_og_property_type'; ?>" content="<?php echo '$template_og_property_content'; ?>" >
    <meta name="robots" content="index, follow">
    <link href="<?php echo '$template_canonical_url'; ?>" rel="canonical">
    <title>{{page_title}}</title>
    (@asset('frontend', 'css'))
</head>

<body>
    (@section::layouts.header)
    <div class="wrapper">
        <div class="row">
            <h4 class=""><u>Limited html allowed</u></h4>
            {!! no_escape !!}
            (@function::countDaysFromBirth(12.04.1987))
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@function::coinFlip())<br>
            (@section::extend)
        </div>
        <div class="">
            <h4 class=""><u>No Html allowed</u></h4>
            <div class="">
                <h1 class="">{{ heading_title }}</h1>
                <p class="">{{ content }}</p>
                <div class="">
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <!-- TODO: Write limits and pagination for loops -->
                    <div class="">
                        (@for::user in users)
                        <h4 class="">User data</h4>
                        <span>User ID : {!! user.id !!}</span><br>
                        <span>User name : {!! user.first_name !!} {!! user.first_name !!}</span><br>
                        <span>User email : {!! user.email !!}</span><br>
                        <img src="{{ user.image }}" alt=""><br>
                        (@endfor)
                    </div>
                    <div class="">
                        (@for::post in posts)
                        <h4 class="">Post info</h4>
                        <span>Post ID : {!% post.id %!}</span><br>
                        <span>Post title : {!% post.title %!}</span><br>
                        <span>Post body : {!% post.body %!}</span><br>
                        <span>Post tags : {!% post.tags %!}</span><br>
                        <img src="{!% post.image %!}" alt=""><br>
                        <span>Post author : {!% post.author_name %!}</span><br>
                        <span>Posted on : {!% post.created_at %!}</span><br>
                        <span>Modified on : {!% post.modified_at %!}</span><br>
                        (@endfor)
                    </div>
                    <div class="">
                        (@for::num in 0..32)
                        {{num}}
                        (@endfor)
                    </div>
                    <div class="">
                        (@for::letter in a..z)
                        {{letter}}
                        (@endfor)
                    </div>
                    <div class="">
                        (@for::team in teams)
                        <h5 class="">{{team}}</h5><br>
                        (@endfor)
                    </div>
                    <div class="">
                        <h4 class="">User data with keys</h4>
                        (@for::user in users)
                        (@for::key, value in user)
                        <span>User {{key}} : {!! value !!}</span><br>
                        (@endfor)
                        (@endfor)
                    </div>
                    <div class=>
                        (@if::cars is same as [])
                        <h4 class="">Cars === []</h4>
                        (@elseif::cars)
                        <h1 class="">Cars isset()</h1>
                        (@endif)

                        (@if::not drivers)
                        <h1>No drivers</h1>
                        (@elseif::drivers)
                        (@for::driver in drivers)
                        {{driver}}
                        (@endfor)
                        (@endif)

                        (@if::users)
                        <h4 class="">Users isset()</h4>
                        (@endif)

                        (@if::cars is not [])
                        <h4 class="">Cars is not []</h4>
                        (@endif)
                    </div>
                </div>
            </div>
        </div>
        <div class="">(@includes::layouts.footer)</div>
        (@asset('app', 'javascript'))
    </div>
</body>

</html>