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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Tanda Tangan ({{ $document->signatures->count() }})
                        </h3>
                        <a href="{{ route('documents.signatures') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Lihat Semua Tanda Tangan →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @foreach($document->signatures as $signature)
                            <div class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-600 font-semibold text-sm">
                                                        {{ substr($signature->signer_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $signature->signer_name }}</p>
                                                @if($signature->signer_email)
                                                    <p class="text-sm text-gray-600">{{ $signature->signer_email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($signature->signer_position)
                                            <p class="text-sm text-gray-500 mb-1">
                                                <span class="font-medium">Jabatan:</span> {{ $signature->signer_position }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Ditandatangani:</span> {{ $signature->signed_at->format('d M Y, H:i:s') }}
                                        </p>
                                        @if($signature->ip_address)
                                            <p class="text-xs text-gray-400 mt-1">IP: {{ $signature->ip_address }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-center">
                                        <img src="{{ $signature->signature_data }}" alt="Signature" 
                                             class="w-36 h-20 border-2 border-gray-300 rounded bg-white shadow-sm"
                                             style="max-width: 144px;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Belum ada tanda tangan pada dokumen ini</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
