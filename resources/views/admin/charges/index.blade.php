@extends('components.master')
@section('title')
    Charges Management
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
{{--      @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('create_charges'))--}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <button class="btn btn-primary" type="button"
                    data-mdb-toggle="modal"
                    data-mdb-target="#serviceModal1">Add New Charge</button>
        </div>
{{--        @endif--}}
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of Added Service Charges</p>
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
                <th scope="col">Service Name</th>
                <th scope="col">Charge Amount</th>
                <th scope="col">Charge Discount</th>
                <th scope="col">Service VAT</th>
                <th scope="col">Grace Period</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="serviceBody">
            <?php $num = 1 ?>
            @foreach($charges as $charge)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $charge->serviceDetails->name }}</td>
                    <td>{{ $charge->service_charge }}</td>
                    <td>{{ $charge->service_charge_discount }}</td>
                    <td>{{ $charge->service_charge_vat }} %</td>
                    <td>{{ $charge->payment_grace_period }} day(s)</td>
                    @if($charge->service_approval == '1')
                        <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                    @else
                        <td><span class="badge rounded-pill bg-danger text-light">Not active</span></td>
                    @endif
                    <td>
{{--                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_charges'))--}}
                            <button type="button" class="btn btn-warning btn-sm px-3" data-mdb-toggle="modal"
                                    onclick="updateCharge({{ $charge }})"
                                    data-mdb-target="#serviceEditModal" data-mdb-placement="bottom" title="Edit Service"
                                    >
                                <i class="fas fa-pen"></i>
                            </button>
{{--                        @endif--}}
{{--                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_charges'))--}}
                            <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-placement="bottom"
                                    onclick="updateChargeStatus({{ $charge->id }})"
                                    data-mdb-toggle="modal" data-mdb-target="#serviceBlockModal" data-row-id="{{ $charge->id }}"
                                    @if($charge->service_approval == '1') title="Change Charge Status" data-service-action="Disable Charge"
                                    @else title="Change Charge Status"  data-service-action="Approve Charge"@endif>
                                <i class="fas @if($charge->service_approval == '1') fa-lock @else fa-unlock @endif"></i>
                            </button>
{{--                         @endif--}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal new Charge -->
    <div class="modal fade left" id="serviceModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="serviceModal1" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceModal1">Add new Service Charge</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-mdb-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('charge.store')}}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-group col mb-4">
                            <select name="service_id" id="service_id" class="form-control format-select" required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('service_id') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                                    is-invalid @enderror" name="service_charge"  required/>
                            <label class="form-label" for="typeRName">Service Charge Amount *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('service_charge') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="charge_discount" required/>
                            <label class="form-label" for="typeRDescription">Service Charge Discount *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('charge_discount') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="charge_vat" required/>
                            <label class="form-label" for="typeRDescription">Service Charge Vat *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('charge_vat') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="grace_period" required/>
                            <label class="form-label" for="typeRDescription">Grace Period (Days) *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('grace_period') {{ $message }} @enderror</div>
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

    <!-- Modal edit Charge -->
    <div class="modal fade left" id="serviceEditModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
        aria-labelledby="serviceEditModal" aria-hidden="true">
        <div class="modal-dialog modal-side modal-top-left">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="serviceEditModal">Edit Service Charge</h5>
                    <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('charge.edit')}}" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-group col mb-4">
                            <input type="hidden" id="charge_id" name="charge_id"/>
                            <select name="service_id" id="update_service_id" class="form-control format-select" required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('service_id') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="update_service_charge" class="form-control form-control-lg @error('name')
                                    is-invalid @enderror" name="service_charge" value="{{ old('service_charge') }}" required/>
                            <label class="form-label" for="typeRName">Service Charge Amount *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('service_charge') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="update_charge_discount" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="charge_discount" value="{{ old('charge_discount') }}" required/>
                            <label class="form-label" for="typeRDescription">Service Charge Discount *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('charge_discount') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="update_charge_vat" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="charge_vat" value="{{ old('charge_vat') }}" required/>
                            <label class="form-label" for="typeRDescription">Service Charge Vat *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('charge_vat') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" id="update_grace_period" class="form-control form-control-lg @error('description')
                                    is-invalid @enderror" name="grace_period" value="{{ old('grace_period') }}" required/>
                            <label class="form-label" for="typeRDescription">Grace Period (Days) *</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('grace_period') {{ $message }} @enderror</div>
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
                    <form method="POST" action="{{ route('charge.status_change') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('POST')
                        <input type="hidden" name="charge_id" id="update_charge_id">
                        <div class="form-group col mb-4">
                            <select name="status" id="update_status" class="form-control format-select" required>
                                <option value="1">Active</option>
                                <option value="0">In-Active</option>
                            </select>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('service_id') {{ $message }} @enderror</div>
                        </div>
                        <div class="form-outline mb-4">
                            <textarea class="form-control" id="textAreaExample" rows="4" name="status_reason" required></textarea>
                            <label class="form-label" for="textAreaExample">Reason...</label>
                            <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('status_reason') {{ $message }} @enderror</div>
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
        function updateChargeStatus(charge){
            $("#update_charge_id").val(charge);
        }

        function updateCharge(charge){
            $("#charge_id").val(JSON.stringify(charge['id']));
            $("#update_service_id").val(JSON.stringify(charge['service_id']));
            $("#update_service_charge").val(JSON.stringify(charge['service_charge']));
            $("#update_charge_discount").val(JSON.stringify(charge['service_charge_discount']));
            $("#update_charge_vat").val(JSON.stringify(charge['service_charge_vat']));
            $("#update_grace_period").val(JSON.stringify(charge['payment_grace_period']));
        }
    </script>
@endsection
