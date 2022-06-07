@extends('components.master')
@section('title')
    Users Identification
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('create_identification'))
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button class="btn btn-primary" type="button" data-mdb-toggle="modal"
                        data-mdb-target="#idModal1">Add new ID type</button>
            </div>
        @endif
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of available ID types</p>
                </div>
                <div class="col input-group justify-content-md-end mb-2">
                    <div class="form-outline">
                        <input type="search" id="id_types_query" class="form-control"/>
                        <label class="form-label" for="form1">Search by ID name</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchIdTypes()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID name</th>
                <th scope="col">ID description</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="idTypeBody">
            <?php $num = 1 ?>
            @foreach($ids as $id)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $id->name }}</td>
                    <td>{{ $id->description }}</td>
                    @if($id->service_status_code == 'AC001')
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                    @endif
                    <td>
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_identification'))
                            <button type="button" class="btn btn-warning btn-sm px-3" data-mdb-toggle="modal"
                                    data-mdb-target="#editIdModal1" data-mdb-placement="bottom" title="Edit ID"
                                    data-identification-id="{{ $id->id }}"
                                    data-id-name="{{ $id->name }}"
                                    data-id-description="{{ $id->description }}">
                                <i class="fas fa-pen"></i>
                            </button>
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_identification'))
                            <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-toggle="modal"
                                    data-mdb-placement="bottom" data-mdb-target="#idBlockModal" data-row-id="{{ $id->id }}"
                                    @if($id->service_status_code == 'AC001') title="Block role" data-service-action="Block {{ $id->name }}"
                                    @else title="Unblock role"  data-service-action="Unblock {{ $id->name }}"@endif>
                                <i class="fas @if($id->service_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal new role -->
    <div class="modal fade left" id="idModal1" data-mdb-backdrop="static" data-mdb-keyboard="false"
        tabindex="-1" aria-labelledby="idModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="idModal1">Add a new Identification type</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('identity.create') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">ID type name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                is-invalid @enderror" name="description" value="{{ old('description') }}" required/>
                            <label class="form-label" for="typeRDescription">ID type description *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('description') {{ $message }} @enderror</div>
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

    <!-- Modal edit permission -->
    <div class="modal fade left" id="editIdModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
         aria-labelledby="editIdModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editIdModal1">Edit Identification</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('identification.edit') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <input name="identification_id" hidden/>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Permission name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="typeRDescription" rows="4" name="description" required></textarea>
                            <label class="form-label" for="textAreaExample">Permission description *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('description') {{ $message }} @enderror</div>
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

    <!-- Modal block/unblock permission -->
    <div class="modal fade left" id="idBlockModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
         aria-labelledby="idBlockModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="idBlockModal"></h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('identification.block.toggle') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <input hidden type="text" name="row_id">
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="textAreaExample" rows="4" name="reason" required></textarea>
                            <label class="form-label" for="textAreaExample">Reason...</label>
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
        var search_id_types_url = '{{ route('identity.search') }}';
    </script>
@endsection
