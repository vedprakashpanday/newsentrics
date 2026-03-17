@extends('layouts.master') @section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1>Welcome to Newsentric Admin</h1>
        <p>Aapka portal setup ho chuka hai. Yahan se aap news manage karenge.</p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3">
                    <h4>Total News: 0</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3">
                    <h4>Total Visitors: 0</h4>
                </div>
            </div>
        </div>
    </div>
@endsection