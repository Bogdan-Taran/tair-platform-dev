@extends('layouts.app')
@section('main-content')<main class="main account-form">
@include('profile.partials.update-profile-information-form')
@include('profile.partials.update-password-form')
@include('profile.partials.delete-user-form')
</main>@endsection
