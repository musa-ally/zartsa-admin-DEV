@extends('components.login_master')
@section('content')
    {{-- Login starts here --}}
    <section class="vh-100 gradient-custom" id="loginBox">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark-transparent text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                                @csrf
                                <div class="mt-md-4 pb-5">

                                    <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                    <p class="text-white-50 mb-5">Please enter your username and password!</p>

                                    <div class="text-start">
                                        <label for="typeUsername">Username:</label>
                                    </div>
                                    
                                    <div class="form-outline form-white mb-4">
                                        
                                        <input type="text" id="typeUsername"
                                               class="form-control @error('username') is-invalid @enderror"
                                               name="username" value="{{ old('username') }}" required
                                               autocomplete="username" autofocus  onkeyup="getUsername()"/>
                                        {{-- <label class="form-label" for="typeUsername">Username</label> --}}
                                        {{-- <div class="invalid-feedback"
                                             style="white-space: nowrap;overflow: scroll">@error('username') {{ $message }} @enderror</div> --}}
                                    </div>

                                    <div class="text-start">
                                        <label  for="typePasswordX">Password:</label>
                                    </div>
                                    <div class="form-outline form-white mb-4">
                                        <input type="password" id="typePasswordX" class="form-control" name="password"
                                               required autocomplete="current-password"/>
                                        {{-- <label class="form-label" for="typePasswordX">Password</label> --}}
                                    </div>

<!--                                    <div class="form-outline form-white mb-4">
                                        <input type="text" id="captcha"
                                               class="form-control @error('captcha') is-invalid @enderror"
                                               name="captcha" value="{{ old('captcha') }}" required
                                               autocomplete="captcha" autofocus/>
                                        <label class="form-label" for="captcha">Captcha</label>
                                        <div class="invalid-feedback"
                                             style="white-space: nowrap;overflow: scroll">@error('captcha') {{ $message }} @enderror</div>
                                    </div>-->

                                    <p class="small text-start"><a class="text-white-50" href="{{ url('password/reset') }}">Forgot password?</a>
                                    </p>
<!--                                    <div class="form-outline form-white mb-4 captcha">
                                        <span>{!! captcha_img('flat') !!}</span>
                                        <button type="button" class="btn btn-success reload" id="reload">
                                            <i class='bx bx-refresh' style="font-size: 24px;"></i>
                                        </button>
                                    </div>-->
                                    <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Login ends here --}}
@endsection
