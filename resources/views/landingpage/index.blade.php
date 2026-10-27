@extends('layouts.app')

@section('content')

    @include('landingpage.partials.navbar')

    @include('landingpage.components.hero')

    @include('landingpage.components.clients')

    @include('landingpage.components.services')

    @include('landingpage.components.monitoring')

    @include('landingpage.components.advantages')

    @include('landingpage.components.about')

    @include('landingpage.components.cta')

    @include('landingpage.partials.footer')

@endsection
