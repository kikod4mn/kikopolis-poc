(@extends::layouts.base)

@section('extend')
<div class="container">
    <h1 class="h3 text-center">All web-pages available for content management</h1>
    <div class="row my-3">
        (@for::page in pages)
        <a href="@@urlroot/content/{{page.id}}/edit" class="btn btn-secondary">{{ page.page_route }}</a>
        (@endfor)
    </div>
    <div class="row my-3">
        <a href="@@urlroot/content/create" class="btn btn-info">Add new content</a>
    </div>
</div>
@endsection