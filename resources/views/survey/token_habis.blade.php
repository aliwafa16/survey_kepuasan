@extends('layout.frontend') @section('content') 
@php
    $language = 'indonesian'; // atau 'english', 'malaysia' sesuai pilihan user
@endphp



<style>
    body {
      background-color: grey;
    }
  
    /* Customize the slider track */
    input[type="range"] {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      width: 100%;
      height: 8px;
      background: #bdc0c6;
      /* Light gray background */
      border-radius: 9999px;
      transition: background 0.3s;
    }
  
    /* Customize the slider thumb */
    input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 20px;
      height: 20px;
      background: #adff5d;
      /* Tailwind green */
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s;
      box-shadow: 0 0 15px rgba(173, 255, 93, 0.8);
    }
  
    input[type="range"]::-moz-range-thumb {
      width: 20px;
      height: 20px;
      background: #adff5d;
      /* Tailwind green */
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s;
      box-shadow: 0 0 15px rgba(173, 255, 93, 0.8);
    }
  
    input[type="range"]::-ms-thumb {
      width: 20px;
      height: 20px;
      background: #adff5d;
      /* Tailwind green */
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s;
      box-shadow: 0 0 15px rgba(173, 255, 93, 0.8);
    }
  
    /* Change thumb color on hover */
    input[type="range"]:hover::-webkit-slider-thumb {
      background: #22c55e;
      /* Darker green */
    }
  
    input[type="range"]:hover::-moz-range-thumb {
      background: #22c55e;
      /* Darker green */
    }
  
    input[type="range"]:hover::-ms-thumb {
      background: #22c55e;
      /* Darker green */
    }
  </style>
  <div class="container p-6 mx-auto ">
    <main class="  h-auto mb-10">
      <div class="container mx-auto px-4 py-8 border-spacing-2">
        <img src="{{ asset('img/BANNER.png') }}" class="mb-5" alt="">
        <div class="container bg-[#7ea38d]/30 mx-auto px-16 py-8 border-2 border-[#7ea38d] rounded-[20px]">
          Token Habis
      </div>
  </div>
  </main>
  
  </div> @endsection