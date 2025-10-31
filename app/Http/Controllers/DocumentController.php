<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->paginate(10);
        return view('documents.index', compact('documents'));
    }

    public function signatures()
    {
        $signatures = Signature::with('document')->latest('signed_at')->paginate(15);
        return view('documents.signatures', compact('signatures'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $file = $request->file('document');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $filename, 'public');

        $qrCodeToken = Str::random(32);
        $qrCodeUrl = route('signatures.show', ['token' => $qrCodeToken]);
        
        // Generate QR Code - Using SVG format (doesn't require imagick)
        // SVG is lightweight, scalable, and works perfectly for QR codes
        $qrCodePath = 'qrcodes/' . $qrCodeToken . '.svg';
        $qrCodeSvg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H') // High error correction for better scanning
            ->generate($qrCodeUrl);
        
        Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'original_filename' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'qr_code_token' => $qrCodeToken,
            'qr_code_path' => $qrCodePath,
            'status' => 'pending',
            'expires_at' => $request->expires_at,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Dokumen berhasil dibuat dan QR Code telah dihasilkan!');
    }

    public function show($id)
    {
        $document = Document::with('signatures')->findOrFail($id);
        return view('documents.show', compact('document'));
    }

    public function qrcode($id)
    {
        $document = Document::findOrFail($id);
        
        if (!$document->qr_code_path || !Storage::disk('public')->exists($document->qr_code_path)) {
            abort(404, 'QR Code not found');
        }

        $filePath = Storage::disk('public')->path($document->qr_code_path);
        $mimeType = str_ends_with($document->qr_code_path, '.svg') ? 'image/svg+xml' : 'image/png';
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan!');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Delete files
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        if ($document->qr_code_path && Storage::disk('public')->exists($document->qr_code_path)) {
            Storage::disk('public')->delete($document->qr_code_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}