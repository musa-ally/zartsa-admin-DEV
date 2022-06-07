@extends('components.master')
@section('title')
    users
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('creating_users'))
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('user.create') }}"><button class="btn btn-primary me-md-2" type="button">Create new user</button></a>
            </div>
        @endif
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of available users</p>
                </div>
                <div class="col input-group justify-content-md-end mb-2">
                    <div class="form-outline">
                        <input type="search" id="user_query" class="form-control"/>
                        <label class="form-label" for="form1">Search by username or full name</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchUser()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Full name</th>
                <th scope="col">Username</th>
                <th scope="col">ID type</th>
                <th scope="col">ID number number</th>
                <th scope="col">Phone number</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="userBody">
            <?php $num = 1 ?>
            @foreach($users as $user)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $user->first_name.' '.$user->last_name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->id_types_id }}</td>
                    <td>{{ $user->id_number }}</td>
                    <td>{{ $user->phone_number }}</td>
                    @if($user->account_status_code == 'AC001')
                    <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                    @endif
                    <td>
                        @if($user->id != \Illuminate\Support\Facades\Auth::id())
                            @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_user_profile'))
                                <a href="{{ url('user', [\Illuminate\Support\Facades\Crypt::encrypt($user->id)]) }}">
                                    <button type="button" class="btn btn-primary btn-sm px-3"
                                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View user">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </a>
                            @endif
                            @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_user'))
                                <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-placement="bottom"
                                        data-mdb-toggle="modal" data-mdb-target="#serviceBlockModal" data-row-id="{{ $user->id }}"
                                        @if($user->account_status_code == 'AC001') title="Block Service" data-service-action="Block {{ $user->username }}"
                                        @else title="Unblock Service"  data-service-action="Unblock {{ $user->username }}"@endif>
                                    <i class="fas @if($user->account_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal block/unblock service -->
    <div class="modal fade left" id="serviceBlockModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
         aria-labelledby="serviceBlockModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceBlockModal"></h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('user.block.toggle') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <input hidden type="text" name="row_id">
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="textAreaExample" rows="4" name="reason" required></textarea>
                            <label class="form-label" for="textAreaExample">Reason...</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-outline-primary" data-mdb-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const search_user_url = '{{ route('user.search') }}';
        const user_id = '{{ \Illuminate\Support\Facades\Auth::id() }}';
    </script>
@endsection


@section('page-script')
    <script>
        var _index = 1;
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
    </script>
@endsection
