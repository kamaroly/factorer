<div class="box">
{{--  @if ((request()->is('members*') || request()->is('loans*') || request()->is('regularisation*')) && !request()->is('loans/pending*') || request()->is('loans/complete'))
            
    @if(!request()->is('regularisation/types') && !request()->is('regularisation/regulate') && !request()->isMethod('post') && !request()->is('loans/blocked') && !request()->is('members*transacts') && !request()->is('loans/unblock*'))
       @include('members.search')
    @endif
 @endif --}}
    <div class="box-header with-border">
      <div class="box-tools">
      {{-- Request::is('regularisation/types') --}}
    </div>
    <div class="box-body">
      @yield('content')
    </div><!-- /.box-body -->
    <div class="box-footer">
      @yield('content_footer')
  </div><!-- /.box-footer-->
</div>