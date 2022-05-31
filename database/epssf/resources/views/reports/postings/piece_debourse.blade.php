<table class="pure-table pure-table-bordered" style="width:30%;float: left ">
  	 	<tr>
	  	 	<th> {!! $labels->top_left_upper !!}</th>
	     	<td> {!! $labels->top_left_upper_value !!}</td>
	  	</tr>
	  	<tr>
	  	 	<th> {!! $labels->top_left_under !!}</th>
	     	<td> {!! $labels->top_left_under_value !!} </td>
	  	</tr>
  </table>
  <table class="pure-table pure-table-bordered" style="width:30%;float: right ">
  	 	<tr>
	  	 	<th> {!! $labels->top_right_upper !!}</th>
	     	<td> {!! $labels->top_right_upper_value !!}</td>
	  	</tr>
	  	<tr>
	  	 	<th> {!! $labels->top_right_under !!}</th>
	     	<td> {!! $labels->top_right_under_value !!} </td>
	  	</tr>
  </table>
<table class="pure-table pure-table-bordered">
<caption> 
	<span class="text-2xl text-gray-800 font-semibold">{!! $labels->title !!}</span>
	<span class="block text-xl text-yellow-800 font-light underline">
		<span class="text-gray-800 font-semibold">{{ trans('member.adhersion_id') }}: </span>
		{{ $postings->first()->adhersion_id }}
	</span>
 </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('account.account_number') }}</th>
	     	<th>{{ trans('account.account_entitled') }}</th>
			<th>{{ trans('account.debit') }}</th>
			<th>{{ trans('account.credit') }}</th>
	  	</tr>
   	 </thead>
 <tbody>

   @each('reports.member.item_piece_debourse', $postings, 'posting', 'members.no-items')
	
	@if (count($postings) > 0)
   	<tr>
	  	 	<th colspan="2">{!!  $postings->first()->wording !!}</th>
			<th>{!! number_format(abs($postings->where('transaction_type','debit')->sum('amount'))) !!}</th>
			<th>{!! number_format(abs($postings->where('transaction_type','credit')->sum('amount')))!!}</th>
	  	</tr>
	 @endif
 </tbody>
</table>
<br/>

@if (request()->segment(4) !== 'accounting')
	<table class="pure-table pure-table-bordered" style="width:25%">
	  	 	<tr>
		  	 	<td>
		  	 		<strong>{{ trans('report.beneficiare') }}</strong> <br>
		  
				
	                {!! $labels->top_right_upper_value !!}
		
				</td>
		  	</tr>

		  	<tr style="height: 50px;">
		  		<td><strong>Signature:</strong></td>,<br>
		  		
		  	</tr> <br/>
			
	 </table>
@endif

<br>

<table class="pure-table pure-table-bordered">
  	 	<tr>
	  	 	<td>
	  	 		<strong>{{ trans('report.initiated') }}</strong> <br/>
	  	 		             

                <?php 
                  $user_id=$postings->first()->user_id;
               
               $userone = $User->getmemberinitiator($user_id);
                		?>
              
                  @foreach( $userone as $user_data)
                 	
                  {!!$user_data->first_name !!} {!!$user_data->last_name !!}
                 	
                 	 @endforeach
             <!--<td><strong>{{ trans('report.verified') }}</strong> <br/>
				
				</td>------>
         

			
			
				<td><strong>{{ trans('report.authorized') }}</strong> <br/>
				{!! (new Ceb\Models\Setting)->get('general.tresorien') !!}
				</td>

				<td  colspan="4"  style="vertical-align:top"><strong>{{ trans('report.approved') }}</strong> <br/>
				{!! (new Ceb\Models\Setting)->get('general.president') !!}
				</td>
			
	  	</tr>

	  	<tr style="height: 70px;">
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		
	  		   
                
	  	</tr>
	  	 </table>
	  	 <br/>

           
	  	</tr>
 </table>



 <br/>