<?php

/*
 * B+
 * Copyright (C) 2017 Jorge Vieira, José Sousa, Miguel Reboiro-Jato,
 * Noé Vázquez, Bárbara Amorim, Cristina P. Vieira, André Torres, Hugo
 * López-Fernández, and Florentino Fdez-Riverola
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>B+ Project - @yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>B+ Project</title>
    <link href="{{URL::asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('css/jquery-ui.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('css/style.css')}}" rel="stylesheet">
    <link href="{{URL::asset('css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{URL::asset('css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    @yield('startcss')
    <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="top-icons">
                    <span class="top-icons-space">
                        <a class="top-icons-link" href="/"><i class="fa fa-home fa-lg iconformat_top" title="Home"></i></a>
                        <a class="top-icons-link" href="mailto:jbvieira@ibmc.up.pt"><i class="fa fa-envelope fa-lg iconformat_top" title="Contact us"></i></a>
                        <!--a class="top-icons-link" href="howtocite.php"><i class="fa fa-pencil-square fa-lg iconformat-top" title="How to cite?" aria-hidden="true"></i></a-->
                    </span>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::check() && Auth::user()->role_id == \App\Providers\AuthServiceProvider::ADMIN_ROLE)
                        <li><a href="{{ url('/user/manage') }}">Manage Users</a></li>
                    @endif
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                    @else
                        <li><a href="{{ url('/project/manage') }}">Manage Projects</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/user/edit') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('edit-form').submit();">
                                        Edit profile
                                    </a>

                                    <form id="edit-form" action="{{ url('/user/editOwn') }}" method="POST" style="display: none;">
                                        {{ Form::hidden('id', Auth::user()->id) }}
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                                <li>
                                    <a href="{{ url('/logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <div class="banner">
        <img src="{{URL::asset('images/bpositive.png')}}" alt="Project Logo" class="project-logo"/>
    </div>
    {{--
    @section('sidebar')
        This is the master sidebar.
    @show
    --}}
    <div class="container">
        @if (count($errors) > 0)
            <div class="alert alert-warning">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (isset($results) && count($results) > 0)
            <div class="alert alert-success">
                <ul>
                    @foreach ($results as $result)
                        <li>{{ $result }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>

    <div class="nav navbar-inverse navbar-static-bottom">
        <div class="container">
            <div class="footer-left">
                <p style="float: left">
                    <a href="http://www.i3s.up.pt"><img src="{{URL::asset('images/i3s.png')}}" alt="i3S"></a>
                </p>
                <p class="rodape-info">
                    Phone: +351 220 408 800 <br/>
                    Email: info@i3s.up.pt <br/>
                    Address: Rua Alfredo Allen, 208 <br/>
                    4200-135 Porto, Portugal
                </p>
            </div>
            <div class="footer-right">
                <p style="color: white; font-weight: bold; font-size: 1.2em"><strong>If you use B+, please cite us:</strong></p>
                <p>
                    N. V&aacute;zquez, C.P. Vieira, B.S.R. Amorin, A. Torres, H. L&oacute;pez-Fern&aacute;ndez, F. Fdez-Riverola, J.L.R. Sousa, M. Reboiro-Jato, J. Vieira (2017)
                    On the automated collection and sharing (B+ database) of data on adaptive amino acid changes.
                    <em>11<sup>th</sup> International Conference on Practical Applications of Computational Biology &amp; Bioinformatics.</em> Porto, Portugal
                </p>
                <span>
                    <a class="footer-social-icons" href="https://www.facebook.com/i3s.up.pt/?fref=ts/" target="_new"><i class="fa fa-facebook fa-lg" aria-hidden="true"></i></a>
                    <a class="footer-social-icons" href="https://twitter.com/i3si3" target="_new"><i class="fa fa-twitter fa-lg" aria-hidden="true"></i></a>
                    <a class="footer-social-icons" href="https://www.linkedin.com/company/10318110?trk=tyah&trkInfo=clickedVertical%3Acompany%2CclickedEntityId%3A10318110%2Cidx%3A1-1-1%2CtarId%3A1479142019953%2Ctas%3Ainstituto%20de%20investiga%C3%A7%C3%A3o%20" target="_new"><i class="fa fa-linkedin fa-lg" aria-hidden="true"></i></a>
                    <a class="footer-social-icons" href="https://pt.pinterest.com/i3suppt/" target="_new"><i class="fa fa-pinterest fa-lg" aria-hidden="true"></i></a>
                </span>
                <span>
                    <a class="footer-icons" href="https://www.ibmc.up.pt" target="_new"><img src="{{URL::asset('images/IBMC.png')}}" alt="IBMC"></a>
                    <a class="footer-icons" href="http://www.ineb.up.pt" target="_new"><img src="{{URL::asset('images/INEB.png')}}" alt="INEB"></a>
                    <a class="footer-icons" href="https://www.ipatimup.pt" target="_new"><img src="{{URL::asset('images/IPATIMUP.png')}}" alt="IPATIMUP"></a>
                    <a class="footer-icons" href="http://www.up.pt" target="_new"><img src="{{URL::asset('images/UPORTO.png')}}" alt="UPORTO"></a>
                </span>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{URL::asset('js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/bootstrap-colorpicker.min.js')}}"></script>
@yield('endscripts')
</body>
</html>
