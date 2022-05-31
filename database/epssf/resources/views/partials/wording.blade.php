  <label>{{ trans('general.libelle') }}</label>
  <textarea name="wording" class="form-control loan-input" 
  			placeholder="{{ trans('general.reason_for_this_transaction') }}">{{ isset($wording) ? $wording : null }}</textarea>

