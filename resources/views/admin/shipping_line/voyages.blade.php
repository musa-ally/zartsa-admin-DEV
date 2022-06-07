@extends('components.master')
@section('title')
    Voyages
@endsection
@section('content')
    @include('components.alert')
    <div class="container d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ url('ship-lines') }}"><span class="badge bg-dark">Shipping line companies</span></a>
        <a href="{{ url('vessels', [$code]) }}"><span class="badge bg-warning">Vessels</span></a>
    </div>
    <div class="table-header-container mt-2 mb-1">
        <div class="row">
            <div class="col">
                <p>List of available Voyages</p>
            </div>
            <div class="col input-group justify-content-md-end mb-2">
                <div class="form-outline">
                    <input type="search" id="form1" class="form-control"/>
                    <label class="form-label" for="form1">Search</label>
                </div>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">voyage number</th>
            <th scope="col">Estimated arrival date</th>
            <th scope="col">Departure date</th>
            <th scope="col">Status</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $num = 1 ?>
        @foreach($voyages as $voyage)
            <tr>
                <th scope="row">{{ $num++ }}</th>
                <td>{{ $voyage->number }}</td>
                <td>{{ $voyage->estimated_arrival_date }}</td>
                <td>{{ $voyage->departure_date }}</td>
                @if($voyage->arrival_date == null)
                    <td><span class="badge rounded-pill bg-danger text-light">Not arrived</span></td>
                @else
                    <td><span class="badge rounded-pill bg-success text-light">Arrived</span></td>
                @endif
                <td>
                    <a href="{{ url('bol', [$code, $voyage->id]) }}">
                        <button type="button" class="btn btn-primary btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View BOL">
                            <i class="fas fa-eye"></i>
                        </button>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
