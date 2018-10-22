<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo.png') }}">
    <title>@yield('title') | {{ env('SITE_NAME') }}</title>
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header card-no-border">

    <!-- <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div> -->
    <div id="main-wrapper">
        
        @include('layouts.include.header')
        
        @include('layouts.include.side-nav')

        <div class="page-wrapper">
            <div class="container-fluid">
                
                @yield('content')
                <!-- End PAge Content -->
                <!-- ============================================================== -->
            </div>
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                &#169; {{ date('Y').' '.env('SITE_NAME') }}
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
    </div>

    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/tether.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>

    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>

    <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>

    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>
    
    <script type="text/javascript">
        
        $('#country').change(
            function()
            {
                var country_id = $('#country').find(":selected").attr('data-country-id');
                
                option = '<option value="">-Please select a state-</option>';
                  $.get("{{ url('get-states') }}", {"country_id" : country_id}, function(states){
                      if (states.length == 0)
                      {
                          
                      }
                      else
                      {
                          // option = '<option value="'.states.name.'">'..'</option>';
                          // for(i = 0)
                          jQuery.each(states, function(index, item) {
                              // console.dir(index+' kkkk ' );
                              // console.log(JSON.stringify(item)+' jjj '+index );
                              option += '<option value="'+item.name+'" data-state-id ="'+item.id+'">'+item.name+'</option>';
                          });
                      }
                      $('#state').html(option);
                      // $('#mailing_city').html('<option value=""></option>');
                })
        });
    </script>
</body>

</html>
