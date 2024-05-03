@extends('layouts.template.master')
@section('page-style')
@endsection
@section('content')
    <nue-cent-vot-component :url-province="'{{route('province')}}'"
                            :url-distric="'{{route('district')}}'"
                            :url-departamento="'{{route('departamento')}}'"
                            :url-pais="'{{route('pais')}}'"
                            :url-maps="'{{route('maps')}}'"
                            :url-guardar="'{{route('guardarCentroCosto')}}'"
                            >
    </nue-cent-vot-component>
@endsection
@section('page-script')
@endsection