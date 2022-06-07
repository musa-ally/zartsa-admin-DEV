<div class="box-container">
{{--    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('create_permission'))--}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary me-md-2"
                    data-mdb-toggle="modal"
                    data-mdb-target="#permissionModal1"
                    type="button">Create new permission</button>
        </div>
{{--    @endif--}}
    <div class="table-header-container mt-2 mb-1">
        <div class="row">
            <div class="col">
                <p>List of available permissions</p>
            </div>
            <div class="col input-group justify-content-md-end mb-2">
                <div class="form-outline">
                    <input type="search" id="permission_query" class="form-control" name="name"/>
                    <label class="form-label" for="form1">Search by permission name</label>
                </div>
                <button type="button" class="btn btn-primary" onclick="searchPermission()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Role name</th>
            <th scope="col">Role description</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody id="permissionBody">
        <?php $num = 1 ?>
        @foreach($permissions as $permission)
            <tr>
                <th scope="row">{{ $num++ }}</th>
                <td>{{ $permission->display_name }}</td>
                <td>{{ $permission->description }}</td>
                @if($permission->service_status_code == 'AC001')
                    <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                @else
                    <td><span class="badge rounded-pill bg-danger text-light">Blocked</span></td>
                @endif
                <td>
                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_permission'))
                        <button type="button" class="btn btn-warning btn-sm px-3"
                                data-mdb-toggle="modal" data-mdb-target="#editPermissionModal1" data-mdb-placement="bottom"
                                title="Edit permission" data-permission-id="{{ $permission->id }}"
                                data-permission-name="{{ $permission->display_name }}"
                                data-permission-description="{{ $permission->description }}">
                            <i class="fas fa-pen"></i>
                        </button>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_permission'))
                        <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-toggle="modal"
                                onclick="setValue({{ $permission->id }})"
                                data-mdb-placement="bottom"  data-mdb-target="#permissionBlockModal" data-row-id="{{ $permission->id }}"
                                @if($permission->service_status_code == 'AC001') title="Block role" data-service-action="Block {{ $permission->display_name }}"
                                @else title="Unblock role"  data-service-action="Unblock {{ $permission->display_name }}"@endif>
                            <i class="fas @if($permission->service_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Modal new permission -->
<div class="modal fade left" id="permissionModal1" data-mdb-backdrop="static" data-mdb-keyboard="false"
     tabindex="-1" aria-labelledby="permissionModal1" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="permissionModal1">Add a new permission</h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('permission.create') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="form-outline mb-4">
                        <input type="text" id="typePName" class="form-control form-control-lg @error('name')
                                is-invalid @enderror validate" name="name" value="{{ old('name') }}" required />
                        <label class="form-label" for="typePName">Permission name *</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                    </div>
                    <div class="form-outline">
                        <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                                is-invalid @enderror validate" name="description" value="{{ old('description') }}" required />
                        <label class="form-label" for="typeRDescription">Permission description *</label>
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
<div class="modal fade left" id="editPermissionModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
     aria-labelledby="editPermissionModal1" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="editPermissionModal1">Edit Permission</h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('permission.edit') }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <input name="permission_id" hidden/>
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
<div class="modal fade left" id="permissionBlockModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
     aria-labelledby="permissionBlockModal" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="permissionBlockModal"></h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('permission.block.toggle') }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <input hidden type="text" name="row_id" id="permission_row_id">
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
    var search_permission_url = '{{ route('permission.search') }}';

    function setValue(row_id){
        $("#permission_row_id").val(row_id)
    }
</script>
