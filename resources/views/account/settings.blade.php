@extends('components.master')
@section('title')
    Settings
@endsection
@section('content')
    <div class="main-body">
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <a href="#" class="card" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">System Configuration</h4>
                    <p class="mb-0">Your personal system preferences</p>
                </a>
                <a href="{{ url('password-settings') }}" class="card mt-3" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">Password & Security</h4>
                    <p class="mb-0">Details about your account security</p>
                </a>
                <a href="{{ url('audit-logs') }}" class="card mt-3" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">Audit Logs</h4>
                    <p class="mb-0">Details about user activities/actions</p>
                </a>
            </div>
            <div class="col-md-8">
                <div class="row gutters-sm">
                    <div class="mb-3">
<!--                        <div class="card mb-3" style="padding: 10px 20px;">
                            <div class="row mb-1">
                                <div class="col-md-2">
                                    <img src="{{ URL::to('/images/profile/user_profile.png') }}" alt="Admin" class="rounded-circle" width="70">
                                </div>
                                <div class="col text-start">
                                    <h5 class="mb-0">Upload a new profile photo</h5>
                                    <p class="mb-0">profile_pic_name.jpg</p>
                                </div>
                                <div class="col">
                                    <button class="btn btn-outline-dark btn-lg px-5" type="button">Update photo</button>
                                </div>
                            </div>
                        </div>-->
                        <div class="card">
                            <div class="card-body">
                                @include('components.alert')
                                <h4 class="d-flex align-items-center mb-3">Change user information here</h4>
                                <form method="POST" action="{{ route('user.update') }}">
                                    @csrf
                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="form-outline">
                                                <input type="text" id="typeFName" class="form-control form-control-lg
                                            @error('first_name') is-invalid @enderror" name="first_name" value="{{ Auth::user()->first_name }}" required autocomplete="first_name"/>
                                                <label class="form-label" for="typeFName">First name *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('first_name') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-outline">
                                                <input type="text" id="typeLName" class="form-control form-control-lg
                                            @error('last_name') is-invalid @enderror" name="last_name" value="{{ Auth::user()->last_name }}" required autocomplete="last_name"/>
                                                <label class="form-label" for="typeLName">Last name *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('last_name') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-outline">
                                                <input type="text" id="typePhone" class="form-control form-control-lg
                                            @error('username') is-invalid @enderror" name="username" value="{{ Auth::user()->username }}" required autocomplete="username"/>
                                                <label class="form-label" for="typePhone">Username *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('username') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="form-outline">
                                                <input type="email" id="typeEmailX" class="form-control form-control-lg
                                            @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email }}" required/>
                                                <label class="form-label" for="typeEmailX">Email</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('email') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-outline">
                                                <input type="text" id="typePhone" class="form-control form-control-lg
                                            @error('phone_number') is-invalid @enderror" maxlength="10" name="phone_number" value="{{ Auth::user()->phone_number }}" required autocomplete="phone_number"/>
                                                <label class="form-label" for="typePhone">Phone number *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('phone_number') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col form-group">
                                            <select name="gender" id="gender" class="form-control" required>
                                                <option value="">Choose gender...</option>
                                                <option value="M" @if(Auth::user()->gender == 'M') selected @endif>Male</option>
                                                <option value="F" @if(Auth::user()->gender == 'F') selected @endif>Female</option>
                                            </select>
                                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('gender') {{ $message }} @enderror</div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-outline-dark btn-lg px-5" type="submit">Update information</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
