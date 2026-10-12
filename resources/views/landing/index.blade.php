@extends('layouts.app')

@section('content')

    @include('components.navbar')

    @include('landing.hero')

    @include('landing.clients')

    @include('landing.services')

    @include('landing.monitoring')

    @include('landing.workflow')

    @include('landing.advantages')

    @include('landing.about')

    @include('landing.cta')

    @include('components.footer')

@endsection
