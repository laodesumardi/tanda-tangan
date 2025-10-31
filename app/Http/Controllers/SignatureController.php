<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignatureController extends Controller
{
    public function show($token)
    {
        $document = Document::where('qr_code_token', $token)->firstOrFail();
        
        if ($document->isExpired()) {
            return view('signatures.expired', compact('document'));
        }

        if ($document->status !== 'pending') {
            return view('signatures.already_signed', compact('document'));
        }

        return view('signatures.create', compact('document'));
    }

    public function store(Request $request, $token)
    {
        $request->validate([
            'signer_name' => 'required|string|max:255',
            'signer_email' => 'nullable|email|max:255',
            'signer_position' => 'nullable|string|max:255',
            'signature' => 'required|string', // Base64 signature image
        ]);

        $document = Document::where('qr_code_token', $token)->firstOrFail();

        if (!$document->canBeSigned()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat ditandatangani!');
        }

        // Decode and save signature image
        $signatureData = $request->signature;
        $signatureImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

        Signature::create([
            'document_id' => $document->id,
            'signer_name' => $request->signer_name,
            'signer_email' => $request->signer_email,
            'signer_position' => $request->signer_position,
            'signature_data' => $signatureData,
            'ip_address' => $request->ip(),
            'signed_at' => now(),
        ]);

        // Update document status
        $document->update([
            'status' => 'signed'
        ]);

        return redirect()->route('signatures.success', ['token' => $token])
            ->with('success', 'Dokumen berhasil ditandatangani!');
    }

    public function success($token)
    {
        $document = Document::where('qr_code_token', $token)->with('signatures')->firstOrFail();
        return view('signatures.success', compact('document'));
    }

    public function verify($token)
    {
        $document = Document::where('qr_code_token', $token)->with('signatures')->firstOrFail();
        
        // Generate QR Code jika belum ada
        if (!$document->qr_code_path || !Storage::disk('public')->exists($document->qr_code_path)) {
            $qrCodeUrl = route('signatures.verify', $token);
            $qrCodePath = 'qrcodes/' . $document->qr_code_token . '.svg';
            
            // Generate QR Code
            $qrCodeSvg = QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrCodeUrl);
            
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);
            
            // Update document dengan path QR Code
            $document->update(['qr_code_path' => $qrCodePath]);
        }
        
        return view('signatures.verify', compact('document'));
    }

    public function downloadSigned($id)
    {
        $document = Document::with('signatures')->findOrFail($id);
        
        if ($document->signatures->isEmpty()) {
            return redirect()->back()->with('error', 'Dokumen belum ditandatangani!');
        }

        // In a real application, you would merge the signatures with the document
        // For now, we'll just redirect to the original document
        return Storage::disk('public')->download($document->file_path, 'signed_' . $document->original_filename);
    }
}