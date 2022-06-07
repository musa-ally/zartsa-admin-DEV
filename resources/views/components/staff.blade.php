<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
	
		<!-- Custom CSS -->
	<link href="{{ asset('/staff/css/custom-styles.css') }}" rel="stylesheet">
	
	<!-- Bootstrap 4 -->
	<link href="{{ asset('/staff/css/bootstrap.css') }}" rel="stylesheet">

	<!-- Fonts & Icons -->
   <link href="{{ asset('/staff/css/material-icons/iconfont/material-icons.css') }}" rel="stylesheet">
   <link href="{{ asset('/staff/css/material-design-fonts/css/materialdesignicons.css') }}" rel="stylesheet">

    <!-- Styles -->
	<link href="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ URL::to('/css/main.css') }}">
    <link rel="stylesheet" href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	  @if (Auth::check()) 
          <meta name="user-id" content="{{ Auth::user()->id }}">
       @endif 
</head>
<body>
@if(\Illuminate\Support\Facades\Auth::user()->hasRole('staff'))
<div class="master-sidebar active">
    <div class="logo-content">
        <div class="logo">
            <i class='bx bxs-ship'></i>
            <div class="logo-name">ZPC</div>
        </div>
        <i class='bx bx-menu' id="btn-menu"></i>
    </div>
    <ul class="nav-list">
        <li>
            <a href="{{url('staff_dashboard')}}"  class="list {{ request()->is('staff_dashboard*') ? 'active' : '' }}">
                <i class='bx bx-file'></i>
                <span class="link-name title-18">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
	<li>
      <a href="{{url('customer_applications')}}"  class="list {{ request()->is('customer_applications*') ? 'active' : '' }}">
                <i class='bx bx-file'></i>
                <span class="link-name title-18">Customer Applications</span>
            </a>
            <span class="tooltip">Customer Applications</span>
        </li>
        <li>
            <a href="{{url('my_applications')}}"  class="list {{ request()->is('my_applications*') ? 'active' : '' }}">
                <i class='bx bx-file'></i>
                <span class="link-name title-18">My Applications</span>
            </a>
            <span class="tooltip">My Applications</span>
        </li>
		<li>
      <a href="{{url('check_clearance')}}" class="list {{ request()->is('check_clearance*') ? 'active' : '' }}">
                <i class='bx bx-file'></i>
                <span class="link-name title-18">Check Clearance</span>
            </a>
            <span class="tooltip">Check Clearance</span>
        </li>

		<li>
      <a href="{{url('destuffing')}}" class="list {{ request()->is('destuffing*') ? 'active' : '' }}">
                <i class='bx bxs-ship'></i>
                <span class="link-name title-18">Destuffing</span>
            </a>
            <span class="tooltip">Destuffing</span>
        </li>

    </ul>
    <div class="profile-content">
        <div class="profile">
            <div class="profile-details">
                <i class='bx bx-user' ></i>
                <div class="name-job">
                    <div class="name">{{ \Illuminate\Support\Facades\Auth::user()->first_name.' '.\Illuminate\Support\Facades\Auth::user()->last_name }}</div>
                    <div class="job">{{ \Illuminate\Support\Facades\Auth::user()->username }}</div>
                </div>
            </div>
            <a href="{{ route('logout') }}" style="color: white"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class='bx bx-power-off' id="logout"></i>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

</div>
@endif	

<div class="home-content" id="app">
<v-app style="background-color: transparent;">
    @yield('content')
</v-app>
</div>

<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
<script src="https://unpkg.com/moment@2.26.0/moment.js"></script>
<script>
    let btnMenu = document.querySelector('#btn-menu');
    let sidebar = document.querySelector('.master-sidebar');

    btnMenu.onclick = function () {
        sidebar.classList.toggle('active');
    }
</script>
<script type="text/javascript" src="{{ URL::to('/js/mdb/mdb.min.js') }}"></script>

</body>
</html>
