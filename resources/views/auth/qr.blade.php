@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Enable 2FA via Google Authenticator app</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('verify-token') }}">
                            @csrf
                            <p>Scan this barcode with your Google Authenticator App: </p>
                            <img src="{{$qrUrl}}" alt="">
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

                            <input type="hidden" name="two-factor-type" value="{{$twoFactorType}}">

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
        </div>
    </div>
@endsection
