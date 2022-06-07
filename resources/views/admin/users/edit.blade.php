@extends('components.master')
@section('title')
    Edit user
@endsection
@section('content')
    @include('components.alert')
    <div class="d-grid d-md-flex justify-content-md-start">
        <a href="{{ url('user', [$id]) }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="justify-content-md-start mt-2 mb-1">
        <form method="POST" action="{{ route('user.edit', [$id]) }}" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            <div class="card" style="padding: 20px">
                <h5>Edit user: {{ $user->username }}</h5>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeFName" class="form-control form-control-lg @error('first_name') is-invalid @enderror" name="first_name" value="{{ $user->first_name }}" required autocomplete="first_name"/>
                            <label class="form-label" for="typeFName">First name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('first_name') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeLName" class="form-control form-control-lg @error('last_name') is-invalid @enderror" name="last_name" value="{{ $user->last_name }}" required autocomplete="last_name"/>
                            <label class="form-label" for="typeLName">Last name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('last_name') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typePhone" class="form-control form-control-lg @error('username') is-invalid @enderror" name="username" value="{{ $user->username }}" required autocomplete="username"/>
                            <label class="form-label" for="typePhone">Username *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('username') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="email" id="typeEmailX" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required/>
                            <label class="form-label" for="typeEmailX">Email *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('email') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typePhone" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" maxlength="10" name="phone_number" value="{{ $user->phone_number }}" required autocomplete="phone_number"/>
                            <label class="form-label" for="typePhone">Phone number *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('phone_number') {{ $message }} @enderror</div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="form-group col mb-4">
                        <select name="id_types_id" id="id_types_id" class="form-control" required>
                            @foreach($id_types as $id_type)
                                @if($id_type->id == $user->id_types_id)
                                    <option value="{{ $id_type->id }}" selected>{{ $id_type->name }}</option>
                                @else
                                    <option value="{{ $id_type->id }}">{{ $id_type->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('id_types_id') {{ $message }} @enderror</div>
                    </div>
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeIdNo" class="form-control form-control-lg @error('id_number') is-invalid @enderror" name="id_number" value="{{ $user->id_number }}" required autocomplete="id_number"/>
                            <label class="form-label" for="typeIdNo">ID Number *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('id_number') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <select name="gender" id="gender" class="form-control" required>
                            @if($user->gender == 'M')
                                <option value="M" selected>Male</option>
                                <option value="F">Female</option>
                            @else
                                <option value="M">Male</option>
                                <option value="F" selected>Female</option>
                            @endif
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('gender') {{ $message }} @enderror</div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-outline-dark btn-lg px-5" type="submit">Update user</button>
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
@endsection
