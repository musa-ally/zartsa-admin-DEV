<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Styles -->
	<link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <!-- MDB -->
    <link href="{{ URL::to('/css/mdb/mdb.min.css') }}" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />

    <!-- MS DROPDOWN -->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/ms_dropdown/dd.css') }}" />


    <link rel="stylesheet" href="{{ URL::to('/css/main.css') }}">
    <link rel="stylesheet" href="{{ URL::to('/css/style1.css') }}">
    <link rel="stylesheet" href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- Modernize js -->
    <script src="{{ URL::to('js/modernizr-3.6.0.min.js') }}"></script>

    <style type="text/css">
        .format-select{
            height: 45px !important;
        }
        /* Fixed sidenav, full height */
        .sidenav {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 20px;
        }
        /* Main content */
        .main {
            margin-left: 200px; /* Same as the width of the sidenav */
            font-size: 20px; /* Increased text to enable scrolling */
            padding: 0px 10px;
        }
        /* Dropdown container (hidden by default). Optional: add a lighter background color and some left padding to change the design of the dropdown content */
        .dropdown-container {
            display: none;
            background-color: #262626;
            padding-left: 8px;
        }
        /* Optional: Style the caret down icon */
        .fa-caret-down {
            float: right;
            padding-right: 8px;
        }
    </style>
