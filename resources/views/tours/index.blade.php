@extends('layouts.app')

@section('content')
    <h1>Туры</h1>
    <ul>
        @foreach($tours as $tour)
            <li>
                <h2>{{ $tour->name }}</h2>
                <p>Страна: {{ $tour->country }}</p>
                <p>Описание: {{ $tour->description }}</p>
                <p>Дата начала: {{ $tour->starting_date }}</p>
                <p>Дата окончания: {{ $tour->ending_date }}</p>
                <p>Цена: {{ $tour->price }}</p>
                <img src="{{ asset('/tour_images/' . $tour->img) }}" alt="Tour Image">
            </li>
        @endforeach
    </ul>
@endsection
