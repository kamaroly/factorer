  <div class="row">
	<div  class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
		<a href="{{ route('corrections.contributions.complete') }}"
		   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-success">
		   {{ trans('contribution.complete_transaction') }}
		</a>
	</div>
	
	<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
		<a href="{{ route('corrections.contributions.cancel') }}"
		   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-warning">
		   {{ trans('contribution.cancel_transaction') }}
		</a>
	</div>
	</div>