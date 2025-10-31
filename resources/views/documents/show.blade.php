@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('documents.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                ← Kembali ke Daftar Dokumen
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>
                @if($document->description)
                    <p class="mt-2 text-sm text-gray-600">{{ $document->description }}</p>
                @endif
            </div>

            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">File</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->original_filename }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($document->status === 'signed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Ditandatangani
                                </span>
                            @elseif($document->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Menunggu Tanda Tangan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    @if($document->expires_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kedaluwarsa</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->expires_at->format('d M Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code untuk Tanda Tangan</h3>
                        @if($document->qr_code_path && Storage::disk('public')->exists($document->qr_code_path))
                            <div class="inline-block p-4 bg-white rounded-lg shadow-sm">
                                <img src="{{ route('documents.qrcode', $document->id) }}" alt="QR Code" class="w-64 h-64" style="max-width: 256px; height: auto;">
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                Scan QR code ini untuk menandatangani dokumen
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Link: <a href="{{ route('signatures.show', $document->qr_code_token) }}" class="text-indigo-600 hover:underline" target="_blank">{{ route('signatures.show', $document->qr_code_token) }}</a>
                            </p>
                        @elseif($document->qr_code_path)
                            <p class="text-sm text-red-500">
                                File QR Code tidak ditemukan. Path: {{ $document->qr_code_path }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">QR Code belum dihasilkan</p>
                        @endif
                    </div>
                    <div class="ml-6">
                        <a href="{{ route('documents.download', $document->id) }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium">
                            Download Dokumen
                        </a>
                    </div>
                </div>
            </div>

            @if($document->signatures->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tanda Tangan</h3>
                    <div class="space-y-4">
                        @foreach($document->signatures as $signature)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $signature->signer_name }}</p>
                                        @if($signature->signer_email)
                                            <p class="text-sm text-gray-600">{{ $signature->signer_email }}</p>
                                        @endif
                                        @if($signature->signer_position)
                                            <p class="text-sm text-gray-500">{{ $signature->signer_position }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-2">Ditandatangani: {{ $signature->signed_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <img src="{{ $signature->signature_data }}" alt="Signature" class="w-32 h-16 border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
