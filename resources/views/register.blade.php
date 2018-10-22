<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo.png') }}">
    <title>Register | {{ env('SITE_NAME') }}</title>
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" id="theme" rel="stylesheet">
</head>

<body>
    <!-- <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div> -->
    <section id="wrapper" class="login-register login-sidebar"  style="background-image:url({{ asset('assets/images/background/login-register.jpg') }});">
  <div class="login-box card">
    <div class="card-body">
      <form class="form-horizontal form-material" id="loginform" method="post" action="{{ url('register') }}">
      	{{ csrf_field() }}
        <a href="{{ url('/') }}" class="text-center db"><br/><img src="{{ asset('assets/images/logo.png') }}" alt="" /></a>
        @include('common.errors')

                @if (Session::has('flash_message'))
                <div align="center" class="alert alert-danger alert-dismissable mw800 center-block">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                    <strong>{{Session::get('flash_message')}}</strong>
                </div>
                @endif

                @if (Session::has('flash_message_success'))
                <div align="center" class="alert alert-success alert-dismissable mw800 center-block">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                    <strong>{{Session::get('flash_message_success')}}</strong>
                </div>
                @endif

                @if (Session::has('flash_message_verified_error'))
                <div align="center" class="alert alert-danger alert-dismissable mw800 center-block">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                    <strong>{{Session::get('flash_message_verified_error')}}</strong>
                </div>
                @endif

                @if (Session::has('flash_message_verified_success'))
                <div align="center" class="alert alert-success alert-dismissable mw800 center-block">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                    <strong>{{Session::get('flash_message_verified_success')}}</strong>
                </div>
                @endif
        <h3 class="box-title m-t-40 m-b-0">Register Now</h3><small>Create your account and enjoy</small> 
        <div class="form-group m-t-20">
          <div class="col-xs-12">
            <input class="form-control" type="text" name="fullname" required="" placeholder="Full Name" value="{{ old('fullname') }}">
          </div>
        </div>

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" name="email" required="" placeholder="Email" value="{{ old('email') }}">

          </div>
        </div>

        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" name="mobile" required="" placeholder="Mobile Number" value="{{ old('mobile') }}">
          </div>
        </div>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" name="password" type="password" required="" placeholder="Password">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" name="password_confirmation" type="password" required="" placeholder="Confirm Password">
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-12">
            <div class="checkbox checkbox-primary p-t-0">
              <input id="checkbox-signup" type="checkbox">
              <label for="checkbox-signup"> I agree to all <a href="{{ url('tos')}}">Terms</a></label>
            </div>
          </div>
        </div>
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Sign Up</button>
          </div>
        </div>
        <div class="form-group m-b-0">
          <div class="col-sm-12 text-center">
            <p>Already have an account? <a href="{{ url('login') }}" class="text-info m-l-5"><b>Sign In</b></a></p>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/bootstrap/js/tether.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>
</body>

</html>