<link href="{{Url()}}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="{{Url()}}/assets/dist/css/modalPopup.css" rel="stylesheet" type="text/css" />

<div class="container">
    <div class="row">
        <!-- You can make it whatever width you want. I'm making it full width
             on <= small devices and 4/12 page width on >= medium devices -->
        <div class="col-xs-8 col-md-8 ModalPopup"> 
{!! Form::open(array('url' => route('attornies.store').'?member='.$member,'files' => true )) !!}
	@include('attornies.form',['button'=>trans('general.add')])
{!! Form::close() !!}
</div>
</div>
</div>
