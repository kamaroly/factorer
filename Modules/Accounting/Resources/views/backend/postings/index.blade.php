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
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Transaction Id</th>
                <th>Amount</th>
                <th>Wording</th>
                <th>Created at</th>
            </tr>

            @foreach ($postings as $posting)
                <tr>
                    <td>
                        <a href="/admin/accounting/posting/{{ $posting->transactionid }}">{{ $posting->transactionid }}</a>
                    </td>
                    <td>{{ $posting->amount }}</td>
                    <td>{{ $posting->wording }}</td>
                    <td>{{ $posting->created_at }}</td>
                </tr>
            @endforeach
        </table>

    </div>
</div>

@endsection
