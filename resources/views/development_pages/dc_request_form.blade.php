@extends('components.master')
@section('title')
    Request Form
@endsection
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>


    <div class="main-body">
        <div class="row gutters-sm">
            {{-- <div class="col-md-4 mb-3">
                <a href="#" class="card" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">System Configuration</h4>
                    <p class="mb-0">Your personal system preferences</p>
                </a>
                <a href="{{ url('password-settings') }}" class="card mt-3" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">Password & Security</h4>
                    <p class="mb-0">Details about your account security</p>
                </a>
                <a href="{{ url('audit-logs') }}" class="card mt-3" style="padding: 10px 20px; border: 2px solid #20778b">
                    <h4 class="mb-0">Audit Logs</h4>
                    <p class="mb-0">Details about user activities/actions</p>
                </a>
            </div> --}}
            <div class="col-md-10">
                <div class="row gutters-sm">
                    <div class="mb-3">
                        <!--<div class="card mb-3" style="padding: 10px 20px;">
                            <div class="row mb-1">
                                <div class="col-md-2">
                                    <img src="{{ URL::to('/images/profile/user_profile.png') }}" alt="Admin" class="rounded-circle" width="70">
                                </div>
                                <div class="col text-start">
                                    <h5 class="mb-0">Upload a new profile photo</h5>
                                    <p class="mb-0">profile_pic_name.jpg</p>
                                </div>
                                <div class="col">
                                    <button class="btn btn-outline-dark btn-lg px-5" type="button">Update photo</button>
                                </div>
                            </div>
                        </div>-->
                        <div class="card">
                            <div class="card-body">
                                @include('components.alert')
                                <form method="POST" action="{{ route('user.update') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <h4 class="align-items-center mb-3">Maelezo ya Muombaji</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h5 class="align-items-center mb-3">Taarifa Binafsi</h5>
                                        <div class="col-md-4 col-xs-1">
                                            <div class="form-outline">
                                                <input type="text" id="typeFName" class="form-control form-control-lg"
                                                   name="first_name" value="{{old('first_name')}}" required autocomplete="first_name"/>
                                                <label class="form-label" for="typeFName">Jina la Kwanza *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('first_name') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-1">
                                            <div class="form-outline">
                                                <input type="text" id="typeMName" class="form-control form-control-lg"
                                                 name="middle_name" value="{{old('first_name')}}" required autocomplete="middle_name"/>
                                                <label class="form-label" for="typeMName">Jina la Kati*</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('middle_name') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-1">
                                            <div class="form-outline">
                                                <input type="text" id="typeLName" class="form-control form-control-lg"
                                                    name="last_name" value="{{old('first_name')}}" required autocomplete="last_name"/>
                                                <label class="form-label" for="typeLName">Last name *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('last_name') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                      
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typeBirthDate" class=" date form-control form-control-lg"
                                                    name="birth_date" value="{{old('birth_date')}}" required autocomplete="birth_date"/>
                                                <label class="form-label" for="birth_date">Tarehe ya Kuzaliwa *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('birth_date') {{ $message }} @enderror</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typeBirthPlace" class="form-control form-control-lg"
                                                    name="birth_place" value="{{old('birth_place')}}" required autocomplete="birth_place"/>
                                                <label class="form-label" for="birth_place">Mahali ulipo zaliwa *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('birth_place') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="email" id="typeEmailX" class="form-control form-control-lg
                                            @error('email') is-invalid @enderror" name="email"  required/>
                                                <label class="form-label" for="typeEmailX">Email</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('email') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typePhone" class="form-control form-control-lg
                                            @error('phone_number') is-invalid @enderror" maxlength="10" name="phone_number" required autocomplete="phone_number"/>
                                                <label class="form-label" for="typePhone">Namba ya Simu *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('phone_number') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12">
                                            <h5 class="align-items-center mb-3">Taarifa za Makazi</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-0">
                                        <div class="col-md-12">
                                            <div class="form-outline">
                                                <input type="text" id="typeSehemu" class="form-control form-control-lg"
                                                   name="sehemu" value="{{old('sehemu')}}" required autocomplete="sehemu"/>
                                                <label class="form-label" for="wilaya">Sehemu unayo ishi *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('sehemu') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typeShehina" class="form-control form-control-lg"
                                                   name="shehina" value="{{old('shehina')}}" required autocomplete="shehina"/>
                                                <label class="form-label" for="shehina">Shehina *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('shehina') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typeWilaya" class="form-control form-control-lg"
                                                   name="wilaya" value="{{old('wilaya')}}" required autocomplete="wilaya"/>
                                                <label class="form-label" for="wilaya">Wilaya *</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('wilaya') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typeHouseNumber" class="form-control form-control-lg"
                                                   name="house_number" value="{{old('house_number')}}" required autocomplete="house_number"/>
                                                <label class="form-label" for="house_number">Namba ya Nyumba</label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('house_number') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-outline">
                                                <input type="text" id="typePostAddress" class="form-control form-control-lg"
                                                   name="post_address" value="{{old('post_address')}}" required autocomplete="post_address"/>
                                                <label class="form-label" for="wilaya">Anuani ya Posta </label>
                                                <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('post_address') {{ $message }} @enderror</div>
                                            </div>
                                        </div>
                                    </div>
                                     

                                      <div class="row mt-5">
                                        <div class="col-12">
                                            <h5 class="align-items-center mb-3">Aina ya Baji</h5>
                                        </div>
                                    </div>

                                    {{-- CSS for toggling collapse functionality --}}
                                        <style>
                                            .form-group label> .description{
                                                display:none;
                                            }
                                            .form-group input[type="radio"]:checked ~ .description{
                                                display:block;
                                            }
                                        </style>
                                    {{-- CSS end --}}

                                    <div class="form-group">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>      
                                                    <input type="radio" name="is_driver"  value="1">
                      
                                                   Baji ya Dereva
                                                    <div class="mt-1 description">
                                                        Namba ya Leseni ya Udereva
                                                      <div class="form-outline">
                                                          <input type="text" id="typeLicenseNumber" class="form-control form-control-lg"
                                                             name="license_number" value="{{old('license_number')}}" required autocomplete="license_number"/>
                                                         
                                                          <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('post_address') {{ $message }} @enderror</div>
                                                      </div>
                                                    </div>
                                                </label>
                                                
                                            </div>
                                            <div class="col-md-6">
                                                <label>      
                                                    <input type="radio" name="is_driver"  value="0">
                                                
                                                    Baji ya Konda
                                                    <div class="mt-1 description">
                                                        Namba ya Kitambulisho cha Taifa
                                                      <div class="form-outline">
                                                          <input type="text" id="typeNationaIDNumber" class="form-control form-control-lg"
                                                             name="national_id_number" value="{{old('national_id_number')}}" required autocomplete="national_id_number"/>
                                                          
                                                          <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('national_id_number') {{ $message }} @enderror</div>
                                                      </div>
                                                    </div>
                                                  </label>
                                            </div>
                                        </div>
                                          
                                      
                                    
                                    {{-- <div>
                                        <button class="btn btn-outline-dark btn-lg px-5" type="submit">Update information</button>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $('.date').datepicker({  
           format: 'mm-dd-yyyy'
         });  
    </script> 
@endsection
