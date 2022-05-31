<?php $defaultPermissions = config('sentinel.default_permissions', []); ?>
<?php $permissionGroup = null;  ?>
<div class="row">
@foreach ($defaultPermissions as $permission)
{{-- is this a new permission group then starts it's grid --}}
   @if ($permissionGroup != explode('.', $permission)[0] )
      {{-- If this the permission is not null , then I need to close previous group --}}
       @if (!is_null($permissionGroup))
                </ul>
              </div>
            </div>
          </div>
       @endif

      {{-- Open new group grid  --}}
      <div class="col-md-3">
          <div class="box box-successs collapsed-box">
              <div class="box-header with-border"> 
               <strong "box-title">{!! $permissionGroup = explode('.', $permission)[0] !!}</strong>
               <div class="box-tools pull-right" style="width: 5%">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
              </div>
            <div class="box-body">
            <ul>
            @endif
            <li>
              <input name="permissions[{!! $permission !!}]" value="1" type="checkbox" {!! (isset($permissions[$permission]) ? 'checked' : '') !!}>
               {{ trans( $permissionGroup.'.'.str_replace('.', '_', $permission)) }}
            </li>      
      @endforeach
      </ul>
    </div>
  </div>
</div>