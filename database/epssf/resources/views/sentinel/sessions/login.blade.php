<!DOCTYPE html>
<html>
<head>
	<link href="{{Url()}}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ url('assets/tailwind.min.css') }}">

	<title>
		Login page
	</title>
</head>
<body class="bg-blue-700">
<div class="flex h-screen">
	<!-- LEFT MARKETING SIDE -->
	<div class="h-full w-1/1 m-auto">
	<img src="{{Url('assets/images/login2.PNG')}}" class="h-full rounded-lg mx-auto border-10 border-blue-900 ">
		
	</div>
	<!-- RIGHT LOGIN FORM SIDE -->
	<div class="h-full w-1/3 bg-gray-200 pl-32 pr-32"> 
	  <div class="w-full content-center pt-16">	
	      <img src="{{Url('assets/images/logo.jpg')}}" class="h-32 w-32 rounded-lg mx-auto border-2 border-blue-700 ">
	  </div>
    <form method="POST" action="{{ route('sentinel.session.store') }}" accept-charset="UTF-8" class="pt-8">
	       <input type="hidden" name="_token" value="{{ csrf_token() }}">
	       <span class="text-blue-800 leading-tight text-center text-xl font-semibold pb-8"> 
				Sign in with your organization account
	       </span>
		    <div class="mb-4 mt-8">
		      <label class="block text-blue-700 text-xl font-bold mb-2" for="username">
		        Email
		      </label>
		      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-blue-700 leading-tight focus:outline-none focus:shadow-inner
		      				focus:border-2 border-blue-600" 
		             name="email" 
		             value="{{ Input::old('email') }}"
		             type="text" 
		             placeholder="Enter your Email"
					 autofocus 
		             >
     		       {!! ($errors->has('password') ?  $errors->first('email', '<p class="text-red-500 text-sm italic">:message</p>') : '') !!}
		    </div>
		    <div class="mb-6">
		      <label class="block text-blue-700 text-xl font-bold mb-2" for="password">
		        Password
		      </label>
		      <input class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-blue-700 mb-3 leading-tight focus:outline-none
		      				focus:shadow-inline focus:border-2 border-blue-600" 
		      				name="password" 
		      				type="password" 
		      				placeholder="******************">
		       {!! ($errors->has('password') ?  $errors->first('password', '<p class="text-red-500 text-sm italic">:message</p>') : '') !!}
		    </div>
		    <div class="flex items-center justify-between">
		      <input class="bg-blue-600 hover:bg-blue-700 text-blue-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" value="Sign In" type="submit">
		      <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('sentinel.forgot.form') }}">
		        Forgot Password?
		      </a>
		    </div>
		  </form>
			  <p class="text-center text-gray-500 text-sm mb-0 inline-block align-text-bottom">
			    &copy;2017 - {{ date('Y') }} All rights reserved.
			  </p>
			</div>
		</div>
	</body>
</html>