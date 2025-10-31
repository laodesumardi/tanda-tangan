<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Dokumen</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900">Verifikasi Dokumen</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ $document->title }}</p>
                </div>

                <div class="px-6 py-4">
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            @if($document->status === 'signed')
                                <svg class="h-8 w-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold text-green-700">Dokumen Tervalidasi</span>
                            @else
                                <svg class="h-8 w-8 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold text-yellow-700">Status: {{ ucfirst($document->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 mb-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Judul Dokumen</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->original_filename }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Tanda Tangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->signatures->count() }}</dd>
                        </div>
                    </dl>

                    <!-- QR Code Section -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code Verifikasi</h3>
                        <div class="flex flex-col items-center">
                            <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-gray-200">
                                @if($document->qr_code_path && Storage::disk('public')->exists($document->qr_code_path))
                                    <img src="{{ Storage::disk('public')->url($document->qr_code_path) }}" 
                                         alt="QR Code Verifikasi" 
                                         class="w-64 h-64 mx-auto"
                                         style="max-width: 256px; height: auto;">
                                @else
                                    <div class="w-64 h-64 mx-auto flex items-center justify-center bg-gray-100 rounded">
                                        <p class="text-sm text-gray-500">QR Code tidak tersedia</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 text-center max-w-md">
                                <p class="text-sm font-medium text-gray-700 mb-2">Scan QR Code untuk mengakses halaman verifikasi ini</p>
                                <p class="text-xs text-gray-500 mb-3 break-all">
                                    {{ route('signatures.verify', $document->qr_code_token) }}
                                </p>
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-xs text-blue-800">
                                        <strong>Token Verifikasi:</strong> 
                                        <span class="break-all font-mono">{{ $document->qr_code_token }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($document->signatures->count() > 0)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Tanda Tangan</h3>
                            <div class="space-y-4">
                                @foreach($document->signatures as $signature)
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $signature->signer_name }}</p>
                                                @if($signature->signer_email)
                                                    <p class="text-sm text-gray-600">{{ $signature->signer_email }}</p>
                                                @endif
                                                @if($signature->signer_position)
                                                    <p class="text-sm text-gray-500">{{ $signature->signer_position }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-2">
                                                    Ditandatangani: {{ $signature->signed_at->format('d M Y H:i') }}
                                                </p>
                                                @if($signature->ip_address)
                                                    <p class="text-xs text-gray-400">IP: {{ $signature->ip_address }}</p>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <img src="{{ $signature->signature_data }}" alt="Signature" class="w-32 h-16 border border-gray-300 rounded bg-white">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Belum ada tanda tangan pada dokumen ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>
