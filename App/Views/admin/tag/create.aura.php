(@extends::layouts.base)

@section('extend')
<div class="container">
	<h1 class="h1">Create a new tag</h1>
	<form method="post" action="@@urlroot/tag/insert">
		@@form::text(tag.type)
		@@form::text(tag.tag)
		@@form::text(tag.value)
		@@form::submit()
	</form>
</div>
@endsection