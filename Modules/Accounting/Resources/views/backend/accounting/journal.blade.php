<div class="row">
{{-- 	<div class="col-xs-3 col-md-3">
<div class="form-group">
    <label for="wording">
    	 {{ trans('accounting.journal') }}
     </label>
   {!! Form::select('journal', $journals,null, ['class'=>'form-control account'])!!}
</div>
</div> --}}
<div class="col-xs-3 col-md-5">
<div class="form-group">
    <label for="wording">
    	 {{ trans('accounting.wording') }}
     </label>
   	 	 {!! Form::input('text', 'wording', null, ['class'=>'form-control','placeholder'=>trans('general.write_your_wording')]) !!}
</div>
</div>
<div class="col-xs-3 col-md-4">
    <div class="form-group">
        <label for="cheque_number">
        	 {{ trans('accounting.cheque_number') }}
         </label>
       	 {!! Form::input('text', 'cheque_number', null, ['class'=>'form-control','placeholder'=>trans('general.cheque_number')]) !!}
    </div>
</div>
</div>
