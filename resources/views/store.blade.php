@extends('base.base')
@section('content')
    <h1>Store Page</h1>
    @foreach ($product_categories as $pc)
        <h2>{{ $pc->name }}</h2>
        <p>{{ $pc->description }}</p>
    @endforeach
@endsection