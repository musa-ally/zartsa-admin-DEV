@extends('components.master')
@section('title')
    profile
@endsection
@section('content')
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ url()->previous() }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="row gutters-sm">
        @include('components.alert')
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="{{ URL::to('/images/profile/user_profile.png') }}" alt="Admin" class="rounded-circle" width="150">
                        <div class="mt-3">
                            @if(\Illuminate\Support\Facades\Auth::user()->is_approver === 1)
                            <div class="row">
                                <div class="col"><p class="text-green"><i class="fas fa-check-circle"></i> Approver</p></div>
                                <div class="col"><a href=""><span class="badge bg-danger">Remove</span></a></div>
                            </div>
                            @else
                                <a href=""><span class="badge bg-success mb-3">Make Approver</span></a>
                            @endif
                            <h4>{{ strtoupper($user->first_name) }} {{ strtoupper($user->last_name) }}</h4>
                            <p class="text-dark mb-1">Actions:</p>
                            @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_user'))
                                <a href="{{ url('user/edit', [\Illuminate\Support\Facades\Crypt::encrypt($user->id)]) }}"><button class="btn btn-warning"><i class="fas fa-pen"></i> &nbsp;Edit</button></a>
                            @endif
                            <button class="btn btn-success" data-mdb-toggle="modal" data-mdb-target="#userEditModal">
                                <i class="fas fa-envelope"></i> &nbsp;Email</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0" style="color: #0d688b"><i class="fas fa-user-alt"></i> &nbsp;Full name:</h6>
                        <span class="text-dark">{{ strtoupper($user->first_name) }} {{ strtoupper($user->last_name) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0" style="color: #0d688b"><i class="fas fa-user-alt"></i> &nbsp;Username: </h6>
                        <span class="text-dark">{{ $user->username }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0" style="color: #0d688b"><i class="fas fa-male"></i> &nbsp;Gender: </h6>
                        <span class="text-dark">{{ $user->gender }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0" style="color: #0d688b"><i class="fas fa-envelope"></i> &nbsp;Email address: </h6>
                        <span class="text-dark">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0" style="color: #0d688b"><i class="fas fa-phone"></i> &nbsp;Phone number: </h6>
                        <span class="text-dark">{{ $user->phone_number }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row gutters-sm">
                <div class="mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="d-flex align-items-center mb-3">User permissions</h6>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4 form-group">
                                        @if(in_array($permission->id, $u_permissions_filtered))
                                            <input type="checkbox" id="{{ $permission->id }}" name="{{ $permission->id }}" checked onchange="addRemovePermissionToUser(this.id)"> {{ $permission->display_name }}
                                        @else
                                            <input type="checkbox" id="{{ $permission->id }}" name="{{ $permission->id }}" onchange="addRemovePermissionToUser(this.id)"> {{ $permission->display_name }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal block/unblock service -->
    <div class="modal fade left" id="userEditModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
         aria-labelledby="userEditModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="userEditModal">Send Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('user.email') }}" class="needs-validation" novalidate>
                        @csrf
                        <input hidden type="text" name="email" value="{{ $user->email }}">
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="textAreaExample" rows="4" name="message" required></textarea>
                            <label class="form-label" for="textAreaExample">Start typing...</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Send Email</button>
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
        const user_permission_url = '{{ route('permission.user.add_remove') }}';
        const user_id = '{{ $user->id }}';
    </script>
@endsection
