
@extends("layouts.master")

@section("title", "Inserción de items")

@section("header", "Inserción de items")

@section("content")
    @isset($item)
        <form action="{{ route('items.update', ['item' => $item->id]) }}" method="POST">
        @method("PUT")
    @else
        <form action="{{ route('items.store') }}" method="POST">
    @endisset
        @csrf
        Nombre de la etiqueta:<input type="text" name="name" value="{{$item->name ?? '' }}"><br>
        <input type="submit">
        </form>
@endsection