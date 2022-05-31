 <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9" >
	 @include('members.form')
</div>
<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
	<div class="row">
		 @include('members.rightButtons')
	</div>
	<div class="row">
		 @include('members.summary')
	</div>
</div>

   <?php $emergencyLoan = $member->emergency_with_relicat->first(); ?>

   @if ( empty($emergencyLoan) === FALSE )
		<div class="row">
			 @include('members.emergency')
		</div>
	@endif
<script src="{{Url()}}/assets/dist/js/datepickr.js" type="text/javascript"></script>
<script src="{{Url()}}/assets/dist/js/date.js" type="text/javascript"></script>