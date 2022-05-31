<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title> Forgot your Password? </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       <!-- Bootstrap 3.3.2 -->
    <link href="{{Url()}}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="{{Url()}}/assets/fontawesome/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="{{Url()}}/assets/ionicons/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{Url()}}/assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{Url()}}/assets/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">

  @include('sentinel.layouts.notifications')
  <!-- ./ notifications -->
    <div class="login-box">
      
      <!-- Notifications -->
      <div class="login-box-body">

<form method="POST" action="{{ route('sentinel.reset.password', [$hash, $code]) }}" accept-charset="UTF-8">

        <div class="small-6 large-centered columns">  

            <h3>Reset Your Password</h3>
    		
             <div class="row">
                 <div class="small-3 columns">
                     <label for="right-label" class="right inline">New</label>
                 </div>
                 <div class="small-9 columns">
                     <input class="form-control" placeholder="New Password" name="newPassword" value="" id="newPassword" type="password">
                     {{ ($errors->has('newPassword') ?  $errors->first('newPassword', '<small class="error">:message</small>') : '') }}
                 </div>

                 <div class="small-3 columns">
                     <label for="right-label" class="right inline">Confirm</label>
                 </div>
                 <div class="small-9 columns">
                     <input class="form-control" placeholder="Confirm New Password" name="newPassword_confirmation" value="" id="newPassword_confirmation" type="password">
                     {{ ($errors->has('newPassword_confirmation') ?  $errors->first('newPassword_confirmation', '<small class="error">:message</small>') : '') }}
                 </div>

                <div class="small-10  columns">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input class="btn btn-success" value="Reset Password" type="submit">
                </div>
             </div>
</form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="{{Url()}}/assets/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{Url()}}/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="{{Url()}}/assets/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>

