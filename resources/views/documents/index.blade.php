@extends('layouts.app')

@section('title', 'Daftar Dokumen')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Dokumen</h1>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $documents->total() }} dokumen</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('documents.signatures') }}" class="bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    Lihat Tanda Tangan
                </a>
                <a href="{{ route('documents.create') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium">
                    + Upload Dokumen Baru
                </a>
            </div>
        </div>

        @if($documents->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($documents as $document)
                        <li>
                            <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
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
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $document->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $document->original_filename }} • 
                                                {{ $document->signatures->count() }} tanda tangan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('documents.show', $document->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Lihat Detail
                                        </a>
                                        <a href="{{ route('documents.download', $document->id) }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada dokumen</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan mengupload dokumen baru.</p>
                <div class="mt-6">
                    <a href="{{ route('documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Upload Dokumen
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
