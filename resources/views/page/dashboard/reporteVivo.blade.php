@extends('layouts.template.master')
@section('page-style')
@endsection
@section('content')
    <dashboard-vivo-component
    :img-logo ="{{asset('images/logo/logo.png')}}"
    :ruta-reporte ="{{route('polito.voto.total')}}"
    ></dashboard-vivo-component>
@endsection
@section('page-script')
@endsection