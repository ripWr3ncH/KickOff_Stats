@extends('layouts.app')

@section('title', 'Reset Password - KickOff Stats')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="bg-primary w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-key text-white text-3xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-light">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-muted">
                Enter your new password below
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 dark:bg-red-900 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 dark:text-red-300"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email-display" class="block text-sm font-medium text-light mb-2">
                        Email Address
                    </label>
                    <input id="email-display" 
                           type="email" 
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-600 bg-gray-700 text-gray-400 sm:text-sm cursor-not-allowed" 
                           value="{{ $email }}"
                           disabled>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-light mb-2">
                        New Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-600 bg-card text-light placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" 
                           placeholder="Enter new password (min. 6 characters)">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-light mb-2">
                        Confirm New Password
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-600 bg-card text-light placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" 
                           placeholder="Confirm new password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-check text-white group-hover:text-white"></i>
                    </span>
                    Reset Password
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('home') }}" class="font-medium text-sm text-primary hover:text-green-400">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Home
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
