@extends('layouts.app')

@section('title', 'Semua Tanda Tangan')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Semua Tanda Tangan</h1>
            <a href="{{ route('documents.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                ← Kembali ke Daftar Dokumen
            </a>
        </div>

        @if($signatures->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($signatures as $signature)
                        <li class="p-4 hover:bg-gray-50">
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
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $signature->signer_name }}
                                            </p>
                                            @if($signature->signer_email)
                                                <p class="text-sm text-gray-500">{{ $signature->signer_email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="ml-13">
                                        @if($signature->signer_position)
                                            <p class="text-xs text-gray-500 mb-1">
                                                <span class="font-medium">Jabatan:</span> {{ $signature->signer_position }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-500 mb-1">
                                            <span class="font-medium">Dokumen:</span> 
                                            <a href="{{ route('documents.show', $signature->document->id) }}" class="text-indigo-600 hover:underline">
                                                {{ $signature->document->title }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Waktu:</span> {{ $signature->signed_at->format('d M Y, H:i:s') }}
                                        </p>
                                        @if($signature->ip_address)
                                            <p class="text-xs text-gray-400 mt-1">IP: {{ $signature->ip_address }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-4">
                                    <div class="text-center">
                                        <img src="{{ $signature->signature_data }}" alt="Signature" 
                                             class="w-40 h-20 border border-gray-300 rounded bg-white shadow-sm"
                                             style="max-width: 160px;">
                                        <p class="text-xs text-gray-400 mt-1">Tanda Tangan</p>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('documents.show', $signature->document->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            Lihat Dokumen
                                        </a>
                                        <a href="{{ route('signatures.verify', $signature->document->qr_code_token) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-indigo-300 shadow-sm text-xs font-medium rounded text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                            Verifikasi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4">
                {{ $signatures->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada tanda tangan</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada dokumen yang ditandatangani.</p>
                <div class="mt-6">
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Lihat Dokumen
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

