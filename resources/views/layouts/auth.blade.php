<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="{{ url('assets/images/favicon.png" type="image/x')}}-icon">
        <link rel="shortcut icon" href="{{ url('assets/images/favicon.png" type="image/x')}}-icon">
        <title>{{config('app.name')}} - Sign In</title>

        <!-- Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/font-awesome.css')}}">

        <!-- latest jquery-->
        <script src="{{url('assets/js/jquery.min.js')}}"></script>

        <!-- ico-font-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/icofont.css')}}">

        <!-- Themify icon-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/themify.css')}}">

        <!-- Flag icon-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/flag-icon.css')}}">

        <!-- Feather icon-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/feather-icon.css')}}">

        <!-- Plugins css start-->
        <!-- Plugins css Ends-->
        <!-- Bootstrap css-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/bootstrap.css')}}">

        <!-- App css-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/style.css')}}">
        <link id="color" rel="stylesheet" href="{{url('assets/css/color-1.css')}}" media="screen">

        <!-- Responsive css-->
        <link rel="stylesheet" type="text/css" href="{{url('assets/css/responsive.css')}}">
    </head>
    <body>
        <div class="container-fluid p-0">
            <div class="row m-0">
                <div class="col-12 p-0">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- latest jquery-->
      <script src="{{ url('assets/js/jquery.min.js')}}"></script>
      <!-- Bootstrap js-->
      <script src="{{ url('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
      <!-- feather icon js-->
      <script src="{{ url('assets/js/icons/feather-icon/feather.min.js')}}"></script>
      <script src="{{ url('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
      <!-- scrollbar js-->
      <!-- Sidebar jquery-->
      <script src="{{ url('assets/js/config.js')}}"></script>
      <!-- Plugins JS start-->
      <!-- calendar js-->
      <!-- Plugins JS Ends-->
      <!-- Theme js-->
      <script src="{{ url('assets/js/script.js')}}"></script>
    </body>
</html>