@extends('layouts.template.master')
@section('page-style')
@endsection
@section('content')
    <dashboard-vivo-component
    :img-logo ="'{{asset('images/logo/logo.png')}}'"
    :ruta-reporte ="'{{route('polito.voto.total')}}'"
    :reporte-en-vivo-totales ="'{{route('reporte.en.vivo.totales')}}'"
    :reporte-en-vivo-totales-votos ="'{{route('reporte.en.vivo.totales-votos')}}'"
    ></dashboard-vivo-component>
@endsection
@section('page-script')
@endsection