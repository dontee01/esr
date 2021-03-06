<aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- User profile -->
                <div class="user-profile">

                    <div class="profile-img"> <img src="{{ asset('assets/images/users/1.jpg') }}" alt="user" /> </div>

                    <div class="profile-text"> <a href="#" class="dropdown-toggle link u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{ Session::get('username') }} </a>
                        <!-- <div class="dropdown-menu animated flipInY">
                            <a href="#" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                            <a href="#" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
                            <a href="#" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                            <div class="dropdown-divider"></div> <a href="login.html" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                        </div> -->
                    </div>
                </div>
                <!-- End User profile text-->
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">

                        <li>
                            <a class="" href="{{ url('console/funding-requests') }}" ><i class="mdi mdi-home"></i><span class="hide-menu">Funding Requests</span></a>
                        </li>

                        <li>
                            <a class="" href="{{ url('console/withdrawal-requests') }}" ><i class="mdi mdi-chart-bubble"></i><span class="hide-menu">Withdrawal Requests</span></a>
                        </li>

                        <li class="nav-devider"></li>

                        <li>
                            <a class="" href="{{ url('logout') }}" ><i class="fa fa-power-off"></i><span class="hide-menu">Logout</span></a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
           
        </aside>