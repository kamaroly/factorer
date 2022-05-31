<div class="col-md-12 bg-gray-200 p-2"> 
        {!! Form::open(array('route'=>'reconciliations.bank.reconcile','method'=>'POST', 'files'=>true,'class'=>'row')) !!}
            
            {{-- Upload cvs --}}
            <div class="col-md-2">  
                <label>{{ trans('reconciliation.upload_bank_file') }}</label>
                <input type="file" name="bank-csv-file" class="form-control col-md-12">
            </div>
            <div class="col-md-2">  
                <label>&nbsp;</label>
               <button class="btn btn-success col-md-5 form-control">
                  <i class="fa fa-upload"></i> {{ trans('general.upload') }}
                </button>
            </div>          
            {!! Form::close() !!}
      </div>