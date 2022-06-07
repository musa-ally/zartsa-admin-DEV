@extends('components.master')
@section('title')
    shipping lines
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <button class="btn btn-primary" type="button"
                    data-mdb-toggle="modal"
                    data-mdb-target="#serviceModal1">Add new shipping line</button>
        </div>
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of available Shipping line companies</p>
                </div>
                <div class="col input-group justify-content-md-end mb-2">
                    <div class="form-outline">
                        <input type="search" id="shipping_line_query" class="form-control"/>
                        <label class="form-label" for="form1">Search</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchShippingLine()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Shipping line name</th>
                <th scope="col">Shipping line Code</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="shippingLineBody">
            <?php $num = 1 ?>
            @foreach($ships as $ship)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $ship->name }}</td>
                    <td>{{ $ship->code }}</td>
                    @if($ship->service_status_id == 1)
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                    @endif
                    <td>
                        <a href="{{ route('vessels.show', [$ship->code]) }}">
                            <button type="button" class="btn btn-primary btn-sm px-3"
                                    data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View Shipping line">
                                <i class="fas fa-eye"></i>
                            </button>
                        </a>
                        <button type="button" class="btn btn-warning btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit Shipping line">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom"
                                title="@if($ship->service_status_id == 1) Block @else UnBlock @endif Shipping line">
                            <i class="fas @if($ship->service_status_id == 1) fa-lock @else fa-unlock @endif"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal new role -->
    <div
        class="modal fade left"
        id="serviceModal1"
        data-mdb-backdrop="static"
        data-mdb-keyboard="false"
        tabindex="-1"
        aria-labelledby="serviceModal1"
        aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceModal1">Add a new shipping line</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-mdb-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('ship.create') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Shipping line name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-outline-primary" data-mdb-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        var search_shipping_lines_url = '{{ route('ship.search') }}';
    </script>
@endsection
