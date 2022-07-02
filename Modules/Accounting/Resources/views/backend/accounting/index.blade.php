@extends('backend.layouts.app')


@section('content_title')
	@if (request()->route()->getName() === 'reconciliations.accounts.index')
		{{ trans('reconciliations.bank_account_reconciliations') }}
	@else
	  {{ trans('navigations.accounting') }}
	@endif

@stop

@section('content')

<div class="card">
    <div class="card-header">
        <a href="{{ route('backend.accounting.postings.index', ['id'=>1]) }}"
           class="btn btn-primary">
           View Previous Transactions
        </a>
    </div>
    <div class="card-body">

    {!! Form::open(['route'=>'backend.accounting.store']) !!}

        @include('accounting::backend.accounting.journal')

        @include('accounting::backend.accounting.form')

    {{ Form::close() }}

</div>
</div>
@endsection
