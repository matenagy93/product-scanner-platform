<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Cash Register - Product Scanner</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="{{ URL::asset('vendor/bootstrap-4.6.1-dist/css/bootstrap.min.css') }}">
		<script src="{{ URL::asset('vendor/jquery-3.6.0.min.js') }}"></script>
		<script src="{{ URL::asset('vendor/popper.min.js') }}"></script>
		<script src="{{ URL::asset('vendor/bootstrap-4.6.1-dist/js/bootstrap.bundle.min.js') }}"></script>
	</head>
	<body>
		<div class="jumbotron text-center pt-4 pb-4 mb-0">
			<h1 class="m-0 text-uppercase font-weight-bold">Cash Register - Product Scanner</h1> 
		</div>
		<div class="container pt-4">
			@if(isset($_GET['error']))
				@if($_GET['error'] == 'start-failure')
					<div class="alert alert-danger">
						<strong>Error!</strong> We wasn't able to start the scanning! Please try again!
					</div>
				@elseif($_GET['error'] == 'end-failure')
					<div class="alert alert-danger">
						<strong>Error!</strong> We wasn't able to end the scanning! Please try again!
					</div>
				@elseif($_GET['error'] == 'scan-details')
					<div class="alert alert-danger">
						<strong>Error!</strong> The given scanning details page isn't unavailable!
					</div>
				@else	
					<div class="alert alert-danger">
						<strong>Unexpected error!</strong> Please try again!
					</div>
				@endif
			@endif
			@yield('content')
		</div>
	</body>
</html>
