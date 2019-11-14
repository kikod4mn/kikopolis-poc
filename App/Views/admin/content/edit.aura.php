(@extends::layouts.base)

@section('extend')
<div class="container mt-4">
	<div class="row mb-3 mt-3">
		<div class="col-12 deep-orange darken-4 p-5">
			<h4 class="lead white-text text-center">Add new page content</h4>
			<p class="white-text">Read the Markdown tutorial below for more info. Do not attempt to edit if you do not know EXACTLY what you are doing.</p>
			<p class="white-text">Editing can ruin your entire page.</p>
		</div>
	</div>
	<form method="post" action="@@urlroot/content/{{page.id}}/update">
		<input type="hidden" name="method" value="POST">
		<?php echo \Kikopolis\App\Utility\Form::csrf(); ?>
		<input type="hidden" name="page_route" value="{{ page.page_route }}">
		<input type="hidden" name="id" value="{{ page.id }}">
		<div class="md-form">
			<input id="page_route" disabled type="text" class="form-control my-3 py-2 px-3 text-dark">
			<label for="page_route">Currently editing page - @to_text({{page.page_route}})</label>
		</div>
		<div class="md-form">
			<textarea id="content" name="content" class="form-control md-textarea my-3 py-2 px-3 text-dark" rows="15">{{page.content}}</textarea>
		</div>
		<div class="md-form text-center">
			<input type="submit" value="Submit" name="submit" class="btn btn-primary">
		</div>
	</form>
	<div class="row mb-3 mt-3">
		<div class="col-12 deep-orange darken-4 p-5">
			<h4 class="lead white-text text-center">Markdown tutorial</h4>
			<p class="white-text">Tutorial goes here</p>
		</div>
	</div>
</div>
@endsection