{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.adminapp')

@section('content')
    <div class="max-w-xl mx-auto py-8">
        {{-- Judul Halaman --}}
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ __('Edit Profil') }}
        </h2>
        <p class="mt-1 text-gray-600">
            {{ __('Perbarui informasi akun dan avatar-mu di sini.') }}
        </p>

        {{-- Form Update Profil --}}
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
            class="mt-6 space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('patch')

            {{-- Avatar Preview & Upload --}}
            <div>
                <label for="avatar" class="block text-sm font-medium text-gray-700">
                    {{ __('Avatar') }}
                </label>
                <div class="mt-2 flex items-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                            class="h-16 w-16 rounded-full object-cover border" />
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 border">
                            <span>?</span>
                        </div>
                    @endif
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                        class="ml-5 block w-full text-sm text-gray-500" />
                </div>
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    {{ __('Nama') }}
                </label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200" />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    {{ __('Email') }}
                </label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    {{ __('Simpan') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-green-600">
                        {{ __('Tersimpan.') }}
                    </p>
                @endif
            </div>
        </form>

        {{-- (Opsional) Hapus Akun --}}
        <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6"
            onsubmit="return confirm('Yakin ingin menghapus akun?');">
            @csrf
            @method('delete')
            <button type="submit" class="text-sm text-red-600 hover:underline">
                {{ __('Hapus Akun Saya') }}
            </button>
        </form>
    </div>
@endsection