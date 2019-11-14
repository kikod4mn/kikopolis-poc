(@extends::layouts.base)

@section('extend')
<div class="container mt-4">
	<div class="row mb-3 mt-3">
		<div class="col-12 deep-orange darken-4 p-5">
			<h4 class="lead white-text text-center">Add new page content</h4>
			<p class="white-text">This functionality is not functional. Do not attempt to add. Will return a DB error.</p>
		</div>
	</div>
    <form method="post" action="@@urlroot/content/insert">
		<input type="hidden" name="method" value="POST">
		<?php echo \Kikopolis\App\Utility\Form::csrf(); ?>
		<div class="md-form">
			<select class="browser-default custom-select custom-select-lg mb-3" name="page_route">
				<option disabled selected>Choose a page to edit</option>
				<option value="home.index">Home</option>
				<option value="home.about">About</option>
				<option value="home.contact">Contact</option>
				<option value="home.faq">FAQ</option>
				<option value="blog">Blog</option>
			</select>
		</div>
		<div class="md-form">
			<textarea id="content" name="content" class="form-control md-textarea my-3 py-2 px-3 text-dark" rows="15"></textarea>
			<label for="content">Enter the content in markdown format</label>
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