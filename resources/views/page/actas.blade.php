@extends('layouts.template.master')
@section('page-style')
@endsection
@section('content')
{{-- {{Auth::user()->persona_id}} --}}
    <actas-component
    :user-id="{{Auth::user()->id}}"
    ></actas-component>
@endsection
@section('page-script')
@endsection