<div class="row">
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
	<button 
		role="submit" 
		id="js-complete-button" 
		onclick="return confirm('{!! trans('general.are_you_sure_you_want_to_this_this_action_cannot_be_reverted') !!}')"
	   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-success">
	  {{ trans('general.saving') }}
	</button>
</div>
<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
	<a href="{{ route($cancelRoute) }}"
	   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-danger">
	  {{ trans('general.cancel') }}
	</a>
</div>

</div>
