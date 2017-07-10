<header class="site-header">
    <div class="container">
        <a href="/" class="site-logo">
            <img class="hidden-md-down" src="img/cadabra-logo.png" alt="">
            <img class="hidden-lg-up" src="img/cadabra-logo.png" alt="">
        </a>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>
        <div class="site-header-content">
            <div class="site-header-content-in">
                @if(auth()->check())
                    <div class="site-header-shown">
                        <div class="dropdown user-menu">
                            <span class="merchant-name">
                                {{ auth()->user()->name }}
                                <span style="display:block;font-size:0.6em;text-align:right;">{{ auth()->user()->email }}</span>
                            </span>
                            <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="img/avatar-2-64.png" alt="">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                                <a class="dropdown-item" href="/profile"><span class="font-icon glyphicon glyphicon-user"></span>Profile</a>
                                <a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-question-sign"></span>Help</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout"><span class="font-icon glyphicon glyphicon-log-out"></span>Logout</a>
                            </div>
                        </div>

                        <button type="button" class="burger-right">
                            <i class="font-icon-menu-addl"></i>
                        </button>
                    </div><!--.site-header-shown-->

                    <div class="mobile-menu-right-overlay"></div>
                    <div class="site-header-collapsed">
                        <div class="site-header-collapsed-in">
                            <div class="dropdown dropdown-typical">
                                <div class="dropdown-menu" aria-labelledby="dd-header-sales">
                                    <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                    <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                    <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                    <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                    <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                                </div>
                            </div>
                        </div><!--.site-header-collapsed-in-->
                    </div><!--.site-header-collapsed-->
                @endif
            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header>
