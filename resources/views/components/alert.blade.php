@if ($errors->any())
    <div class="alert alert-dismissible fade show ms-4" role="alert"
         style="background: orangered; color: white">
        <i class="fas fa-times-circle me-3"></i>{{ $errors->first() }}
        <button
            type="button"
            class="btn-close"
            data-mdb-dismiss="alert"
            aria-label="Close"></button>
    </div>
@endif
@if(\Illuminate\Support\Facades\Session::has("message"))
    <div class="alert alert-dismissible fade show ms-4" role="alert"
         @if(\Illuminate\Support\Facades\Session::get('error') == true)
         style="background: orangered; color: white"
         @else style="background: mediumseagreen; color: white" @endif>
        <i class="fas @if(\Illuminate\Support\Facades\Session::get('error') == true)
            fa-times-circle @else fa-check-circle @endif me-3"></i>{{ \Illuminate\Support\Facades\Session::get('message') }}
        <button
            type="button"
            class="btn-close"
            data-mdb-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>
@endif
