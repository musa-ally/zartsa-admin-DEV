@extends('components.master')
@section('title')
    Bill of ladings
@endsection
@section('content')
    @include('components.alert')
    <div class="d-md-flex justify-content-md-start">
        <a href="{{ url('discharge-list') }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="table-header-container mt-2 mb-1">
        <div class="row">
            <div class="col">
                <p>List of available Bill of ladings</p>
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
    @if(count($bols) > 0)
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">BOL number</th>
            <th scope="col">Consignee</th>
            <th scope="col">Notify</th>
            <th scope="col">Port of lading</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $num = 1 ?>
        @foreach($bols as $bol)
            <tr>
                <th scope="row">{{ $num++ }}</th>
                <td>{{ $bol->number }}</td>
                <td>{{ $bol->consignee }}</td>
                <td>{{ $bol->notify }}</td>
                <td>{{ $bol->port_of_lading }}</td>
                <td>
                    <a href="{{ url('cargo', [$voyageId, $bol->number]) }}">
                        <button type="button" class="btn btn-primary btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View Cargo">
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
