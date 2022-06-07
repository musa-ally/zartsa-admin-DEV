@extends('components.master')
@section('title')
    Password security
@endsection
@section('content')
    @include('components.alert')
    <div class="main-body">
        <div class="d-grid d-md-flex justify-content-md-start">
            <a href="{{ url('settings') }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
        </div>
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card" style="padding: 10px 20px;">
                    <h4 class="mb-4">Change password</h4>
                    <?php $info_body = 'Password must contain:<br>'.implode($requiredPolicy) ?>
                    @include('components.info')
                    <form method="POST" action="{{ route('update.password') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="password" id="typeOPass" class="form-control form-control-lg
                                            @error('old_pass') is-invalid @enderror" name="old_pass" required/>
                            <label class="form-label" for="typeOPass">Old password *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('old_pass') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="password" id="typePasswordX" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required/>
                            <label class="form-label" for="typePasswordX">Password *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('password') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-3">
                            <input type="password" id="typeRepeatPass" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required/>
                            <label class="form-label" for="typeRepeatPass">Repeat password *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('password_confirmation') {{ $message }} @enderror</div>
                        </div>
                        <div>
                            <button class="btn btn-outline-dark btn-lg px-5" type="submit">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
            @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('set_password_policy'))
                <div class="col-md-8">
                <div class="row gutters-sm">
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">Set password policy</h4>
                                <?php $info_body = 'Check or uncheck to allow or disallow password policy respectively.<br>To Change password length, hold and slide the slider' ?>
                                @include('components.info')
                                <div class="row mb-4">
                                    @foreach($policies as $policy)
                                        @if($policy->name != 'length')
                                            <div class="col">
                                                <div class="form-group">
                                                    <input type="checkbox" @if($policy->value == 1) checked @endif id="{{ $policy->name }}"
                                                    onchange="addRemovePasswordPolicy(this.id)"> {{ $policy->display_name }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="row mb-4">
                                    @foreach($policies as $policy)
                                        @if($policy->name == 'length')
                                            <div class="col">
                                                <label class="form-label" for="customRange2">{{ $policy->display_name }}</label>
                                                <div class="range">
                                                    <input type="range" class="form-range" min="4" max="16" id="customRange2" value="{{ $policy->value }}" />
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
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
        var policy_url = '{{ route('policy.add_remove') }}';
    </script>
@endsection
