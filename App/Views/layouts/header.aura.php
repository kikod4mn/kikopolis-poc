@section('header')
<header>
    <nav backgroung="@coral::theme_bgSecondary">
        <div class="text-center container-fluid">
            <h1 style="color:@coral::theme_logoColor;">The site title</h1>
            <ul class="nav justify-content-center">
                <li class="nav-item"><a class="nav-link text-secondary" style="color:@coral::theme_linkColor;" href="home">Home</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" style="color:@coral::theme_linkColor;" href="contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" style="color:@coral::theme_linkColor;" href="faq">F.A.Q</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" style="color:@coral::theme_linkColor;" href="about">About</a></li>
            </ul>
        </div>
    </nav>
</header>
@endsection