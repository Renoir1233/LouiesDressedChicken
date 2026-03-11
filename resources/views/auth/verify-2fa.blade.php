@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Two-Factor Authentication</h2>
            <p class="text-gray-600 mt-2">Enter the verification code sent to your email address</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('verify.2fa.verify') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Verification Code (6 digits)
                </label>
                <input 
                    type="text" 
                    name="code" 
                    id="code" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl letter-spacing font-mono"
                    placeholder="000000"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required
                    autofocus
                />
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
            >
                Verify Code
            </button>
        </form>

        <div class="mt-4 space-y-2">
            <form action="{{ route('verify.2fa.resend') }}" method="POST">
                @csrf
                <button 
                    type="submit" 
                    class="w-full text-blue-600 hover:text-blue-800 text-sm font-semibold py-2"
                >
                    Didn't receive a code? Resend
                </button>
            </form>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded border border-gray-200">
            <div class="flex items-start">
                <input 
                    type="checkbox" 
                    id="trust_device" 
                    name="trust_device" 
                    class="mt-1 mr-3"
                />
                <label for="trust_device" class="text-sm text-gray-600">
                    <span class="font-medium">Trust this device</span>
                    <span class="block text-xs text-gray-500">You won't need to enter a code on this device for the next 30 days</span>
                </label>
            </div>

            @if ($errors->has('trust_device'))
                <p class="text-red-500 text-sm mt-1">{{ $errors->first('trust_device') }}</p>
            @endif
        </div>

        <script>
            // Auto-format code input
            document.getElementById('code').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });

            // Handle trust device checkbox
            document.getElementById('trust_device').addEventListener('change', function() {
                if (this.checked) {
                    // Show confirmation or submit form with trust flag
                    document.getElementById('code').form.appendChild(
                        Object.assign(document.createElement('input'), {
                            type: 'hidden',
                            name: 'trust_device',
                            value: '1'
                        })
                    );
                }
            });
        </script>

        <style>
            .letter-spacing {
                letter-spacing: 0.5em;
            }
        </style>
    </div>
</div>
@endsection
