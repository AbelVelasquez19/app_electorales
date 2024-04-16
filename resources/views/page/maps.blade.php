@extends('layouts.template.master')
@section('page-style')
@endsection
@section('content')
    <maps-component  :img-red="'{{ asset('images/vendor/leaflet/dist/marker-red.png') }}'" 
                     :img-blue="'{{ asset('images/vendor/leaflet/dist/marker-blue.png') }}'"
                     :img-yelow="'{{ asset('images/vendor/leaflet/dist/marker-yelow.png') }}'"></maps-component>
@endsection
@section('page-script')
@endsection