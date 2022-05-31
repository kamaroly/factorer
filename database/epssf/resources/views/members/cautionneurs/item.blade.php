<?php $member = $caution->cauttionneur;?>
@if (strtolower($type) == 'by_me')
	<?php $member = $caution->member;?>
@endif
<tr>
	<td>{!! $member->adhersion_id!!}</td>
	<td>
		<img class="direct-chat-img" src="{{route('files.get', $member->photo)}}" align="left">
		<strong>{!! $member->names!!} </strong> <br/>
		<small>{!! (is_null($member->member_nid) || $member->member_nid=='NULL') ?' ':$member->member_nid !!}</small>
	</td>
	<td>{!! $member->institution_name !!}</td>
	{{-- <td>{!! $member->service !!}</td> --}}
	<?php $amount = (int) $caution->amount; ?>
	<td>{!! number_format(abs($amount)) !!}</td>
	<?php $refunded_amount = (int) $caution->refunded_amount  ?>
	<td>{!! number_format(abs($refunded_amount))!!}</td>
	<td>{!! number_format($amount - $refunded_amount)  !!}</td>
</tr>