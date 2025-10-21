@extends('layouts.app')

@section('title', 'Forgot Password - KickOff Stats')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="bg-primary w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-futbol text-white text-3xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-light">
                Forgot your password?
            </h2>
            <p class="mt-2 text-center text-sm text-muted">
                No problem. Just enter your email and we'll send you a password reset link.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 dark:bg-green-900 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 dark:text-green-300"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                        @if (session('dev_mode') && session('reset_url'))
                            <div class="mt-3 text-sm text-green-700 dark:text-green-300">
                                <p class="font-semibold mb-2">Development Mode - Click the link below:</p>
                                <a href="{{ session('reset_url') }}" class="text-blue-600 dark:text-blue-400 hover:underline break-all">
                                    {{ session('reset_url') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

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

        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-600 bg-card text-light placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" 
                           placeholder="Email address"
                           value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-paper-plane text-white group-hover:text-white"></i>
                    </span>
                    Send Password Reset Link
                </button>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <a href="{{ route('home') }}" class="font-medium text-primary hover:text-green-400">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Home
                    </a>
                </div>
                <div class="text-sm">
                    <a href="{{ route('home') }}#login" class="font-medium text-primary hover:text-green-400">
                        Remember password? Login
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
