@extends('components.master')
@section('title')
    New user
@endsection
@section('content')
    @include('components.alert')
    <div class="d-grid d-md-flex justify-content-md-start">
        <a href="{{ url('users') }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="justify-content-md-start mt-2 mb-1">
        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
            @csrf
            <div class="card" style="padding: 20px">
                <h5>Create a new user</h5>
                <?php $info_body = 'Checked permission(s) below are related to a chosen role.<br>
                        You can check or uncheck the permissions manually without relying on a role.' ?>
                @include('components.info')
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeFName" class="form-control form-control-lg @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name"/>
                            <label class="form-label" for="typeFName">First name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('first_name') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeLName" class="form-control form-control-lg @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name"/>
                            <label class="form-label" for="typeLName">Last name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('last_name') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typePhone" class="form-control form-control-lg @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username"/>
                            <label class="form-label" for="typePhone">Username *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('username') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="email" id="typeEmailX" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required/>
                            <label class="form-label" for="typeEmailX">Email *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('email') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typePhone" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" maxlength="10" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number"/>
                            <label class="form-label" for="typePhone">Phone number *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('phone_number') {{ $message }} @enderror</div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="form-group col mb-4">
                        <select name="id_types_id" id="id_types_id" class="form-control format-select" required>
                            <option value="">Choose ID type...</option>
                            @foreach($id_types as $id_type)
                                <option value="{{ $id_type->id }}">{{ $id_type->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('id_types_id') {{ $message }} @enderror</div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeIdNo" class="form-control form-control-lg @error('id_number') is-invalid @enderror" name="id_number" value="{{ old('id_number') }}" required autocomplete="id_number"/>
                            <label class="form-label" for="typeIdNo">ID Number *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('id_number') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <select name="gender" id="gender" class="form-control format-select" required>
                            <option value="">Choose gender...</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('gender') {{ $message }} @enderror</div>
                    </div>
                    <div class="col form-group mb-4">
                        <select name="role" id="role" class="form-control format-select" required onchange="getPermissions()">
                            <option value="">Choose role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('role') {{ $message }} @enderror</div>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <h4>Permissions:</h4>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-3 form-group">
                                <input type="checkbox" name="{{ $permission->id }}"> {{ $permission->display_name }} &nbsp;
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <button class="btn btn-outline-dark btn-lg px-5" type="submit">Register user</button>
                </div>
            </div>
        </form>
    </div>

    <!-- MDB -->
    <script>
        (() => {
            'use strict';

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation');

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
    <script>
        const permissions_url = '{{ url('role/permissions') }}';
    </script>
@endsection
