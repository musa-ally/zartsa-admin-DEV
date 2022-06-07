@extends('components.master')
@section('title')
    Audit logs
@endsection
@section('content')
    @include('components.alert')
    <div class="main-body">
        <div class="d-grid d-md-flex justify-content-md-start">
            <a href="{{ url('settings') }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
        </div>
        <div class="row gutters-sm mt-4">
            <!-- Tabs navs -->
            <ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="ex1-tab-1" data-mdb-toggle="tab" href="#ex1-tabs-1"
                        role="tab" aria-controls="ex1-tabs-1" aria-selected="true">System Logs</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="ex1-tab-2" data-mdb-toggle="tab" href="#ex1-tabs-2"
                        role="tab" aria-controls="ex1-tabs-2" aria-selected="false" >Login attempts</a>
                </li>
            </ul>
            <!-- Tabs navs -->

            <!-- Tabs content -->
            <div class="tab-content" id="ex1-content">
                <div class="tab-pane fade show active" id="ex1-tabs-1" role="tabpanel" aria-labelledby="ex1-tab-1">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User</th>
                            <th scope="col">Event type</th>
                            <th scope="col">Event description</th>
                            <th scope="col">Event Status</th>
                            <th scope="col">Event Date</th>
                        </tr>
                        </thead>
                        <tbody id="userBody">
                        <?php $num = 1 ?>
                        @foreach($logs as $log)
                            <tr>
                                <th scope="row">{{ $num++ }}</th>
                                <td>{{ $log['user']->username }}</td>
                                <td>{{ $log->event_type }}</td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->event_status }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="ex1-tabs-2" role="tabpanel" aria-labelledby="ex1-tab-2">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Email Address</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">Country</th>
                            <th scope="col">Event Status</th>
                            <th scope="col">Event Date</th>
                        </tr>
                        </thead>
                        <tbody id="userBody">
                        <?php $num_login = 1 ?>
                        @foreach($login_logs as $log)
                            <tr>
                                <th scope="row">{{ $num_login++ }}</th>
                                <td>{{ $log->email }}</td>
                                <td>{{ $log->ip }}</td>
                                <td>{{ $log->geoip_country }}</td>
                                <td>{{ $log->event }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
