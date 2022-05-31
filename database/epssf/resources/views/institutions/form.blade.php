         <div class="form-group">
            {!! Form::label('name',trans('institution.name')) !!}
            {!! Form::text('name', $institution->name,array('class' => 'form-control institution name')) !!}           
        </div>