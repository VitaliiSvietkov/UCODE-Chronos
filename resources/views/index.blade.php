@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="/css/style.css">
@endsection

@section('body')
    <div class="d-flex flex-column p-0 container-fluid" style="height: 90vh;">
        <div id="header" class="row p-0 m-0"></div>
        <div id="CalendarSidebar" class="FuckingSidebar"></div>
        <div id="Calendar" class="container-fluid pt-2" style="height: 100%"></div>
    </div>
@endsection
