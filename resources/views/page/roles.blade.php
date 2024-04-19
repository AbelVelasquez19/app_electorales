@extends('layouts.template.master')
@section('page-style')
    <link rel="stylesheet" href="{{asset('libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('libs/bootstrap-select/bootstrap-select.css')}}" />
@endsection
@section('content')
    <roles-component></roles-component>
@endsection
@section('page-script')
    <script src="{{asset('libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('libs/select2/select2.js')}}"></script>
    <script src="{{asset('libs/select2/forms-selects.js')}}"></script>
@endsection