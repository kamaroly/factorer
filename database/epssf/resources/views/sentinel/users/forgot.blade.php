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
    <div class="login-box">
      
      <div class="login-box-body">
      <!-- Notifications -->
  @include('Sentinel::layouts/notifications')
  <!-- ./ notifications -->

<form method="POST" action="{{ route('sentinel.reset.request') }}" accept-charset="UTF-8">
    <input name="_token" value="{{ csrf_token() }}" type="hidden">
    
        <div class="small-6 large-centered columns">           
            <h3>Forgot your Password?</h3>
            
            
                <div class="small-2 columns">
                    <label for="right-label" class="right inline">Email</label>
                </div>
                <div class="small-10 columns {{ ($errors->has('email')) ? 'error' : '' }}">
                    <input class="form-control" placeholder="E-mail" autofocus="autofocus" name="email" type="text" value="{{ Input::old('email') }}">
                    {{ ($errors->has('email') ? $errors->first('email', '<small class="error">:message</small>') : '') }}
                </div>
           

            
                <div class="small-10 col-offset-2 columns">
                <br>
                    <input class="btn btn-success" value="Send Instructions" type="submit">
               
           
  		
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
