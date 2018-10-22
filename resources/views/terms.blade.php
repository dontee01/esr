<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{env('SITE_NAME')}}</title>

    <link rel="shortcut icon" href="{{ asset('landing/img/core-img/logo.jpg') }}">

    <link rel="stylesheet" href="{{ asset('landing/style.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/responsive.css') }}">

    <!--[if IE]>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body class="dark_version">
    <!-- preloader start -->
    <div id="preloader"></div>
    <!-- /.end preloader -->

    <!-- ***************** Header Start ***************** -->
    <header class="header_area">
        <div class="main_header_area" id="sticky">
            <div class="container">
                <div class="row">

                    <div class="col-sm-2 col-xs-9">
                        <div class="logo_area">
                            <a href="{{ url('/')}}">LMI
                                <!-- <img src="img/core-img/logo.png" alt=""> -->
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-10 col-xs-12">
                        <!-- Menu Area Start -->
                        <div class="main_menu_area">
                            <div class="mainmenu">
                                <nav>
                                    <ul id="nav">
                                        <li><a href="{{url('/')}}">Home</a></li>

                                        @if (!Session::has('uid'))
                                        <li><a href="{{url('login')}}">Login</a></li>
                                        <li><a href="{{url('register')}}">Sign Up</a></li>

                                        @else
                                        <li><a href="{{url('/dashboard')}}">Dashboard</a></li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>

                        </div>
                        <!-- Menu Area End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Header Area End -->
    </header>
    <!-- ***************** Header End ***************** -->

    <!-- ************** About Us Area Start ************** -->
    <section class="about_area" id="about">
        <div class="container">
            <div class="about_us_area">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-xs-12 col-md-8 section_padding_100 wow fadeInUp">
                        <!-- Section Heading Start -->
                        <div class="section_heading">
                            <p>carefully read our</p>
                            <h3>Terms &amp; Conditions</h3>
                        </div>
                        <!-- Section Heading End -->

                        <!-- About Us Text Start -->
                        <div class="about_us_text" style="text-align: left;">
                            <p>By signing up for membership, you agree to the following Terms, Conditions and Disclaimers:</p>
                            <ul>
                                <li>Membership fee is non-refundable once the online registration has been completed.</li>
                                
                                <li>We are not an investment platform. We only refer to earn. NO REFERALS; NO EARNINGS and your matrix must be completed to earn the corresponding bonus/ incentives.</li>
                                
                                <li>All accounts MUST have a minimum of TWO referrals to earn the corresponding completion bonus and other incentives.</li>
                                
                                <li>The investment/ business grant is per person and not per accounts. You only get the grants on ONLY one of your accounts. This also applies with the travel incentive.</li>
                                
                                <li>The board of directors can review the membership fee of $50 upward when the need arises as long as a notice of a minimum of one month is given.</li>
                                
                                <li>All incentives and rewards must be given out within 6 and 12 months upon qualification.</li>
                                
                                <li>Without the approval of the board of directors, the printing of materials that carries the company’s symbols, information etc is considered illegal which can lead to the termination of membership and in some cases forfeiture of bonuses and incentives.</li>
                                
                                <li>Members found soliciting  the involvement of any WMGC members in any form of multi level referral system or direct sales AND member found compelling registered members to resign under them will have their membership terminated and incentive/bonus forfeited.</li>
                                
                                <li>Any member whose membership is terminated and incentive/bonus withheld have the right to make representation to WMGC compliance/legal department for review.</li>
                                
                                <li>Any member found using the name of WMGC for fraudulent activities will be handled to the appropriate authorities with their membership terminated as well as the forfeiture of their bonus & incentives.</li>

                                <li>Under no circumstances, including negligence, shall we, or anyone else involved in creating, producing, managing or advertising this service, be liable for any direct, indirect, incidental, special or consequential damages that result from the use of, or inability to use this service, and all the files and software contained within it, including, but not limited to, reliance on any information obtained through this service; or that result from mistakes, omissions, interruptions, deletion of files or e-mail, errors, defects, viruses, delays in operation, or transmission, or any failure of performance, whether or not limited to acts of God, communications failure, theft, destruction or unauthorized access to our records, programs or services.</li>
                                
                                <li>We reserve the right to add or remove features and functionality, make changes on how the platform works, manage memberships, within this site, and otherwise make changes to the service and this agreement without notice.</li>
                            </ul>

                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- end./ container -->
    </section>
    <!-- ************** Awesome Feature Area End ************** -->



    <!-- ************** Call to action Area Start ************** -->
    <div class="call_to_action section_padding_60">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <!-- call to action text -->
                    <div class="call_to wow fadeInUp" data-wow-delay=".2s">
                        <h3>We provide the best donation services.</h3>
                        <div class="call_to_action_button">
                            <a class="btn btn-default" href="#" role="button">Join Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ************** Call to action Area End ************** -->

    <!-- ************** Footer Area Start ************** -->
    <footer class="footer_area">
        <!-- Bottom Footer Area Start -->
        <div class="footer_bottom_area">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="footer_bottom wow fadeInDown" data-wow-delay=".2s">
                            <p>Copyright &copy; {{ date('Y')}} {{ env('SITE_NAME') }} | <a href="{{url('tos')}}">Terms &amp; Conditions</a> | <a href="{{env('SITE_EMAIL')}}" class="">{{env('SITE_EMAIL')}}</a></p>
                        </div>
                        <!-- <div class="footer_bottom wow fadeInDown" data-wow-delay=".2s">
                            <p>Copyright &copy; 2017 Extra Donor | <a href="">Terms &amp; Conditions</a></p>
                        </div> -->
                        <!-- Bottom Footer Copywrite Text Area End -->
                    </div>
                </div>
                <!-- end./ row -->
            </div>
            <!-- end./ container -->
        </div>
        <!-- Bottom Footer Area End -->
    </footer>
    <!-- ************** Footer Area End ************** -->

    <!-- ************** All jQuery Plugins ************** -->

    <!-- jQuery (necessary for all JavaScript plugins) -->
    <script src="{{ asset('landing/js/jquery-2.2.4.min.js') }}"></script>

    <!-- Bootstrap js -->
    <script src="{{ asset('landing/js/bootstrap.min.js') }}"></script>

    <!-- Waypoint js -->
    <script src="{{ asset('landing/js/jquery.waypoints.min.js') }}"></script>

    <!-- Owl-carousel js -->
    <script src="{{ asset('landing/js/owl.carousel.min.js') }}"></script>

    <!-- Ajax Contact js -->
    <script src="{{ asset('landing/js/ajax-contact.js') }}"></script>

    <!-- Meanmenu js -->
    <script src="{{ asset('landing/js/meanmenu.js') }}"></script>

    <!-- Onepage Nav js -->
    <script src="{{ asset('landing/js/jquery.nav.min.js') }}"></script>

    <!-- Magnific Popup js -->
    <script src="{{ asset('landing/js/jquery.magnific-popup.min.js') }}"></script>

    <!-- Counterup js -->
    <script src="{{ asset('landing/js/counterup.min.js') }}"></script>

    <!-- Back to top js -->
    <script src="{{ asset('landing/js/jquery.scrollUp.js') }}"></script>

    <!-- jQuery easing js -->
    <script src="{{ asset('landing/js/jquery.easing.1.3.js') }}"></script>

    <!-- Sticky js -->
    <script src="{{ asset('landing/js/jquery.sticky.js') }}"></script>

    <!-- WOW js -->
    <script src="{{ asset('landing/js/wow.min.js') }}"></script>

    <!-- parallux js -->
    <script src="{{ asset('landing/js/jquery.stellar.min.js') }}"></script>

    <!-- Active js -->
    <script src="{{ asset('landing/js/custom.js') }}"></script>

</body>

</html>