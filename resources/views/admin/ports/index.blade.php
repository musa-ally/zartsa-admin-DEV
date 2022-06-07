@extends('components.master')
@section('title')
    users
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <button class="btn btn-primary" type="button"
                    data-mdb-toggle="modal"
                    data-mdb-target="#serviceModal1">Add new port</button>
        </div>
        <table class="table table-striped table-hover caption-top">
            <caption>
                List of available Ports
            </caption>
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Port name</th>
                <th scope="col">Port Address</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $num = 1 ?>
            @foreach($ports as $port)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $port->name }}</td>
                    <td>{{ $port->address }}</td>
                    @if($port->service_status_id == 1)
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                    @endif
                    <td>
                        <button type="button" class="btn btn-warning btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit service">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom"
                                title="@if($port->service_status_id == 1) Block @else UnBlock @endif Service">
                            <i class="fas @if($port->service_status_id == 1) fa-lock @else fa-unlock @endif"></i>
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
                    <h5 class="modal-title" id="serviceModal1">Add a new port</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-mdb-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('port.create') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Port name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('address')
                                is-invalid @enderror" name="address" value="{{ old('address') }}" required/>
                            <label class="form-label" for="typeRDescription">Port address *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('address') {{ $message }} @enderror</div>
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
@endsection
