<div class="col-lg-12 col-sm-12">
    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
        <div class="btn-group" role="group">
            <label>{!! $attorney->names !!}</label>
            @include('files.show',['filename'=>$attorney->photo])
        </div>
        <div class="btn-group" role="group">
            @include('files.show',['filename'=>$attorney->signature])
        </div>
    </div>   
</div>
            
    