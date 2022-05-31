<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>EPSSF MS</title>
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
    <div class="login-box">

      <div class="login-box-body">
      <div class="login-logo">
        <a href="{{Url()}}" class="logo">
          <img src="{{Url('assets/dist/img/logo1.jpg')}}" style="width:100px;height: 100px;">
        </a>
       </div><!-- /.login-logo -->

	   <!-- Notifications -->
	   @include('Sentinel::layouts/notifications')
	   <!-- ./ notifications -->

       <p class="login-box-msg">Sign in to continue</p>

       <form method="POST" action="{{ route('sentinel.session.store') }}" accept-charset="UTF-8">
       <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="form-group has-feedback">
               <input placeholder="Email" autofocus="autofocus" class="form-control" name="email" type="text"  value="{{ Input::old('email') }}">
                {{ ($errors->has('email') ? $errors->first('email', '<small class="error">:message</small>') : '') }}
          </div>
          <div class="form-group has-feedback">
             <input class="form-control" placeholder="Password" name="password" value="" type="password">
             {{ ($errors->has('password') ?  $errors->first('password', '<small class="error">:message</small>') : '') }}
          </div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <input name="rememberMe" value="rememberMe" type="checkbox">
                Remember Me
                </label>

              </div>
                        <a href="{{ route('sentinel.forgot.form') }}">Forgot Password</a>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <input class="btn btn-primary  btn-block btn-flat" value="Sign In" type="submit">
            </div><!-- /.col -->

          </div>

        </form>




      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->

    <script src="/assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>

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
