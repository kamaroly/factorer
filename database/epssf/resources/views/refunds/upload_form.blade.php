<div class="row header-container" >
        @if ($refundHasDifference == false)
        {!! Form::open(array('route'=>'refunds.batch','method'=>'POST', 'files'=>true)) !!}
            <h4 class="text-green-700">{{ trans('refund.bulk_refund') }}</h4>
            {!! Form::file('file',['class'=>'btn col-md-3']) !!} 
            <button class="btn btn-success col-md-1">
                <i class="fa fa-upload"></i> {{ trans('general.upload') }}
            </button>
            <a href="{{ route('contributions.sample.csv') }}" class="btn btn-default">
                <i class="fa fa-download"></i> {{ trans('general.download_sample') }}
            </a>
        @endif

        {{-- Only show refund nature if we don't have it selected --}}
        <select class="btn bg-indigo-100" 
                id="refund-loan-type" 
                name="refund-loan-type"> 
            <option value="NON_EMERGENCY_LOAN" {{ $refundLoanType === 'NON_EMERGENCY_LOAN' ? 'SELECTED' : '' }}>
                {{ trans('navigations.other_refund') }}
            </option>
            <option value="EMERGENCY_LOAN" {{ $refundLoanType === 'EMERGENCY_LOAN' ? 'SELECTED' : '' }}>
                {{ trans('navigations.emergency_refund') }}
            </option>
        </select>

        @if (isset($refundNature))
            <div class="btn btn-warning"><strong>{{ $refundNature }}</strong></div>
        @endif
        
        @if ($membersWithoutEmergency->isEmpty() == FALSE && $refundLoanType = 'EMERGENCY_LOAN')
            <a href="{{ route('refunds.export') }}?export-member-without-emergency-loans=yes" class="btn btn-warning" target="_blank">
            <i class="fa fa-download"></i>
                {{ trans('refund.export_members_without_emergency') }} ({{ $membersWithoutEmergency->count() }})        
            </a>
        @endif

        {{-- Members with differences --}}
        @if ($refundHasDifference == true)
			<a href="{{ route('refunds.index') }}?remove-member-with-differences=yes" class="btn btn-danger">
                <i class="fa fa-remove"></i> {{ trans('refunds.remove_with_difference') }}         
            </a>
            <a href="{{ route('refunds.export') }}?export-member-with-differences=yes" class="btn btn-primary" target="_blank">
            <i class="fa fa-download"></i>
                {{ trans('refunds.export_differences') }}         
            </a>
        @endif

      {{--  Errored uploads--}}
       @if ($uploadHasErrors == true)
            <a href="{{ route('refunds.export') }}?export-member-with-errors=yes" class="btn btn-inverse" target="_blank">
            <i class="fa fa-download"></i>
            {{ trans('refunds.export_uploaded_with_errors') }}         
            </a>
        @endif
    {!! Form::close() !!}

</div>