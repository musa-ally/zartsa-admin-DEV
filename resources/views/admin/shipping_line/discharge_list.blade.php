@extends('components.master')
@section('title')
    Voyages
@endsection
@section('content')
    @include('components.alert')
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
    @if(count($voyages) > 0)
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Vessel name</th>
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
                <td>{{ $voyage->vessels_name }}</td>
                <td>{{ $voyage->number }}</td>
                <td>{{ $voyage->estimated_arrival_date }}</td>
                <td>{{ $voyage->departure_date }}</td>
                @if($voyage->arrival_date == null)
                    <td><span class="badge rounded-pill bg-danger text-light">Not arrived</span></td>
                @else
                    <td><span class="badge rounded-pill bg-success text-light">Arrived</span></td>
                @endif
                <td>
                    <a href="{{ url('bol', [$voyage->id]) }}">
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
