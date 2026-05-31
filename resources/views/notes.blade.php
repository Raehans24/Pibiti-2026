@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <h2 class="text-3xl font-bold dark:text-white">
        Notes
    </h2>
    <p class="text-slate-500 mt-2 dark:text-white">
        Buat catatanmu lebih berwarna
    </p>
    <x-card>
        <h3 class="text-lg font-bold mb-6">Tambahkan catatan</h3>
        <div class="space-y-4">
            <div class="mb-4 ">
                <label class="block mb-2" for="title">Judul</label>
                <input type="text" name="title" class="w-full rounded-lg full border">
            </div>  
            <div class="mb-4 ">
                <label class="block mb-2" for="contenct">Catatan</label>
                <textarea rows="6" type="text" name="contenct" class="w-full rounded-lg full border">
                    </textarea>
            </div>  
        </div>
        <div class="flex justify-end">
            <x-button class="font-bold w-full">Simpan</x-button>
        </div>
    </x-card>
    <x-card>
        <h3 class="text-lg font-semibold mb-6">
            Upload Catatan
        </h3>
        <input type="file" class="flex w-full h-[100px] justify-center rounded-lg full border cursor-pointer">
        <p class="text-xs text-slate-500 mt-1">Upload Catatan yang ingin anda simpulkan
        </p>
    </x-card>
    <div class="">
        <h3 class="text-xl font-bold dark:text-white py-6">Catatan Saya</h3>
        <div class="space-y-4">
            <x-card>
                <h4 class="text-lg font-semibold">
                    Belajar Laravel
                </h4>
                <p class="text-slate-500 mt-1">
                    Saya sedang belajar laravel
                </p>
                <div class="flex gap-2 mt-4">
                    <x-button>
                        Simpulkan menggunakan Ai
                    </x-button>
                    <x-button class="bg-emerald-500">
                        Buatkan Quiz dengan Ai
                    </x-button>
                    <x-button class="bg-red-500">
                        Hapus
                    </x-button>
                </div>
            </x-card>
            <x-card>
                <h4 class="text-lg font-semibold">
                    Belajar Node.JS
                </h4>
                <p class="text-slate-500 mt-1">
                    Saya sedang belajar Node.JS 
                </p>
                <div class="flex gap-2 mt-4">
                    <x-button>
                        Simpulkan menggunakan Ai
                    </x-button>
                    <x-button class="bg-emerald-500">
                        Buatkan Quiz dengan Ai
                    </x-button>
                    <x-button class="bg-red-500">
                        Hapus
                    </x-button>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
