@extends('components.master')
@section('title')
    Manifest
@endsection
@section('content')
    @include('components.alert')
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ url('discharge-list') }}"><span class="badge bg-dark">Discharge list</span></a>
        <a href="{{ url()->previous() }}"><span class="badge bg-success">Bill of lading</span></a>
    </div>
    <div class="table-header-container mt-2 mb-1">
        <div class="row">
            <div class="col">
                <p>List of available Cargo</p>
            </div>
            <div class="col input-group justify-content-md-end mb-2">
                <div class="form-outline">
                    <input type="search" id="form1" class="form-control" style="color: white"/>
                    <label class="form-label" for="form1">Search</label>
                </div>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    @if(count($cargos) > 0)
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Cargo number</th>
            <th scope="col">Weight (Kg)</th>
            <th scope="col">Cargo type</th>
            <th scope="col">Container size</th>
            <th scope="col">Remarks</th>
        </tr>
        </thead>
        <tbody>
        <?php $num = 1 ?>
        @foreach($cargos as $cargo)
            <tr>
                <th scope="row">{{ $num++ }}</th>
                <td>{{ $cargo->cargo_number }}</td>
                <td>{{ $cargo->weight_kg }}</td>
                <td>{{ $cargo->cargo_type }}</td>
                <td>{{ $cargo->container_size }}</td>
                <td>{{ $cargo->remarks }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <div class="container">
            <lottie-player src="https://assets1.lottiefiles.com/packages/lf20_bbqpmpse.json" background="transparent"
                           speed="1"  style="width: 300px; height: 300px;" autoplay></lottie-player>
        </div>
        <div class="container">
            <h3>No Results found!</h3>
        </div>
    @endif
@endsection
