<!DOCTYPE html>
<html>
  @include('partials.head')

  <body class="bg-blue-800 sidebar-mini {{ ((Request::is('report*')|| Request::is('regularisation*') || Request::is('loans*')|| Sentry::getUser()->hasAccess('ceb.view.own.profile') || Request::is('log*')) ? 'sidebar-collapse' : '') }}">
    <!-- Site wrapper -->
    <div class="wrapper">
    @include('partials.nav_header')
      <!-- =============================================== -->
      <!-- Left side column. contains the sidebar -->
      @if(!Sentry::getUser()->hasAccess('ceb.view.own.profile') || (Sentry::check() && Sentry::getUser()->hasAccess('admin'))) 
        @include('partials.nav_left')
      @endif
      <!-- =============================================== -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('partials.content_header')
        
        <!-- Main content -->
        <section class="content">

        @include('layouts.notifications')
        @include('flash::message')

          <!-- Default box -->
            @include('partials.content_box')
          <!-- /.box -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      @include('partials.footer')

      <!-- Control Sidebar -->

      <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      {{-- <div class='control-sidebar-bg'></div> --}}
    </div><!-- ./wrapper -->

    @include('partials.scripts')
  </body>
</html>