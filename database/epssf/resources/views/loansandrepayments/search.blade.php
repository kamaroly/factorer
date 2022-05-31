<label>{{ $label }}</label>
  <div class="input-group">
    @if(is_null($cautionneur))
      <input type="text" class="form-control" placeholder="Search" name="{!! $fieldname !!}" id="js-{{ $fieldname }}">
      <div class="input-group-btn search-cautionneur" id="js-search-cautionneur">
          <button class="btn btn-default" type="submit">
            <i class="glyphicon glyphicon-plus"></i>
          </button>
      </div>
    @else
<div class="autocomplete-wrapper block">
<p class="container item-content" >
    <img src="{{route('files.get', $cautionneur->photo)}}" align="left">
        <span class="text-xl font-semibold block">
          {{ $cautionneur->adhersion_id}}
       &nbsp; <a href="{{ route('loan.remove.cautionneur',$fieldname) }}" class="btn btn-warning">
          <i class="fa fa-remove"></i>
        </a>
        </span>
        <span class="names text-green-900 text-lg font-semibold">
          {{ ucfirst(strtolower($cautionneur->first_name)) }} {{ ucfirst(strtolower($cautionneur->last_name))}}
          <br/>
          {{ trans('member.nid') }} : {{ $cautionneur->member_nid }}
        </span>

        <input type="hidden" name="{!! $fieldname !!}" value="{{ $cautionneur->adhersion_id}}">
<p>
</div>
    @endif
</div>