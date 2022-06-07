@extends('components.master')
@section('title')
    Tariffs
@endsection
@section('content')
    @include('components.alert')
    <div class="box-container">
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('creating_users'))
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('tariff.create') }}"><button class="btn btn-primary me-md-2" type="button">
                        Add New Tariff
                    </button>
                </a>
            </div>
        @endif
        <div class="table-header-container mt-2 mb-1">
            <div class="row">
                <div class="col">
                    <p>List of all Tariffs</p>
                </div>
                <div class="col input-group justify-content-md-end mb-2">
                    <div class="form-outline">
                        <input type="search" id="user_query" class="form-control"/>
                        <label class="form-label" for="form1">Search..</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchUser()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Category</th>
                <th scope="col">Amount</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="userBody">
            <?php $num = 1 ?>
            @foreach($tariffs as $tariff)
                <tr>
                    <th scope="row">{{ $num++ }}</th>
                    <td>{{ $tariff->username }}</td>
                    <td>{{ $tariff->id_types_id }}</td>
                    <td>
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_user_profile'))
                            <a href="{{ url('user', [\Illuminate\Support\Facades\Crypt::encrypt($tariff->id)]) }}">
                                <button type="button" class="btn btn-primary btn-sm px-3"
                                        data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View user">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </a>
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_user'))
                            <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-placement="bottom"
                                    data-mdb-toggle="modal" data-mdb-target="#serviceBlockModal" data-row-id="{{ $tariff->id }}"
                                    @if($tariff->account_status_code == 'AC001') title="Block Service" data-service-action="Block {{ $tariff->username }}"
                                    @else title="Unblock Service"  data-service-action="Unblock {{ $tariff->username }}"@endif>
                                <i class="fas @if($tariff->account_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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
                    <form method="POST" action="{{ route('user.block.toggle') }}" class="needs-validation" novalidate>
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
        const search_user_url = '{{ route('user.search') }}';
        const user_id = '{{ \Illuminate\Support\Facades\Auth::id() }}';
    </script>
@endsection
