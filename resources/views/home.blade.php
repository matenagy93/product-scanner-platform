@extends('frame')

@section('content')
	<h3>Start new product scanning</h3>
	<p>Click on the button below to begin the scanning!</p>
	<form method="post" action="{{ route('scan-start') }}">
		@csrf
		<button type="submit" class="btn btn-lg btn-primary">START</button>
	</form>
@endsection