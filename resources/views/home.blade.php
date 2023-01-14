@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-sm-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Dashboard') }}</h4>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ url('/home') }}" method="POST" enctype="multipart/form-data"> @csrf
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-12">
                                <div class="alert alert-success my-1">Dapat Upload hingga <b>100 MB</b></div>
                                <div class="alert alert-danger my-1">Pastikan <b>Nama Poktan </b> sudah terurut dengan Benar !</div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile" name="data">
                                    <label class="custom-file-label" for="customFile">Pilih file</label>
                                </div>
                                <div class="text-small">* File Excel (.xls)</div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
