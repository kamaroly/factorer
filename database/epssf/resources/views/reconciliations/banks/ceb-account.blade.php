 <div class=""> 
        {!! Form::open(array('route'=>'reconciliations.bank.reconcile','method'=>'POST', 'files'=>true,'class'=>'row')) !!}
           {{-- Dates --}}
             <div class="col-md-3">  
                <label>Start Date:</label>
                <input data-toggle="datepicker" name="start_at" class="form-control col-md-12">
            </div>
            <div class="col-md-3">
                <label>End Date:</label>
                <input data-toggle="datepicker" name="end_at" class="form-control col-md-12">
            </div>
            
            {{-- Account to reconcile --}}
          <div class="col-md-3">
              <label>{{ trans('account.account') }}</label>
             {!! Form::select('account', $accounts,null, ['class'=>'form-control account'])!!}
          </div>
           <div class="col-md-3">  
                <label>&nbsp;</label>
               <button class="btn btn-success col-md-5 form-control">
                  <i class="fa fa-upload"></i> {{ trans('general.upload') }}
                </button>
            </div>          
            {!! Form::close() !!}
      </div>

      <div class="col-md-12 bg-gray-200">
        <form action="{{ route('reconciliations.bank.reconciliations.download') }}" method="GET" target="_blank">
          {{ csrf_field() }}
          @if (isset($uploadedData))
            <div class="col-md-12">
                  <input  type="submit" name="download_type"  class="btn btn-success" value="{{ trans('general.export_matching_bank') }}">
                  <input  type="submit" name="download_type" class="btn btn-primary" value="{{ trans('general.export_matching_ceb') }}">
                  <input  type="submit" name="download_type" class="btn btn-warning" value="{{ trans('general.export_not_in_ceb') }}">
                  <input  type="submit" name="download_type" class="btn btn-inverse" value="{{ trans('general.export_not_in_bank') }}">
            </div>

            <div class="col-md-6">
                  @include('reconciliations.banks.bank-results')
            </div>
          @endif
          
          @if (isset($transactions))  
              <div class="col-md-6 bg-yellow-100">
                  @include('reconciliations.banks.account-transactions')
              </div>
            @endif
        </form>
      </div>