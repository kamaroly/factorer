<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 bs-sidebar">
<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.contracts_reports') }}</h4>  
<ol class="nav nav-list">
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contracts/saving&member_search=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.savings_contract') }}
      </a>
    </li>
   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contracts/loan&member_search=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.ordinary_loan_contract') }}
      </a>
   </li>
   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contracts/loan&member_search=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.special_loan_contract') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contracts/loan&member_search=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.loan_regolarisation_contract') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contracts/loan&member_search=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.social_loan_contract') }}
      </a>
  </li>
</ol>

<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.the_disbursed_parts') }}</h4>
<ol class="nav nav-list">
  <li>
        <a href="{{ route('reports.filter') }}/?reporturl=reports/piece/disbursed/accounting&show_transaction_input=true&show_exports=true"  
       class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.accounting_piece_disbursed') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/piece/disbursed/saving&show_transaction_input=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.saving_piece_disbursed') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/piece/disbursed/loan&show_transaction_input=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.loan_piece_disbursed') }}
      </a>
  </li>
</ol>   
</div> 
<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.files_reports') }}</h4>   
<ol class="nav nav-list">
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contributions&member_search=true&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.savings_file') }}
      </a>
  </li>
  <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/members/loanrecords&member_search=true&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.loans_file') }}
      </a>
  </li>
    <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/loans&show_loan_status=true&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.loan_by_status') }}
      </a>
  </li>
  <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/refunds/monthly&show_exports=true&show_institution=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.monthly_refund_file') }}
      </a>
  </li>


    <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/refunds/monthlyContribution&show_exports=true&show_institution=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.monthly_contribution') }}
      </a>
  </li>



  <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/members/history&show_exports=true&show_dates=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.monthly_fee_inventory') }}
      </a>
  </li> 
  <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/members/octroye&show_exports=true&show_dates=true&show_institution=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.octorey_report') }}
      </a>
  </li>
</ol>
</div> 

<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.the_accountants_reports') }}</h4>  
<ol class="nav nav-list">
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/accounting/ledger&show_dates=true&show_exports=true&show_accounts=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.ledger') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/accounting/bilan&show_months=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.bilan') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/accounting/journal&show_dates=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.journal') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.accounting.accounts') }}" onclick="OpenInNewTab(this.href)">
        <i class="icon-chevron-right"></i> {{ trans('report.account_list') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/accounting/trial-balance&show_months=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.trial_balance') }}
      </a>
  </li>

   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/accounting/cash-flow-statement&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('reports.cash-flow-statement') }}
      </a>
  </li>
</ol>

<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.members') }}</h4>  
<ol class="nav nav-list">
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/hightloanthancontribution&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.higher_loan_than_contribution') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/contribution-count&show_institution=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.contribution_count') }}
      </a>
  </li>
  <!--<li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/members/all-details&show_institution=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.member_all_details') }}
      </a>
  </li>-->
</ol>    
</div>

<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.the_management_reports') }}</h4>   
<ol class="nav nav-list">
 <li>
      <a href="{{ route('reports.filter')}}/?reporturl=reports/savings/allmember&show_months=true&show_exports=true"  class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.savings_contribution_member2') }}
      </a>
  </li>
 <li>
      <a href="{{route('reports.filter')}}/?reporturl=reports/savings/level&show_exports=true&show_member_status=true&show_months=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.savings_contribution') }}
      </a>
  </li>
  <li>
  <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/leftmember&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.left_members') }}
      </a>
    </li>

   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/loans/balance&show_months=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.member_loan_balance') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/contributions/notcontribuing&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.contribution_irregolarities') }}
      </a>
  </li>
   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/refunds/irreguralities&show_exports=true&show_months=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.refund_irregolarities') }}
      </a>
  </li>
  <!-- <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/cautions/cautioned_me&member_search=true&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.members_who_cautionned_me') }}
      </a>
  </li>
   <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/cautions/cautioned_by_me&member_search=true&show_dates=true&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.members_cautionned_by_me') }}
      </a>
  </li>-->
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/refunds/refundlatestpayment&show_dates=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.refundlatestpayment') }}
      </a>
  </li>
  <li>
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/contribution-changes&show_dates=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.contribution_changes') }}
      </a>
  </li>
  <li>
      <a  href="{{ route('reports.filter') }}/?reporturl=reports/refunds/modified&show_exports=true&show_dates=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.modified.refunds') }}
      </a>
  </li>
</ol>
</div>
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<h4 class="bg-gray-800 text-gray-100 font-light text-center text-xl p-2 m-1 uppercase">{{ trans('report.others') }}</h4>   
<ol class="nav nav-list">
 <li class="bg-red-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/refunds/at-risk&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.loan_at_risk') }}
      </a>
  </li>
 <li class="bg-green-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/alldetails&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.all_details') }}
      </a>
  </li>

<!--<li class="bg-green-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/allmember&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.savings_contribution_member2') }}
      </a>
  </li> -->

 
 <li class="bg-yellow-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/year_interest&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.Individually_year_interest') }}
      </a>
  </li>
  <li class="bg-orange-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/newmember&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.new_member') }}
      </a>
  </li>
  <li class="bg-yellow-200">
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/guarantors&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.guarantors') }}
      </a>
  </li>
  <li class="bg-blue-200" >
      <a href="{{ route('reports.filter') }}/?reporturl=reports/savings/institutionList&show_exports=true" class="popdown">
        <i class="icon-chevron-right"></i> {{ trans('report.institution_list') }}
      </a>
  </li>
   
</ol>
</div>
