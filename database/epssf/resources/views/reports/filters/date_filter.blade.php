@extends('reports.layouts.popup')
@section('content')

<style type="text/css">
	select {
    display: block;
    width: 15%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    margin-right: 3px;
    float: left;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 0px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
</style>

<input type="hidden" class="report-name" value="{!! $reportUrl !!}">
@if ($filterOptions->show_member_status == true)

{{-- Filter per member status --}}
<div class="row">
	<div class="col-md-4">
		<b>{{ trans('report.select_member_status') }}</b>
	</div>
	<div class="col-md-6">
		<select name="member_status" class="col-md-12 member-status">
		<option value="actif">{{ trans('member.active') }}</option>
		<option value="inactif">{{ trans('member.inactive') }}</option>
	</select>	
	</div>
</div>
		
	
@endif

<div class="row">
{{-- only show this search if it's a member reports --}}
@if ($filterOptions->member_search == true)
	@include('members.search')
	{!!Form::hidden('adhersion_id',null,['class'=>'adhersion_id']) !!}
	<span class="label label-warning">{{ trans('general.please_start_by_selecting_others_input_before_selecting_a_member') }}</span>
@endif
<br/>

{{-- only show this search if it's we need to select months --}}
@if ($filterOptions->show_months_number == true)
<b>{{ trans('reports.select_months') }}</b>	
	<br/>
	  {!! Form::select('months-number', [1=>1, 3=>3, 6=>6], null, ['class'=>'form-control','id'=>'months-number']) !!}
	<br/>
@endif
<br/>

{{-- Only show this if the report require institition selection  --}}
@if ($filterOptions->show_institution == true)
<b>{{ trans('reports.select_institution') }}</b>	
	<br/>
	  {!! Form::select('institution', $institutions, null, ['class'=>'form-control','id'=>'institition']) !!}
	<br/>
@endif
{{-- Only show this if the report require institition selection  --}}
@if ($filterOptions->show_transaction_type == true)
<b>{{ trans('reports.transaction_type') }}</b>	
	<br/>
	  {!! Form::select('transaction_type', ['saving'=>'saving','withdrawal'=>'withdrawal'], null, ['class'=>'form-control','id'=>'transaction_type']) !!}
	<br/>
@endif
{{-- Only show this if the report require institition selection  --}}
@if ($filterOptions->show_transaction_input == true)
<label>{{ trans('reports.transaction_id') }}</label>	
	<br/>
	 {!! Form::text('transaction_id', null,  ['class'=>'form-control','id'=>'transaction_id']) !!}
	<br/>
@endif
{{-- Only show this if the report require loan status selection  --}}
@if ($filterOptions->show_loan_status == true)
<div>
	<label>{!! trans('reports.select_loan_status') !!} :</label>
    {!! Form::select('loan_status', $loanStatuses, null , ['class' => 'loan_status']) !!}
 </div>
@endif	

{{-- Only show this if the report require accounts selection  --}}
@if ($filterOptions->show_accounts == true)
	<b>{{ trans('reports.select_account') }}</b>
	  {!! Form::select('account', $accounts,isset($accountId)?$accountId :null, ['class'=>'form-control','id'=>'account'])!!}    
@endif

{{-- Only show this if the report require to show loan types selection  --}}
@if ($filterOptions->show_loan_types == true)
  <b>{{ trans('reports.loan_type') }}</b>
   {!! Form::select('loan_type',$loanTypes,null,['class'=>'form-control','id'=>'loan_type'])!!}
@endif

{{-- Only show this if the report require to show dates or months selection  --}}
@if ($filterOptions->show_dates == true || $filterOptions->show_months == true)
<b>{{ trans('reports.report_range') }}</b>
<br/>
{{-- Only show simplified date when we date is selected --}}
@if ($filterOptions->show_dates == true)
<div id="report_date_range_simple" style="display: block;">
	<input type="radio" name="report_type" id="simple_radio" value="simple" checked="checked" class="pull-left" style="padding-right: 20px">
		{!! Form::select('report_date_range_simple', get_simple_date_ranges(),null, ['id'=>'report_date_range_simple',]) !!}
</div>
<br>
@endif


{{-- Only consider the rest if show dates is enabled --}}
@if ($filterOptions->show_dates == true)
<br/>
    <label>Complex date range </label> 
    <br> 
	<input type="radio" name="report_type" id="complex_radio" value="complex" style="float: left;display: block;">
    {!! Form::select('start_day', get_days(), date('d') , ['class' => 'start_day']) !!}
	{!! Form::select('start_month', get_months(), date('m'), ['class' => 'start_month']) !!}
	{!! Form::select('start_year', get_years(), date('y'), ['class' => 'start_year']) !!}

<strong style="display: block;float: left;padding: 10px;font-size: 18px;">-</strong>

@endif
    {!! Form::select('end_day', get_days(), date('d') , ['class' => 'end_day']) !!}
	{!! Form::select('end_month', get_months(), date('m'), ['class' => 'end_month']) !!}
	{!! Form::select('end_year', get_years(), date('y'), ['class' => 'end_year']) !!}
<br/>
<br/>
@endif

{{-- Only show this if the report require to show export options selection  --}}
@if ($filterOptions->show_exports == true)
<div class="pull-left" style="display: block;">
	<b>{!! trans('reports.export_excel') !!} :</b> 
	<input type="radio" name="export_excel" id="export_excel_yes" value="1"> {!! trans('general.yes') !!}
	<input type="radio" name="export_excel" id="export_excel_no" value="0" checked="checked"> {!! trans('general.no') !!}
	</div>
<br/>
@endif

<button class="btn btn-success btn-lg btn-block generate_report" type="submit"
 id="submit">
	{{ trans('report.submit') }}
</button>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$(".generate_report").click(function(event){
		event.preventDefault();
		var baseUrl = $(".report-name").val();
		var url = null;
		var export_excel = 0;
		var daterange    = null;
		var adhersion_id = 'none';
		if ($('#export_excel_yes').is(':checked'))
		{
			export_excel = 1;
		}

		// If this is reports/savings/level
		// For ONLY CLOSING REPORT/ SAVINGS LEVELS
		@if (trim(request()->get('reporturl')) == 'reports/savings/level')
			url = '/'+baseUrl+'/'+ $(".member-status").val()+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif

			// If this is reports/savings/allmember
		// For ONLY CLOSING REPORT/ SAVINGS for all members
		@if (trim(request()->get('reporturl')) == 'reports/savings/allmember')
			url = '/'+baseUrl+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif

		//cash flow  statement report url
		//@if  (trim(request()->get('reporturl')) == 'reports/accounting/cash-flow-statement')
	
		 //     url = '/'+baseUrl+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
		//	console.log(url);
		//	OpenInNewTab(url);
		//	return;
		//@endif    

        //bilan report url

		@if  (trim(request()->get('reporturl')) == 'reports/accounting/bilan')
		      url = '/'+baseUrl+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif    
        
        //trial balance

		@if  (trim(request()->get('reporturl')) == 'reports/accounting/trial-balance')
		      url = '/'+baseUrl+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif  
    
        @if (trim(request()->get('reporturl')) == 'reports/loans/balance')
			url = '/'+baseUrl+'/'+ $(".member-status").val()+'/'+$(".end_year").val()+'-'+$(".end_month").val()+'-'+ $(".end_day").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif
 		@if (trim(request()->get('reporturl')) == '/reports/refunds/at-risk')
			url = '/'+baseUrl+'/'+ $("#months-number").val()+'/'+ export_excel;
			console.log(url);
			OpenInNewTab(url);
			return;
		@endif
		
		// Consider end date for reports that needs
		// months only 
		@if (trim(request()->get('reporturl')) == 'reports/refunds/irreguralities')
			url = '/'+baseUrl+'/'+ $(".end_year").val()+'-'+$(".end_month").val()+'/'+ export_excel;
			OpenInNewTab(url);
			return;
		@endif

        //Report fin dette
        @if (trim(request()->get('reporturl')) == 'reports/members/octroye')
            var instititionType = $('#institition').val();
			var start_date = $(".start_year").val()+'-'+$(".start_month").val()+'-'+$('.start_day').val();
			var end_date = $(".end_year").val()+'-'+$(".end_month").val()+'-'+$('.end_day').val();

			daterange = start_date + '/'+ end_date;
			url = '/'+baseUrl+'/'+ daterange+'/'+instititionType+'/'+export_excel;

			OpenInNewTab(url);
			return;
		@endif



		if ($("#simple_radio").is(':checked'))
		{
			 daterange = $("#report_date_range_simple option:selected").val();
			 url= '/'+baseUrl+'/'+daterange +'/'+export_excel;
			 /** if we are going to look for loan status then change the url format */
			if($('.loan_status').length !== 0 ){
				var loan_status = $('.loan_status').val();
				url = '/'+baseUrl+'/'+daterange +'/'+loan_status+'/'+ export_excel;
			}
		}
		else
		{
			var start_date = $(".start_year").val()+'-'+$(".start_month").val()+'-'+$('.start_day').val();
			var end_date = $(".end_year").val()+'-'+$(".end_month").val()+'-'+$('.end_day').val();

			daterange = start_date + '/'+ end_date;
			url = '/'+baseUrl+'/'+ daterange+'/'+ export_excel;

			/** if we are going to look for loan status then change the url format */
			if($('.loan_status').length !== 0 ){
				var loan_status = $('.loan_status').val();
				url = '/'+baseUrl+'/'+daterange +'/'+loan_status+'/'+ export_excel;
			}
		}
        
        
        if(typeof $('#account').val() !=='undefined')
        {
        	var account = $('#account').val();
        	url = '/'+baseUrl+'/'+daterange +'/'+account+'/'+ export_excel;
		}

        if(typeof $('#loan_type').val() !=='undefined')
        {
        	var loan_type = $('#loan_type').val();
        	url = '/'+baseUrl+'/'+daterange +'/'+loan_type+'/'+ export_excel;
		}

	    if(typeof $('#account').val() !=='undefined' && typeof $('#loan_type').val() !=='undefined'){
		 	url = '/'+baseUrl+'/'+daterange +'/'+account+'/'+loan_type+'/'+ export_excel;
		 }

	   if(typeof $('#transaction_id').val() !=='undefined')
        {
        	var transaction_id = $('#transaction_id').val();
        	url = '/'+baseUrl+'/'+transaction_id+'/'+ export_excel;
        	OpenInNewTab(url);
        	return ;
		}

		/** Add additinal parameters for the members routes */
		if(baseUrl.indexOf('members') !== -1 || baseUrl.indexOf('cautions') !== -1 )
		{
			/** USER MUST SELECT A MEMBER FOR THIS REPORT */
			if($('.adhersion_id').val() == '')
			{ swal.setDefaults({ confirmButtonColor: '#d9534f' });
				swal({
			            title:"Please select a member for this report",
			            type :"error",
			            html :true
			          });
				return exit;
			}
			
		    adhersion_id = $('.adhersion_id').val();
			url = url +'/'+adhersion_id;
		}

		if(baseUrl.indexOf('contract') !== -1)
		{
			adhersion_id = $('.adhersion_id').val();
			url = baseUrl+'/'+adhersion_id+'/'+ export_excel;
			OpenInNewTab(url);
			return ;
		}

		if(typeof $('#complex_radio').val() =='undefined' && typeof $('#report_date_range_simple').val() =='undefined' && typeof $('#institition')!== 'undefined')
		{
			var instititionType = $('#institition').val();
			url = baseUrl+'/'+instititionType+'/'+ export_excel;
		}

		/** OPEN  THE REPORT */
		OpenInNewTab(url);

	});
	
	$(".start_month, .start_day, .start_year, .end_month, .end_day, .end_year").click(function(){
		$("#complex_radio").attr('checked', 'checked');
	});
	
	$(".report_date_range_simple").click(function(){
		$("#simple_radio").attr('checked', 'checked');
	});
	
});
</script>
@stop