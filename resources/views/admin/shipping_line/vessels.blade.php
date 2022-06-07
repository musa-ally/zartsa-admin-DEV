@extends('components.master')
@section('title')
    vessels
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ url('ship-lines') }}"><span class="badge bg-dark">Shipping line companies</span></a>
    </div>
    <div class="box-container">
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <button class="btn btn-primary" type="button"
                    data-mdb-toggle="modal"
                    data-mdb-target="#serviceModal1">Add new vessel</button>
        </div>
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of available Vessels</p>
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
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">vessel name</th>
                <th scope="col">vessel status</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php $num = 1 ?>
            @foreach($vessels as $vessel)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $vessel->name }}</td>
                    @if($vessel->service_status_id == 1)
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                    @endif
                    <td>
                        <button type="button" class="btn btn-primary btn-sm px-3"
                                data-vessel-name="{{ $vessel->name }}" data-vessel-id="{{ $vessel->id }}" title="View Voyages"
                                data-mdb-toggle="modal" data-mdb-target="#voyagesModal1">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit Vessel">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-sm px-3"
                                data-mdb-placement="bottom" data-vessel-name="{{ $vessel->name }}"
                                data-mdb-toggle="modal" data-mdb-target="#manifestModal1" title="Upload manifest">
                            <i class="fas fa-file-alt"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm px-3"
                                data-mdb-toggle="tooltip" data-mdb-placement="bottom"
                                title="@if($vessel->service_status_id == 1) Block @else UnBlock @endif Vessel">
                            <i class="fas @if($vessel->service_status_id == 1) fa-lock @else fa-unlock @endif"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal new vessel -->
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
                    <h5 class="modal-title" id="serviceModal1">Add a new vessel</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-mdb-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('vessel.create', [$code]) }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Vessel name *</label>
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

    <!-- Modal Upload manifest -->
    <div
        class="modal fade left" id="manifestModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="manifestModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="manifestModal1">Upload manifest file</h5>
                    <button
                        type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('manifest.upload', [$code]) }}" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        <input hidden type="text" name="vessel_name">
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('number')
                                is-invalid @enderror" name="number" value="{{ old('number') }}" required/>
                            <label class="form-label" for="typeRName">Voyage number *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('number') {{ $message }} @enderror</div>
                        </div>
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label" for="typeFName">Estimated date of arrival *</label>
                                <div class="form-outline">
                                    <input type="date" id="typeFName" class="form-control form-control-lg" name="ead"/>
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label" for="typeLName">Departure date *</label>
                                <div class="form-outline">
                                    <input type="date" id="typeLName" class="form-control form-control-lg" name="dod"/>
                                </div>
                            </div>
                        </div>
                        <label class="form-label" for="customFile">Choose cargo spreadsheet to upload</label>
                        <input type="file" class="form-control mb-4" name="xlsFile" id="customFile"  accept=".xls, .xlsx" required/>
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

    <!-- Modal View manifest -->
    <div class="modal fade left" id="voyagesModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="voyagesModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="voyagesModal1">Select voyage & BOL</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input hidden type="text" name="vessel_name">
                    <div class="row">
                        <div class="col" id="voyagesDiv">
                            <a class="nav-link dropdown-toggle hidden-arrow btn btn-primary" href="#"
                               id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown"
                               aria-expanded="false"> Choose Voyage...
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <div class="input-group mt-2 mx-2">
                                        <div class="form-outline">
                                            <input type="search" id="voyage-search-input" class="form-control-dropdown" />
                                            <label class="form-label" for="search-input-dropdown">Search</label>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"/></li>
                                <div id="voyageOptionItems"></div>
                            </ul>
                        </div>
                        <div class="col">
                            <a class="nav-link dropdown-toggle hidden-arrow btn btn-primary" href="#"
                               id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown"
                               aria-expanded="false"> Choose Bill of lading...
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <div class="input-group mt-2 mx-2">
                                        <div class="form-outline">
                                            <input type="search" id="search-input-dropdown" class="form-control-dropdown" />
                                            <label class="form-label" for="search-input-dropdown">Search</label>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"/></li>
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
