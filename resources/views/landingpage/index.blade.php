@extends('layouts.app')

@section('content')

    @include('landingpage.partials.navbar')

    @include('landingpage.components.hero')

    @include('landingpage.components.klien')

    @include('landingpage.components.layanan')

    @include('landingpage.components.solusi')

    @include('landingpage.components.arsitektur')

    @include('landingpage.components.perusahaan')

    @include('landingpage.components.kontak')

    @include('landingpage.partials.footer')

@endsection
