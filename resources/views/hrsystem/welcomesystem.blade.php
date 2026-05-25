@extends('layouts.app')

@section('content')
<div class="container mx-auto justify-center text-center py-4">
    <h1 class="text-2xl font-bold mb-4">
        งานบริการ HA
    </h1>
    <div class="flex justify-center gap-12">
        <div class="card bg-base-100 w-1/4 shadow-sm border transition-transform duration-500 hover:scale-105">
            <figure>
                <img
                    src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                    alt="Shoes"
                    class="w-full h-48 object-cover" />
            </figure>
            <div class="card-body">
                <h2 class="card-title  justify-center">Request HR</h2>
                <p class="card-actions justify-center">การร้องขอเอกสารต่างๆ</p>
                <div class="card-actions justify-center">
                    <a href="{{ route('request.hr') }}" class="btn btn-soft btn-error w-full">Request HR</a>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 w-1/4 shadow-sm border transition-transform duration-500 hover:scale-105">
            <figure>
                <img
                    src="https://img.daisyui.com/images/stock/photo-1635805737707-575885ab0820.webp"
                    alt="Movie"
                    class="w-full h-48 object-cover" />
            </figure>
            <div class="card-body">
                <h2 class="card-title  justify-center">Manpower</h2>
                <p class="card-actions justify-center">
                    การจัดการทรัพยากรบุคคล
                </p>
                <div class="card-actions justify-center">
                    <button class="btn btn-soft btn-error w-full">Manpower</button>
                </div>
            </div>
        </div>

            <div class="card bg-base-100 w-1/4 shadow-sm border transition-transform duration-500 hover:scale-105">
            <figure>
                <img
                    src="#"
                    alt="Shoes"
                    class="w-full h-48 object-cover" />
            </figure>
            <div class="card-body">
                <h2 class="card-title  justify-center">จัดการข้อมูล HR</h2>
                <p class="card-actions justify-center">
                    จัดการข้อมูล HR
                </p>
                <div class="card-actions justify-center">
                    <a href="{{ route('request.data') }}" class="btn btn-soft btn-error w-full">Request HR</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-12 mt-4" >
        <!-- <div class="card bg-base-100 w-1/4 shadow-sm border transition-transform duration-500 hover:scale-105">
            <figure>
                <img
                    src="#"
                    alt="Shoes"
                    class="w-full h-48 object-cover" />
            </figure>
            <div class="card-body">
                <h2 class="card-title  justify-center">Request HR</h2>
                <p class="card-actions justify-center">การร้องขอเอกสารต่างๆ</p>
                <div class="card-actions justify-center">
                    <a href="{{ route('request.hr') }}" class="btn btn-soft btn-error w-full">Request HR</a>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 w-1/4 shadow-sm border transition-transform duration-500 hover:scale-105">
            <figure>
                <img
                    src="https://img.daisyui.com/images/stock/photo-1635805737707-575885ab0820.webp"
                    alt="Movie"
                    class="w-full h-48 object-cover" />
            </figure>
            <div class="card-body">
                <h2 class="card-title  justify-center">Manpower</h2>
                <p class="card-actions justify-center">
                    การจัดการทรัพยากรบุคคล
                </p>
                <div class="card-actions justify-center">
                    <button class="btn btn-soft btn-error w-full">Manpower</button>
                </div>
            </div>
        </div> -->
    
    </div>
</div>
@endsection