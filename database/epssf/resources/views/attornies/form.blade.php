@extends('layouts.popup')
@section('content')
<link href="{{Url()}}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="row">
  <div class="col-xs-3 col-md-3">
      <div class="form-group">
 <label>{{ trans('attornies.names') }}</label>
  {!! Form::input('text', 'names', null, ['class'=>'form-control']) !!}
</div>
</div>
 <div class="col-xs-3 col-md-3">
      <div class="form-group">
 <label>{{ trans('attornies.nid') }}</label>
  {!! Form::input('text', 'nid', null, ['class'=>'form-control']) !!}
</div>
</div>
  <div class="col-xs-3 col-md-3">
      <div class="form-group">
 <label>{{ trans('general.upload_photo') }}</label>
 <input type="file" name="photo" id="upload-photo" accept="image/*">
 </div>
 </div>

    <div class="col-xs-3 col-md-3">
        <div class="form-group">
 <label>{{ trans('general.signature_selected') }}</label>

 <input type="file" name="signature" id="upload-signature" accept="image/*">
</div>
</div>
</div>
<div class="row">
    <div class="col-xs-12">
        <button class="btn btn-success btn-lg btn-block" type="submit">{{ trans('general.save') }}</button>
    </div>
</div>

@endsection