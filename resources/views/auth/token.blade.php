@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Two factor authentication</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('post-token') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="token" class="col-md-4 col-form-label text-md-right">Token</label>

                                <div class="col-md-6">
                                    <input
                                        id="token"
                                        type="text"
                                        class="form-control @error('token') is-invalid @enderror"
                                        name="token"
                                        value="{{ old('email') }}"
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

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Validate token
                                    </button>

                                    @if(request()->session()->get('authy.using_sms'))
                                        <hr>
                                        <p class="form-text">Token not arrived? <a href="{{route('resend')}}">Resend
                                                token</a></p>
                                    @endif

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
