<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentPic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EstablishmentController extends Controller
{
    public function index()
    {
        $establishments = Establishment::with('pictures')->get();
        return view('superadmin.manage_establishments', compact('establishments'));
    }

    public function store(Request $request)
    {
        try {
            // Log file information for debugging
            \Log::info('File upload debug:', [
                'has_files' => $request->hasFile('pictures'),
                'file_count' => $request->hasFile('pictures') ? count($request->file('pictures')) : 0,
                'files' => $request->hasFile('pictures') ? array_map(function($file) {
                    return [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'extension' => $file->getClientOriginalExtension()
                    ];
                }, $request->file('pictures')) : []
            ]);

            $request->validate([
                'establishment_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'maps_data' => 'nullable|string',
                'description' => 'required|string',
                'schedule' => 'required|string',
                'category' => 'nullable|string|max:255',
                'pictures.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240', // Added webp support
            ]);

            // Log the schedule data for debugging
            \Log::info('Creating establishment with schedule:', [
                'schedule' => $request->schedule
            ]);

            // Validate that schedule is valid JSON
            $scheduleData = json_decode($request->schedule, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid schedule format: ' . json_last_error_msg());
            }

            // Create the establishment
            $establishment = Establishment::create([
                'establishment_name' => $request->establishment_name,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'maps_data' => $request->maps_data,
                'description' => $request->description,
                'schedule' => $request->schedule,
                'category' => $request->category,
            ]);

            // Handle picture uploads
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $index => $picture) {
                    // Additional validation for each file
                    if (!$picture->isValid()) {
                        \Log::error("Invalid file at index {$index}: " . $picture->getError());
                        continue;
                    }
                    
                    $path = $picture->store('establishments', 'public');
                    
                    EstablishmentPic::create([
                        'establishment_id' => $establishment->id,
                        'image_path' => $path,
                        'caption' => null, // You can add caption functionality later
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Establishment added successfully!',
                'establishment' => $establishment->load('pictures')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error creating establishment:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating establishment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the establishment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Establishment $establishment)
    {
        return response()->json($establishment->load('pictures'));
    }

    public function update(Request $request, Establishment $establishment)
    {
        try {
            // Log the request data for debugging
            \Log::info('Update request data:', [
                'establishment_id' => $establishment->id,
                'content_type' => $request->header('Content-Type'),
                'all_data' => $request->all(),
                'has_files' => $request->hasFile('pictures'),
                'file_count' => $request->hasFile('pictures') ? count($request->file('pictures')) : 0
            ]);

            $request->validate([
                'establishment_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'maps_data' => 'nullable|string',
                'description' => 'required|string',
                'schedule' => 'required|string',
                'category' => 'nullable|string|max:255',
                'pictures.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

            // Log the schedule data for debugging
            \Log::info('Updating establishment schedule:', [
                'establishment_id' => $establishment->id,
                'schedule' => $request->schedule
            ]);

            // Validate that schedule is valid JSON
            $scheduleData = json_decode($request->schedule, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid schedule format: ' . json_last_error_msg());
            }

            $establishment->update([
                'establishment_name' => $request->establishment_name,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'maps_data' => $request->maps_data,
                'description' => $request->description,
                'schedule' => $request->schedule,
                'category' => $request->category,
            ]);

            // Handle new picture uploads
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $picture) {
                    $path = $picture->store('establishments', 'public');
                    
                    EstablishmentPic::create([
                        'establishment_id' => $establishment->id,
                        'image_path' => $path,
                        'caption' => null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Establishment updated successfully!',
                'establishment' => $establishment->load('pictures')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating establishment:', [
                'establishment_id' => $establishment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the establishment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Establishment $establishment)
    {
        // Delete associated pictures from storage
        foreach ($establishment->pictures as $picture) {
            Storage::disk('public')->delete($picture->image_path);
        }

        $establishment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Establishment deleted successfully!'
        ]);
    }

    public function deletePicture(EstablishmentPic $picture)
    {
        Storage::disk('public')->delete($picture->image_path);
        $picture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Picture deleted successfully!'
        ]);
    }
}
