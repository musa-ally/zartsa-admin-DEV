@extends('components.master')
@section('title')
    New Tariff
@endsection
@section('content')
    @include('components.alert')
    <div class="d-grid d-md-flex justify-content-md-start">
        <a href="{{ route('tariff.index') }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="justify-content-md-start mt-2 mb-1">
        <form method="POST" action="{{ route('tariff.store') }}" class="needs-validation" novalidate>
            @csrf
            <div class="card" style="padding: 20px">
                <h5>Add New Tariff</h5>
                <?php $info_body = 'Make sure the added tariff its exact as provided on Government Tariff Document.' ?>
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
                </div>
                <div class="row mb-4">
                    <div class="col">
                        <div class="form-outline">
                            <input type="text" id="typeIdNo" class="form-control form-control-lg @error('id_number') is-invalid @enderror" name="id_number" value="{{ old('id_number') }}" required autocomplete="id_number"/>
                            <label class="form-label" for="typeIdNo">Number Of Days*</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('id_number') {{ $message }} @enderror</div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <select name="gender" id="category" class="form-control format-select" required>
                            <option value="">Choose Category...</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('category') {{ $message }} @enderror</div>
                    </div>
                </div>
                <div>
                    <button class="btn btn-outline-dark btn-lg px-5" type="submit">Add Tariff</button>
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
