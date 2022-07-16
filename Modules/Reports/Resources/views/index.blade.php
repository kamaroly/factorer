@extends('backend.layouts.app')


@section('content_title')
	@if (request()->route()->getName() === 'reconciliations.accounts.index')
		{{ trans('reconciliations.bank_account_reconciliations') }}
	@else
	  {{ trans('navigations.reports') }}
	@endif

@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h1>Reports</h1>
    </div>

    <div class="card-body">

        <div class="container">
            <div class="row">
              <div class="col">
                <ul class="list-group">
                    <li class="list-group-item warning active" aria-current="true">Accounting Reports</li>
                    <li class="list-group-item">
                        <a
                            href="{{ route('backend.reports.filter', ['report-route'=> 'admin.reports.grand.livre']) }}"
                            target="_blank"
                        >
                            Grand livre (ledge)
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a
                            href="{{ route('backend.reports.filter', ['report-route'=> 'admin.reports.journal']) }}"
                            target="_blank"
                        >
                        Journal
                        </a>
                    </li>
                    <li class="list-group-item">Report 3</li>
                    <li class="list-group-item">Report 4</li>
                  </ul>
              </div>
              <div class="col">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">Inventory Reports</li>
                    <li class="list-group-item">
                        <a
                            href="{{ route('backend.reports.filter', ['report-route'=> 'admin.reports.inventory']) }}"
                            target="_blank"
                        >
                            Rapport de stock matiere premiere
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a
                            href="{{ route('backend.reports.filter', ['report-route'=> 'backend.reports.product-inventory']) }}"
                            target="_blank"
                        >
                            Rapport de stock produit finie(entre et sortie)
                        </a>
                    </li>
                    <li class="list-group-item">Report 3</li>
                    <li class="list-group-item">Report 4</li>
                  </ul>
              </div>
              <div class="col">
                <ul class="list-group">
                    <li class="list-group-item active" aria-current="true">Sales Reports</li>
                    <li class="list-group-item">Report 1</li>
                    <li class="list-group-item">Report 2</li>
                    <li class="list-group-item">Report 3</li>
                    <li class="list-group-item">Report 4</li>
                  </ul>
              </div>
            </div>
          </div>
    </div>
</div>
@endsection
