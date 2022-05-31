@if(!empty($filename))
	<img src="{{route('files.get', $filename)}}" alt="ALT NAME" class="img-responsive" />
@endif