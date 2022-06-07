@extends('components.master')
@section('title')
    error page
@endsection
@section('content')
    <div class="container">
        <lottie-player src="https://assets4.lottiefiles.com/private_files/lf30_tt9pmfn3.json" background="transparent"
                       speed="1" style="width: 300px; height: 300px;" loop autoplay></lottie-player>
    </div>
    <div class="container">
        <h5>@if($code == 401) UnAuthorized @else Not Allowed @endif</h5>

        <div class="container d-grid gap-2 d-md-flex justify-content-md-center">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-dark me-md-2" type="submit">Logout</button>
            </form>
        </div>
    </div>
@endsection
