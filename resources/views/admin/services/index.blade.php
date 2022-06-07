@extends('components.master')
@section('title')
    services
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
{{--        <div class="d-grid gap-2 d-md-flex justify-content-md-start">--}}
{{--            <button class="btn btn-primary" type="button"--}}
{{--                    data-mdb-toggle="modal"--}}
{{--                    data-mdb-target="#serviceModal1">Add new service</button>--}}
{{--        </div>--}}
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of available Services</p>
                </div>
                <div class="col input-group justify-content-md-end mb-2">
                    <div class="form-outline">
                        <input type="search" id="service_query" class="form-control"/>
                        <label class="form-label" for="form1">Search by service name</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchServices()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Service name</th>
                <th scope="col">Service description</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="serviceBody">
            <?php $num = 1 ?>
            @foreach($services as $service)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->description }}</td>
                    @if($service->service_status_code == 'AC001')
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Not active</span></td>
                    @endif
                    <td>
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_service'))
                            <button type="button" class="btn btn-warning btn-sm px-3" data-mdb-toggle="modal"
                                    onclick="setService({{ $service->id }})"
                                    data-mdb-target="#serviceEditModal" data-mdb-placement="bottom" title="Edit Service"
                                    data-service-id="{{ $service->id }}"
                                    data-service-name="{{ $service->name }}" data-service-desc="{{ $service->description }}">
                                <i class="fas fa-pen"></i>
                            </button>
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_services'))
                            <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-placement="bottom"
                                    data-mdb-toggle="modal" data-mdb-target="#serviceBlockModal" data-row-id="{{ $service->id }}"
                                    @if($service->service_status_code == 'AC001') title="Block Service" data-service-action="Block {{ $service->name }}"
                                    @else title="Unblock Service"  data-service-action="Unblock {{ $service->name }}"@endif>
                                <i class="fas @if($service->service_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                            </button>
                         @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal new service -->
    <div class="modal fade left" id="serviceModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="serviceModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceModal1">Add a new port service</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-mdb-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Service name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                is-invalid @enderror" name="description" value="{{ old('description') }}" required/>
                            <label class="form-label" for="typeRDescription">Service description *</label>
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

    <!-- Modal edit service -->
    <div class="modal fade left" id="serviceEditModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="serviceEditModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceEditModal">Edit service</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('service.edit') }}" class="needs-validation" novalidate>
                        @csrf
                        <div id="image-div" class="mb-4">
                        </div>
                        <input type="hidden" id="service_id" name="service_id"/>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                            <label class="form-label" for="typeRName">Service name *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="typeRDescription" rows="4" name="description" required></textarea>
                            <label class="form-label" for="textAreaExample">Service description *</label>
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

    <!-- Modal block/unblock service -->
    <div class="modal fade left" id="serviceBlockModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="serviceBlockModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceBlockModal"></h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('service.block.toggle') }}" class="needs-validation" novalidate>
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
        var search_services_url = '{{ route('service.search') }}';
    </script>
@endsection

@section('page-script')

    <script>
        function setService(service_id){
            $("#service_id").val(service_id)
        }
    </script>
@endsection
