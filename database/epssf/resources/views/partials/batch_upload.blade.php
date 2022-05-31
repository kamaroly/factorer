<div class="row bg-gray rounded-b-lg border-t border-l border-r border-gray-400 p-4">

        @if ($contributionHasDifference !== true)
            {!! Form::open(array('route' => $route,'method'=>'POST', 'files'=>true)) !!}
            {!! Form::file('file',['class'=>'btn col-md-3','id'=>'contribution-file']) !!} 
            {!! Form::submit('Upload', array('class'=>'btn btn-success col-md-3')) !!}

            <a href="{{ route('contributions.sample.csv') }}" class="btn btn-info col-md-3">
                <i class="fa fa-file-excel-o"></i> {{ ucfirst(trans('general.download_sample')) }}
            </a>
        @endif

        @if ($contributionHasDifference == true)
			<a href="{{ route('contributions.index') }}?remove-member-with-differences=yes" class="btn btn-warning">
            {{ trans('contribution.remove_with_difference') }}         
            </a>
            <a href="{{ route('contributions.export') }}?export-member-with-differences=yes" class="btn btn-primary" target="_blank">
            <i class="fa fa-file-excel-o"></i>
            {{ trans('contribution.export_differences') }}         
            </a>
        @endif

       @if ($uploadsWithErrors == true)
            <a href="{{ route('contributions.export') }}?export-member-with-errors=yes" class="btn btn-inverse" target="_blank">
            <i class="fa fa-file-excel-o"></i>
            {{ trans('contribution.export_uploaded_with_errors') }}         
            </a>
        @endif
    {!! Form::close() !!}
</div>