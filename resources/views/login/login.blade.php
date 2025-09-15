@extends('layout.frontend')
@section('content')
<div class="container mx-auto flex justify-center items-center h-screen">
    <form action="" class="w-75 shadow-lg py-4 px-4 rounded-lg">
        <div class="mb-3">
            <label for="" class="font-normal mb-2">Usermame</label>
            <input type="text" class="w-full border-slate-300 border py-2 px-1 text-center bg-slate-50 rounded-md">
        </div>
        <div class="mb-3">
            <label for="" class="font-normal mb-2">Password</label>
            <input type="password" class="w-full border-slate-300 border py-2 px-1 text-center bg-slate-50 rounded-md">
        </div>
        <button class="bg-blue-600 w-full rounded-lg font-semibold text-white py-2">Login</button>
    </form>
</div>
@endsection