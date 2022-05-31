 <table class="pure-table pure-table-bordered" style="width:40%;float:left">
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_number') }}</th>
	     	<td>{{ $member->adhersion_id }}</td>
	  	</tr>
	  	<tr>
	  	 	<th>{{ trans('member.names') }}</th>
	     	<td>{{ $member->first_name.'  '.$member->last_name }} </td>
	  	</tr>
	  	<tr>
	  	 	<th>{{ trans('member.institution') }}</th>
	     	<td>{{ $member->institution?$member->institution->name:null }}</td>
	  	</tr>
	  	<tr>
	  	 	<th>{{ trans('member.adhersion_date') }}</th>
	     	<td>{{ $member->created_at->format('Y-m-d') }}</td>
	  	</tr>  	
  </table>

   {{-- Ensure that this summary is only displayed on loans records report --}}
   @if ($member->hasActiveEmergencyLoan && request()->is('reports/members/loanrecords/*'))
    <?php $emergencyLoan = $member->activeEmergencyLoan; ?>
	
	@if ($emergencyLoan->emergency_balance > 0)
	  <table style="width:50%;margin-left: 10px" >
	  			{{-- EMERGENCY NUMBERS --}}
			<tr><th colspan="2">{!! ucfirst(trans('loans.emergency_loan_details')) !!}</th></tr>
		  	<tr style="background-color:#9c4221;color:#fff;">
		  	 	<th> {{ trans('loans.emergency_loan_balance') }} </th>
		     	<td>{!! number_format($balance = $emergencyLoan->emergency_balance) !!}</td>
		  	</tr>
		  	<tr style="background-color:#975a16;color:#fff;">
		  	 	<th>{{ trans('loans.emergency_loan_monthly_fees') }}</th>
		     	<td>{!! number_format($monthly_fees = $emergencyLoan->monthly_fees) !!}</td>
		  	</tr>
		  	<tr style="background-color:#744210;color:#fff;">
		  	 	<th>{{ trans('loans.emergency_loan_refund') }}</th>
		     	<td>{!! number_format($emergencyLoan->emergency_refund) !!}</td>
		  	</tr>
		  	<tr style="background-color:#3c366b;color:#fff;">
		  	 	<th>{{ trans('loans.emergency_loan_remaining_tranches') }}</th>
		     	<td>{!!$emergencyLoan->remaining_installments !!}</td>
		  	</tr>		
	  </table>
  	@endif
  @endif