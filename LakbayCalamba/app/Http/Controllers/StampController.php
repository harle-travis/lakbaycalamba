<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StampController extends Controller
{
    /**
     * Show QR code scan page (GET request)
     */
    public function showQRCodeScan(Request $request, $establishmentId)
    {
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            // Redirect to login with return URL
            return redirect()->route('login', ['redirect' => url()->current()]);
        }

        // If authenticated, show a page that processes the stamp
        return view('stamp-scan', compact('establishment'));
    }

    /**
     * Process QR code scan and create stamp (POST request)tohoul
     */
    public function processQRCode(Request $request, $establishmentId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // If this is a web request, redirect to login with return URL
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to collect stamps.',
                    'redirect' => route('login', ['redirect' => url()->current()])
                ], 401);
            } else {
                // Redirect to login page with return URL
                return redirect()->route('login', ['redirect' => url()->current()]);
            }
        }

        $user = Auth::user();
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        // Check if user has already collected a stamp from this establishment (ever)
        $existingStamp = Stamp::where('user_id', $user->id)
            ->where('establishment_id', $establishmentId)
            ->first();

        if ($existingStamp) {
            return response()->json([
                'success' => false,
                'message' => 'You have already collected a stamp from this establishment. Each location can only be stamped once!'
            ], 400);
        }

        try {
            // Create new stamp (only one per establishment ever)
            $stamp = Stamp::create([
                'user_id' => $user->id,
                'establishment_id' => $establishmentId,
                'visit_date' => now(),
            ]);

            // Also log as visitor
            \App\Models\Visitor::create([
                'establishment_id' => $establishmentId,
                'user_id' => $user->id,
                'visited_at' => now(),
                'is_guest' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Stamp collected from {$establishment->establishment_name}!",
                'stamp' => $stamp,
                'establishment' => $establishment
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Stamp collection error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'establishment_id' => $establishmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process stamp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's stamps
     */
    public function getUserStamps(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view your stamps.'
            ], 401);
        }

        $user = Auth::user();
        $stamps = Stamp::with('establishment')
            ->where('user_id', $user->id)
            ->orderBy('visit_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'stamps' => $stamps,
            'total_stamps' => $stamps->count()
        ]);
    }

    /**
     * Get establishment's QR code
     */
    public function getQRCode($establishmentId)
    {
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        // If QR code is missing, generate and persist it on-demand
        if (empty($establishment->qr_code)) {
            try {
                $qrUrl = url("/stamp/process/{$establishment->id}");
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrUrl);
                $establishment->update(['qr_code' => $qrCode]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'qr_code' => $establishment->qr_code,
            'establishment' => $establishment
        ]);
    }

    /**
     * Show test page for QR codes
     */
    public function showTestPage()
    {
        $establishments = Establishment::all();
        return view('test-qr-codes', compact('establishments'));
    }

    /**
     * Generate QR code for a specific establishment
     */
    public function generateQRCode(Request $request, $establishmentId)
    {
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        try {
            // Generate QR code URL that points to the stamp scan page (GET route)
            $qrUrl = url("/stamp/process/{$establishment->id}");
            
            // Generate QR code as SVG string
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrUrl);
            
            // Update establishment with QR code
            $establishment->update(['qr_code' => $qrCode]);

            return response()->json([
                'success' => true,
                'message' => 'QR code generated successfully!',
                'qr_code' => $qrCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate QR code for a specific establishment
     */
    public function regenerateQRCode(Request $request, $establishmentId)
    {
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        try {
            // Generate new QR code URL that points to the stamp scan page (GET route)
            $qrUrl = url("/stamp/process/{$establishment->id}");
            
            // Generate new QR code as SVG string
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrUrl);
            
            // Update establishment with new QR code
            $establishment->update(['qr_code' => $qrCode]);

            return response()->json([
                'success' => true,
                'message' => 'QR code regenerated successfully!',
                'qr_code' => $qrCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test stamp collection without counting as visitor (for admin testing)
     */
    public function testStampCollection(Request $request, $establishmentId)
    {
        $establishment = Establishment::find($establishmentId);

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'Establishment not found.'
            ], 404);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to test stamp collection.'
            ], 401);
        }

        $user = Auth::user();

        // Check if user already has a stamp from this establishment
        $existingStamp = Stamp::where('user_id', $user->id)
            ->where('establishment_id', $establishmentId)
            ->first();

        if ($existingStamp) {
            return response()->json([
                'success' => false,
                'message' => "You already have a stamp from {$establishment->establishment_name}!"
            ], 400);
        }

        try {
            // Create new stamp (only one per establishment ever) - TEST ONLY, NO VISITOR LOG
            $stamp = Stamp::create([
                'user_id' => $user->id,
                'establishment_id' => $establishmentId,
                'visit_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "TEST: Stamp collected from {$establishment->establishment_name}! (No visitor count added)",
                'stamp' => $stamp,
                'establishment' => $establishment
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Test stamp collection error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'establishment_id' => $establishmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process test stamp: ' . $e->getMessage()
            ], 500);
        }
    }
}
