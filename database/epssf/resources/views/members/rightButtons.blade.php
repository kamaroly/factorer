{{-- NEW BUTTONS --}}
<a class="popdown col-md-12 bg-gray-700 text-gray-100 text-center px-4 py-2 m hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1" href="{!!route('members.attornies',['memberId' => $member->id]) !!}">
  {{ trans('member.member_attornes') }}
</a>

@if (!Sentry::getUser()->isNormalMember())
<a class="popdown col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1" href="{{ route('attornies.create') }}?member={!! isset($member->id)?$member->id:null !!}">

 {{ trans('member.add_attornies') }}
</a>
@endif

@if (!Sentry::getUser()->isNormalMember())
<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('reports.members.contracts.saving',['memberId' => $member->adhersion_id]) }}"
	target="_blank"
>
	{{ trans('member.contract_saving') }}
</a>
@endif
@if(count($member->loans) > 0 && !Sentry::getUser()->isNormalMember())
<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('reports.members.contracts.loan',['memberId' => $member->adhersion_id]) }}"
	target="_blank"
>
{{ trans('member.contract_loan') }}
</a>
@endif
<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('members.loanrecords',['memberId' => $member->adhersion_id]) }}"
	target="_blank"
>
{{ trans('member.loan_records') }}
</a>


@if(($member->hasActiveLoan() || $member->hasActiveEmergencyLoan) && !Sentry::getUser()->isNormalMember())
<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-11"
	href="{{ route('members.refund',['memberId' => $member->id]) }}"
>
	{{ trans('member.refund_loan') }}
</a>

@endif
<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('members.contributions',['memberId' => $member->adhersion_id]) }}"
	target="_blank"
>
{{ trans('member.contributions') }}
</a>

@if (!Sentry::getUser()->isNormalMember())
	<a 	class="col-xs-12 col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
		href="{{ route('members.transacts',['memberId' => $member->id]) }}"
	>
	{{ trans('member.do_a_transaction') }}
	</a>
@endif

<a 	class="popdown col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('members.cautions.actives',['memberId' => $member->id]) }}"
>
{{ trans('member.view_current_cautionneurs') }}
</a>
<a 	class="popdown col-md-12 text-gray-100 text-center bg-gray-700 hover:bg-blue-700 hover:text-blue-100 rounded-lg px-4 py-2 m-1"
	href="{{ route('members.cautioned.actives',['memberId' => $member->id]) }}"
>
{{ trans('member.view_member_cautioned_by_me') }}
</a>

