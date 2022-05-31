<div id="search">
	<div id="search-wrapper">
	  <input placeholder="" type="text" id="search-input" />
	  <i id="cross" class="icon"></i>
	</div>
</div>

{{-- THIS STORES THE ID OF THE SELECTED MEMBER --}}
{!! Form::hidden('member_id', null, ['class'=>'member_id']) !!}