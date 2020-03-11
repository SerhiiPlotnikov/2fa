@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @if(!session()->has('qrCode'))
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Two factor settings</div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('settings-2fa') }}">
                                @method('PUT')
                                @csrf

                                <div class="form-group row">
                                    <div class="offset-4 col-md-6">
                                        <select
                                            id="two-factor-type"
                                            class="form-control @error('two_factor_type') is-invalid @enderror"
                                            name="two_factor_type"
                                        >
                                            @foreach($twoFactorTypes as $key=>$name)
                                                <option
                                                    value="{{$key}}"
                                                    {{old('two_factor_type') === $key ||
                                                    $user->hasTwoFactorType($key) ? 'selected="selected"':''}}
                                                >
                                                    {{$name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('two_factor_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group row">
                                    <label
                                        for="phone_number_dialling_code"
                                        class="col-md-4 col-form-label text-md-right"
                                    >
                                        Dialling code
                                    </label>
                                    <div class="col-md-6">
                                        <select
                                            id="phone_number_dialling_code"
                                            class="form-control @error('phone_number_dialling_code') is-invalid @enderror"
                                            name="phone_number_dialling_code"
                                        >
                                            <option value="">Select a dialling code</option>
                                            @foreach($diallingCodes as $code)

                                                <option
                                                    value="{{$code->id}}"
                                                    {{old('phone_number_dialling_code')===$code->id ||
                                                       $user->hasDiallingCode($code->id)? 'selected="selected"':''}}
                                                >
                                                    {{$code->name}} (+{{$code->dialling_code}})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('phone_number_dialling_code')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="phone_number" class="col-md-4 col-form-label text-md-right">Phone
                                        number</label>

                                    <div class="col-md-6">
                                        <input
                                            id="phone_number"
                                            type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number"
                                            value="{{ old('phone_number')?old('phone_number'):$user->getPhoneNumber()}}"
                                            autocomplete="phone number"
                                            autofocus
                                        >

                                        @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Enable 2FA via Google Authenticator app</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('post-token') }}">
                                @csrf
                                <p>Scan this barcode with your Google Authenticator App: </p>
                                <img src="{{session()->get('qrCode')}}" alt="">
                                <p>Enter the code to Enable 2FA</p>

                                <div class="form-group row">
                                    <label for="token" class="col-md-4 col-form-label text-md-right">Authenticator
                                        Code</label>

                                    <div class="col-md-6">
                                        <input
                                            id="token"
                                            type="text"
                                            class="form-control @error('token') is-invalid @enderror"
                                            name="token"
                                            value="{{ old('token') }}"
                                            required
                                            autocomplete="token"
                                            autofocus
                                        >
                                        @error('token')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <input type="hidden" name="two-factor-type" value="{{session()->get('twoFactorType')}}">

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Enable 2FA
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
