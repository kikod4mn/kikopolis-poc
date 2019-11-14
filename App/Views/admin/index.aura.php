(@extends::layouts.base)

@section('extend')
<div class="container">
    <a href="@@urlroot/content/index" class="btn btn-lg btn-primary">Content management</a>
    <a href="@@urlroot/tag/index" class="btn btn-lg btn-primary">Tags management</a>
    <a href="@@urlroot/theme/index" class="btn btn-lg btn-primary">Theme management</a>
</div>
@endsection