</head>
<body  onload="@yield('function')">
@if(\Illuminate\Support\Facades\Auth::user()->hasRole('super_admin'))
<div class="master-sidebar active">
    <div class="logo-content">
        <div class="logo">
            <i class='bx bxs-bus'></i>
            <div class="logo-name">ZARTSA</div>
        </div>
        <i class='bx bx-menu' id="btn-menu"></i>
    </div>
    <ul class="nav-list">
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_dashboard'))
            <li>
                <a href="{{ url('home') }}" class="list {{ request()->is('home') ? 'active' : '' }}">
                    <i class='bx bx-anchor'></i>
                    <span class="link-name">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
        @endif
        {{--       @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_users_list'))--}}
            <li>
                <a href="{{ url('users') }}" class="list {{ request()->is('users') || request()->is('users/*') || request()->is('user/*') ? 'active' : '' }}">
                    <i class='bx bx-user'></i>
                    <span class="link-name">Manage Users</span>
                </a>
                <span class="tooltip">Manage Users</span>
            </li>
{{--        @endif--}}
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_roles_list') || \Illuminate\Support\Facades\Auth::user()->hasPermission('view_permission_list'))
            <li>
                <a href="{{ url('roles-permissions') }}" class="list {{ request()->is('roles-permissions') || request()->is('role/*') ? 'active' : '' }}">
                    <i class='bx bx-key'></i>
                    <span class="link-name">Roles & Permissions</span>
                </a>
                <span class="tooltip">Roles & Permissions</span>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_identification_list'))
            <li>
                <a href="{{ url('identifications') }}" class="list {{ request()->is('identifications') ? 'active' : '' }}">
                    <i class='bx bx-id-card' ></i>
                    <span class="link-name">User Identifications</span>
                </a>
                <span class="tooltip">User Identifications</span>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_list_of_services'))
        <li>
            <a href="{{ url('services') }}" class="list {{ request()->is('services') ? 'active' : '' }}">
                <i class='bx bx-help-circle'></i>
                <span class="link-name">Services</span>
            </a>
            <span class="tooltip">Services</span>
        </li>
        @endif
            <li>
                <a href="{{ url('charges') }}" class="list {{ request()->is('charges') ? 'active' : '' }}">
                    <i class='bx bx-registered'></i>
                    <span class="link-name">Charges Management</span>
                </a>
                <span class="tooltip">Charges Management</span>
            </li>
{{--            <li>--}}
{{--                <a href="#" class="list dropdown-btn">--}}
{{--                    <i class='bx bx-registered' ></i>--}}
{{--                    <span class="link-name">Tariffs Prices</span>--}}
{{--                    <i class="fa fa-caret-down"></i>--}}
{{--                </a>--}}
{{--                <div class="dropdown-container">--}}
{{--                    <a href="#">Vehicle Tariffs</a>--}}
{{--                    <a href="#">Cargo Tarrifs</a>--}}
{{--                </div>--}}
{{--            </li>--}}
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_list_of_services'))
{{--            <li>--}}
{{--                <a href="{{ route('tariff.index') }}" class="list {{ request()->is('tariffs') ? 'active' : '' }}">--}}
{{--                    <i class='bx bx-registered' ></i>--}}
{{--                    <span class="link-name">Tariffs</span>--}}
{{--                </a>--}}
{{--                <span class="tooltip">Tariffs</span>--}}
{{--            </li>--}}
        @endif

{{--        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_discharge_list'))--}}
            <li>
                <a href="{{ url('discharge-list') }}" class="list {{ request()->is('discharge-list') || request()->is('bol/*') || request()->is('cargo/*') ? 'active' : '' }}">
                    <i class='bx bxs-help-circle'></i>
                    <span class="link-name">Charges Formulas</span>
                </a>
                <span class="tooltip">Charges Formulas</span>
            </li>
{{--        @endif--}}
        <li>
            <a href="{{ url('settings') }}" class="list {{ request()->is('settings') || request()->is('password-settings') || request()->is('audit-logs') ? 'active' : '' }}">
                <i class='bx bx-cog'></i>
                <span class="link-name">Settings</span>
            </a>
            <span class="tooltip">Settings</span>
        </li>
{{--        <li>--}}
{{--            <a href="{{ url('help') }}" class="list {{ request()->is('help') ? 'active' : '' }}">--}}
{{--                <i class='bx bx-help-circle'></i>--}}
{{--                <span class="link-name">Help</span>--}}
{{--            </a>--}}
{{--            <span class="tooltip">Help</span>--}}
{{--        </li>--}}

        <li>
            <a href="{{ url('dc-request-form') }}" class="list {{ request()->is('dc-request-form') ? 'active' : '' }}">
                <i class='bx bx-help-circle'></i>
                <span class="link-name">DC request form</span>
            </a>
            <span class="tooltip">DC request form</span>
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
            <a href="{{ route('logout') }}" style="color: white" title="Logout"
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
    @yield('content')
</div>
<script src="https://code.jquery.com/jquery-3.6.0.slim.js"
integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY="
crossorigin="anonymous"></script>
<script>

    var _index = 1;
    function searchRole() {
        const query = document.getElementById("role_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_role_url,
            data: {body: query}
        })
            .done(function (msg) {
                const roles = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("roleBody");

                console.log(roles)

                $("#roleBody").empty();
                roles.forEach(role => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let display_name = row.insertCell(1);
                    display_name.innerHTML = role.display_name;
                    let description = row.insertCell(2);
                    description.innerHTML = role.description;
                    let status = row.insertCell(3);
                    if (role.service_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(4);
                    const actionButtons = '<button type="button" class="btn btn-primary btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                        '                        <i class="fas fa-eye"></i>\n' +
                        '                    </button>\n' +
                        '                    <button type="button" class="btn btn-warning btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit role">\n' +
                        '                        <i class="fas fa-pen"></i>\n' +
                        '                    </button>\n';

                    if (role.service_status_code == 'AC001'){
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                            '                        <i class="fas fa-lock"></i>\n' +
                            '                    </button>';
                    }else{
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                            '                        <i class="fas fa-unlock"></i>\n' +
                            '                    </button>';
                    }
                })
            })
    }

    function searchPermission() {
        const query = document.getElementById("permission_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_permission_url,
            data: {body: query}
        })
            .done(function (msg) {
                const permissions = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("permissionBody");

                console.log(permissions)

                $("#permissionBody").empty();
                permissions.forEach(permission => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let display_name = row.insertCell(1);
                    display_name.innerHTML = permission.display_name;
                    let description = row.insertCell(2);
                    description.innerHTML = permission.description;
                    let status = row.insertCell(3);
                    if (permission.service_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(4);
                    const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit role">\n' +
                        '                        <i class="fas fa-pen"></i>\n' +
                        '                    </button>\n';

                    if (permission.service_status_code == 'AC001'){
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                            '                        <i class="fas fa-lock"></i>\n' +
                            '                    </button>';
                    }else{
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                            '                        <i class="fas fa-unlock"></i>\n' +
                            '                    </button>';;
                    }
                })
            });
    }

    function searchIdTypes() {
        const query = document.getElementById("id_types_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_id_types_url,
            data: {body: query}
        })
            .done(function (msg) {
                const idTypes = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("idTypeBody");

                console.log(idTypes)

                $("#idTypeBody").empty();
                idTypes.forEach(idType => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let name = row.insertCell(1);
                    name.innerHTML = idType.name;
                    let description = row.insertCell(2);
                    description.innerHTML = idType.description;
                    let status = row.insertCell(3);
                    if (idType.service_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(4);
                    const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                        '                        <i class="fas fa-pen"></i>\n' +
                        '                    </button>\n';

                    if (idType.service_status_code == 'AC001'){
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                            '                        <i class="fas fa-lock"></i>\n' +
                            '                    </button>';
                    }else{
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                            '                        <i class="fas fa-unlock"></i>\n' +
                            '                    </button>';
                    }
                })
            });
    }

    function searchServices() {
        const query = document.getElementById("service_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_services_url,
            data: {body: query}
        })
            .done(function (msg) {
                const services = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("serviceBody");

                console.log(services)

                $("#serviceBody").empty();
                services.forEach(service => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let name = row.insertCell(1);
                    name.innerHTML = service.name;
                    let description = row.insertCell(2);
                    description.innerHTML = service.description;
                    let status = row.insertCell(3);
                    if (service.service_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(4);
                    const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                        '                        <i class="fas fa-pen"></i>\n' +
                        '                    </button>\n';

                    if (service.service_status_code == 'AC001'){
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                            '                        <i class="fas fa-lock"></i>\n' +
                            '                    </button>';
                    }else{
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                            '                        <i class="fas fa-unlock"></i>\n' +
                            '                    </button>';
                    }
                })
            });
    }

    function searchShippingLine() {
        const query = document.getElementById("shipping_line_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_shipping_lines_url,
            data: {body: query}
        })
            .done(function (msg) {
                const ships = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("shippingLineBody");

                console.log(ships)

                $("#shippingLineBody").empty();
                ships.forEach(ship => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let name = row.insertCell(1);
                    name.innerHTML = ship.name;
                    let description = row.insertCell(2);
                    description.innerHTML = ship.code;
                    let status = row.insertCell(3);
                    if (ship.service_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(4);
                    const actionButtons = '<a href="vessels/'+ship.code+'"><button type="button" class="btn btn-primary btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View Shipping line">\n' +
                        '                        <i class="fas fa-eye"></i>\n' +
                        '                    </button></a>\n' +
                        '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                        '                        <i class="fas fa-pen"></i>\n' +
                        '                    </button>\n';

                    if (ship.service_status_code == 'AC001'){
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                            '                        <i class="fas fa-lock"></i>\n' +
                            '                    </button>';
                    }else{
                        actions.innerHTML = actionButtons+
                            '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                            '                        <i class="fas fa-unlock"></i>\n' +
                            '                    </button>';;
                    }
                })
            });
    }

    function searchUser() {
        const query = document.getElementById("user_query").value;
        // console.log(query);
        $.ajax({
            method: 'GET',
            url: search_user_url,
            data: {body: query}
        })
            .done(function (msg) {
                const users = JSON.parse(JSON.stringify(msg['results']));
                const table = document.getElementById("userBody");

                console.log(users)

                $("#userBody").empty();
                users.forEach(user => {
                    let row = table.insertRow();
                    let number = row.insertCell(0);
                    number.innerHTML = _index++;
                    let name = row.insertCell(1);
                    name.innerHTML = user.first_name+' '+user.last_name;
                    let username = row.insertCell(2);
                    username.innerHTML = user.username;
                    let id_type = row.insertCell(3);
                    id_type.innerHTML = user.id_types_id;
                    let id_number = row.insertCell(4);
                    id_number.innerHTML = user.id_number;
                    let phone = row.insertCell(5);
                    phone.innerHTML = user.phone_number;
                    let status = row.insertCell(6);
                    if (user.account_status_code == 'AC001'){
                        status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                    }else{
                        status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                    }
                    let actions = row.insertCell(7);
                    if (user.id != user_id){
                        const actionButtons = '<a href="user/'+user.id+'"><button type="button" class="btn btn-primary btn-sm px-3"\n' +
                            '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                            '                        <i class="fas fa-eye"></i>\n' +
                            '                    </button><a/>\n';

                        if (user.service_status_code == 'AC001'){
                            actions.innerHTML = actionButtons+
                                '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                                '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                                '                        <i class="fas fa-lock"></i>\n' +
                                '                    </button>';
                        }else{
                            actions.innerHTML = actionButtons+
                                '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                                '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                                '                        <i class="fas fa-unlock"></i>\n' +
                                '                    </button>';
                        }
                    }
                })
            });
    }

    function addRemovePermission(permission_id) {
        const permissionElement = document.getElementById(permission_id);
        var action = 0
        if (permissionElement.checked){
            action = 1
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'POST',
            url: permission_url,
            data: {'action': action, 'role_id': roleId, 'permission_id': permission_id}
        }).done(function (msg) {
            const result = JSON.parse(JSON.stringify(msg['results']))
            if(result.status_code !== 300){
                permissionElement.checked = !permissionElement.checked;
            }
        })
    }

    function addRemovePermissionToUser(permission_id) {
        const permissionElement = document.getElementById(permission_id);
        let action = 0
        if (permissionElement.checked){
            action = 1
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'POST',
            url: user_permission_url,
            data: {'action': action, 'permission_id': permission_id, 'user_id': user_id}
        }).done(function (msg) {
            const result = JSON.parse(JSON.stringify(msg['results']))
            console.log(result)
            if(result.status_code !== 300){
                permissionElement.checked = !permissionElement.checked;

            }
        })
    }

    function addRemovePasswordPolicy(policy_name) {
        const policyElement = document.getElementById(policy_name);
        var action = 0
        if (policyElement.checked){
            action = 1
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'POST',
            url: policy_url,
            data: {'action': action, 'policy_name': policy_name}
        }).done(function (msg) {
            const result = JSON.parse(JSON.stringify(msg['results']))
            console.log(result)
            if(result.status_code !== 300){
                policyElement.checked = !policyElement.checked;
            }
        })
    }

    function switchCurrency(id) {
        const currency = document.getElementById(id);
        internationalNumberFormat = new Intl.NumberFormat('en-US')
        if (currency.checked){
            document.getElementById('switchCurrency').innerHTML = 'Switch to TZS'

            document.getElementById('dailyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(daily_usd)
            document.getElementById('weeklyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(weekly_usd)
            document.getElementById('monthlyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(monthly_usd)
            document.getElementById('yearlyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(yearly_usd)

            // document.getElementById('tblColDaily').innerHTML = 'Daily income (USD)'
            // document.getElementById('tblColWeekly').innerHTML = 'Weekly income (USD)'
            // document.getElementById('tblColMonthly').innerHTML = 'Monthly income (USD)'
            // document.getElementById('tblColYearly').innerHTML = 'Yearly income (USD)'
        }else{
            document.getElementById('switchCurrency').innerHTML = 'Switch to USD'

            document.getElementById('dailyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(daily_tzs)
            document.getElementById('weeklyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(weekly_tzs)
            document.getElementById('monthlyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(monthly_tzs)
            document.getElementById('yearlyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(yearly_tzs)

            // document.getElementById('tblColDaily').innerHTML = 'Daily income (TZS)'
            // document.getElementById('tblColWeekly').innerHTML = 'Weekly income (TZS)'
            // document.getElementById('tblColMonthly').innerHTML = 'Monthly income (TZS)'
            // document.getElementById('tblColYearly').innerHTML = 'Yearly income (TZS)'
        }
    }

    function getPermissions(){
        const selected_role = document.getElementById("role").value;
        $.ajax({
            method: 'GET',
            url: permissions_url,
            data: {body: selected_role}
        })
            .done(function (msg) {
                console.log(msg['permissions']);
                const obj = JSON.parse(JSON.stringify(msg['permissions']));

                for (let i = 0; i < obj.length; i++) {
                    const object = obj[i];
                }
            });
    }

    $('#serviceBlockModal').on('show.bs.modal', function (e) {

        //get data-id attribute of the clicked element
        var rowId = $(e.relatedTarget).data('row-id');
        var serviceAction = $(e.relatedTarget).data('service-action');

        //populate the textbox
        $(e.currentTarget).find('h5[id="serviceBlockModal"]').text(serviceAction);
        $(e.currentTarget).find('input[name="row_id"]').val(rowId);
    });

    $('#roleBlockModal').on('show.bs.modal', function (e) {

        //get data-id attribute of the clicked element
        var rowId = $(e.relatedTarget).data('row-id');
        var serviceAction = $(e.relatedTarget).data('service-action');

        //populate the textbox
        $(e.currentTarget).find('h5[id="roleBlockModal"]').text(serviceAction);
        $(e.currentTarget).find('input[name="row_id"]').val(rowId);
    });

    $('#permissionBlockModal').on('show.bs.modal', function (e) {

        //get data-id attribute of the clicked element
        var rowId = $(e.relatedTarget).data('row-id');
        var serviceAction = $(e.relatedTarget).data('service-action');

        //populate the textbox
        $(e.currentTarget).find('h5[id="permissionBlockModal"]').text(serviceAction);
        $(e.currentTarget).find('input[name="row_id"]').val(rowId);
    });

    $('#idBlockModal').on('show.bs.modal', function (e) {

        //get data-id attribute of the clicked element
        var rowId = $(e.relatedTarget).data('row-id');
        var serviceAction = $(e.relatedTarget).data('service-action');

        //populate the textbox
        $(e.currentTarget).find('h5[id="idBlockModal"]').text(serviceAction);
        $(e.currentTarget).find('input[name="row_id"]').val(rowId);
    });

    $('#serviceEditModal').on('show.bs.modal', function (e) {

        //get data-id attribute of the clicked element
        const serviceId = $(e.relatedTarget).data('service-id');
        const serviceName = $(e.relatedTarget).data('service-name');
        const serviceDesc = $(e.relatedTarget).data('service-desc');
        const serviceIcon = $(e.relatedTarget).data('service-icon');

        //populate the textbox
        $(e.currentTarget).find('input[name="service_id"]').val(serviceId);
        $(e.currentTarget).find('input[name="name"]').val(serviceName);
        $(e.currentTarget).find('textarea[name="description"]').val(serviceDesc);

        console.log(serviceIcon)
        let imageName = '/images/services/boat-with-containers.png';
        if (serviceIcon != null){
            imageName = '/images/services/'+serviceIcon;
        }
        document.getElementById('image-div').innerHTML = '<img src="'+imageName+'" alt="service image" width="50"> &nbsp;' +
            ' <select name="icon" is="ms-dropdown">\n' +
            '    <option value="" data-image="">Choose icon...</option>\n' +
            '    <option value="cargo.svg" data-image="/images/services/cargo.svg">Cargo</option>\n' +
            '    <option value="forklift.svg" data-image="/images/services/forklift.svg">Fork lift</option>\n' +
            '    <option value="warehouse.svg" data-image="/images/services/warehouse.svg">Ware house</option>\n' +
            '  </select>';
    });

    $('#manifestModal1').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        const vesselName = $(e.relatedTarget).data('vessel-name');
        $(e.currentTarget).find('input[name="vessel_name"]').val(vesselName);
    });

    $('#editRoleModal1').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        const roleId = $(e.relatedTarget).data('role-id');
        const roleName = $(e.relatedTarget).data('role-name');
        const roleDescription = $(e.relatedTarget).data('role-description');

        $(e.currentTarget).find('input[name="role_id"]').val(roleId);
        $(e.currentTarget).find('input[name="name"]').val(roleName);
        $(e.currentTarget).find('textarea[name="description"]').val(roleDescription);
    });

    $('#editPermissionModal1').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        const permissionId = $(e.relatedTarget).data('permission-id');
        const permissionName = $(e.relatedTarget).data('permission-name');
        const permissionDescription = $(e.relatedTarget).data('permission-description');

        $(e.currentTarget).find('input[name="permission_id"]').val(permissionId);
        $(e.currentTarget).find('input[name="name"]').val(permissionName);
        $(e.currentTarget).find('textarea[name="description"]').val(permissionDescription);
    });

    $('#editIdModal1').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        const id = $(e.relatedTarget).data('identification-id');
        const idName = $(e.relatedTarget).data('id-name');
        const idDescription = $(e.relatedTarget).data('id-description');

        $(e.currentTarget).find('input[name="identification_id"]').val(id);
        $(e.currentTarget).find('input[name="name"]').val(idName);
        $(e.currentTarget).find('textarea[name="description"]').val(idDescription);
    });

</script>
<script type="text/javascript" src="{{ asset('js/dashboard_chart.js') }}"></script>
<!-- Chart Js -->
<script src="{{ asset('js/Chart.min.js') }}"></script>

<!-- MS DROPDOWN -->
<script src="{{ asset('js/ms_dropdown/dd.min.js') }}"></script>

@yield('page-script')
<script>
    let btnMenu = document.querySelector('#btn-menu');
    let sidebar = document.querySelector('.master-sidebar');

    btnMenu.onclick = function () {
        sidebar.classList.toggle('active');
    }

    //* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
    var dropdown = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }
</script>
<script type="text/javascript" src="{{ URL::to('/js/mdb/mdb.min.js') }}"></script>
</body>
</html>
