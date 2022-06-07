@extends('components.login_master')
@section('content')
    {{-- Login starts here --}}
    <section class="vh-100 gradient-custom" id="loginBox">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark-transparent text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            @include('components.alert')
                            <form method="POST" action="{{ route('2fa.check') }}" class="needs-validation" novalidate>
                                @csrf
                                <div class="mt-md-4 pb-5">

                                    <h5 class="fw-bold mb-4 text-uppercase">Two factor Auth</h5>

                                    <div class="form-outline form-white mb-4">
                                        <input type="text" id="typeUsername"
                                               class="form-control @error('token') is-invalid @enderror"
                                               name="token" value="{{ old('token') }}" required
                                               autocomplete="token" autofocus/>
                                        <label class="form-label" for="typeUsername">Enter OTP</label>
                                        <div class="invalid-feedback"
                                             style="white-space: nowrap;overflow: scroll">@error('token') {{ $message }} @enderror</div>
                                    </div>
                                    <p class="small text-start"><a class="text-white-50" href="{{ route('2fa.resend') }}">Resend token</a>
                                    </p>
                                    <button class="btn btn-outline-light btn-lg px-5" type="submit">Submit</button>
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
