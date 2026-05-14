<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ url('/assets/images/favicon.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ url('/assets/images/favicon.png') }}" type="image/x-icon">
        <title>{{ env('APP_NAME') }} - Access Denied</title>
        <!-- Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/font-awesome.css') }}">
        <!-- ico-font-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/icofont.css') }}">
        <!-- Themify icon-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/themify.css') }}">
        <!-- Flag icon-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/flag-icon.css') }}">
        <!-- Feather icon-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/feather-icon.css') }}">
        <!-- Plugins css start-->
        <!-- Plugins css Ends-->
        <!-- Bootstrap css-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/bootstrap.css') }}">
        <!-- App css-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/style.css') }}">
        <link id="color" rel="stylesheet" href="{{ url('/assets/css/color-1.css') }}" media="screen">
        <!-- Responsive css-->
        <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/responsive.css') }}">
    </head>   
    <body>
        <!-- tap on top starts-->
        <div class="tap-top"><i data-feather="chevrons-up"></i></div>
        <!-- tap on tap ends-->
        <!-- page-wrapper Start-->
        <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- error-403 start-->
        <div class="error-wrapper">
            <div class="container"><img class="img-100" src="{{ url('/assets/images/other-images/sad.png') }}" alt="">
                <div class="error-heading">
                    <h2 class="headline font-success">403</h2>
                </div>
                <div class="col-md-8 offset-md-2">
                    <p class="sub-content">You don't have permission to access this page.</p>
                </div>
                <div><a class="btn btn-success btn-lg" href="{{ route('home') }}">BACK TO HOME PAGE</a></div>
            </div>
        </div>
        <!-- error-403 end-->
        </div>
        <!-- latest jquery-->
        <script src="{{ url('/assets/js/jquery.min.js') }}"></script>
        <!-- Bootstrap js-->
        <script src="{{ url('/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <!-- feather icon js-->
        <script src="{{ url('/assets/js/icons/feather-icon/feather.min.js') }}"></script>
        <script src="{{ url('/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
        <!-- scrollbar js-->
        <!-- Sidebar jquery-->
        <script src="{{ url('/assets/js/config.js') }}"></script>
        <!-- Plugins JS start-->
        <!-- calendar js-->
        <!-- Plugins JS Ends-->
        <!-- Theme js-->
        <script src="{{ url('/assets/js/script.js') }}"></script>
    </body>   
</html>