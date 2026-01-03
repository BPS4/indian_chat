<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\FacilityMaster;
use App\Models\RoomFacility;
use App\Models\RoomPrice;
use App\Models\RoomType;
use App\Models\RoomTypeAddonPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HotelRoomController extends Controller
{
    public function index(Request $request, $hotelId)
    {
        $roomTypes = RoomType::where('hotel_id', $hotelId)->orderBy('id', 'Desc')->get();

        return view('admin.pages.room.list', compact('roomTypes', 'hotelId'));
    }

    public function create(Request $request, $hotelId)
    {
        $FacilityMaster = FacilityMaster::where('facility_for', 'hotel')->get();
        $addons = Addon::get();

        return view('admin.pages.room.add', compact('hotelId', 'FacilityMaster', 'addons'));
    }

    public function store(Request $request, $hotelId)
    {
        $request->merge([
            'addons' => array_filter($request->addons ?? []),
            'price'  => array_filter($request->price ?? []),
            'person' => array_filter($request->person ?? []),
        ]);

        // ✅ 1. Validate all steps' input
        $validated = $request->validate([
            // Step 1
            'room_type' => 'required|string|max:255',
            'room_size' => 'nullable|string|max:255',
            'max_guests' => 'required|min:1',
            'max_child' => 'required|min:0',
            'bed_type' => 'nullable|string|max:255',
            'photo_url' => 'nullable|file|image|max:2048',
            'description' => 'nullable|string',

            // Step 2
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'base_price' => 'required|numeric|min:0',
            'extra_person_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',

            // Step 3
            'facilities' => 'nullable|array',
            'facilities.*' => 'integer|exists:facility_masters,id',

            // Step 4
            'addons' => 'nullable|array',
            'addons.*' => 'integer|exists:addons,id',
            'price' => 'nullable|array',
            'price.*' => 'numeric|min:0',
            'person' => 'nullable|array',
            'person.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            /* -------------------------------
         * STEP 1 → Save RoomType
         * -----------------------------*/
            $photoPath = null;

            if ($request->hasFile('photo_url')) {
                $image = $request->file('photo_url');
                $filename = 'room_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/rooms'), $filename);
                $photoPath = 'images/rooms/' . $filename;
            }


            $roomType = RoomType::create([
                'hotel_id' => $hotelId,
                'room_name' => $validated['room_type'],
                'room_size' => $validated['room_size'] ?? null,
                'max_guests' => $validated['max_guests'] ?? null,
                'max_child' => $validated['max_child'] ?? null,
                'bed_type' => $validated['bed_type'] ?? null,
                'photo_url' => $photoPath,
                'description' => $validated['description'] ?? null,
            ]);

            /* -------------------------------
         * STEP 2 → Save RoomPrice
         * -----------------------------*/
            RoomPrice::create([
                'room_type_id' => $roomType->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'base_price' => $validated['base_price'],
                'extra_person_price' => $validated['extra_person_price'] ?? 0,
                'currency' => $validated['currency'],
            ]);

            /* -------------------------------
         * STEP 3 → Save Facilities
         * -----------------------------*/
            if (!empty($validated['facilities'])) {
                foreach ($validated['facilities'] as $facilityId) {
                    RoomFacility::create([
                        'room_type_id' => $roomType->id,
                        'facility_id' => $facilityId,
                    ]);
                }
            }

            /* -------------------------------
         * STEP 4 → Save Addons & Prices
         * -----------------------------*/
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $index => $addonId) {
                    RoomTypeAddonPrice::create([
                        'room_type_id' => $roomType->id,
                        'addon_id' => $addonId,
                        'price' => $validated['price'][$index] ?? 0,
                        'per_person' => $validated['person'][$index] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('hotel-room.index', $hotelId)
                ->with('success', 'Room created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Optional: log for debugging
            Log::error('Error saving room data', [
                'hotel_id' => $hotelId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Something went wrong while saving room details. Please try again.'])
                ->withInput();
        }
    }

    public function edit($hotelId, $id)
    {
        $roomType = RoomType::with(['roomPrices', 'facilities', 'addons'])->findOrFail($id);

        $FacilityMaster = FacilityMaster::all();
        $addons = Addon::all();

        return view('admin.pages.room.edit', compact('roomType', 'FacilityMaster', 'addons', 'hotelId'));
    }

    public function update(Request $request, $hotelId, $id)
    {
        $request->merge([
            'addons' => array_filter($request->addons ?? []),
            'price'  => array_filter($request->price ?? []),
            'person' => array_filter($request->person ?? []),
        ]);
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'room_size' => 'nullable|string|max:255',
            'max_guests' => 'nullable|integer|min:1',
            'max_child' => 'nullable|integer|min:0',
            'bed_type' => 'nullable|string|max:255',
            'photo_url' => 'nullable|file|image|max:2048',
            'description' => 'nullable|string',

            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'base_price' => 'required|numeric|min:0',
            'extra_person_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',

            'facilities' => 'nullable|array',
            'facilities.*' => 'integer|exists:facility_masters,id',

            'addons' => 'nullable|array',
            'addons.*' => 'integer|exists:addons,id',
            'price' => 'nullable|array',
            'price.*' => 'numeric|min:0',
            'person' => 'nullable|array',
            'person.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $roomType = RoomType::findOrFail($id);

            // Update main room

            if ($request->hasFile('photo_url')) {
                $image = $request->file('photo_url');
                $filename = 'room_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/rooms'), $filename);
                $photoPath = 'images/rooms/' . $filename;
                $roomType->photo_url = $photoPath;
            }

            $roomType->update([
                'room_type' => $validated['room_type'],
                'room_size' => $validated['room_size'] ?? null,
                'max_guests' => $validated['max_guests'] ?? null,
                'max_child' => $validated['max_child'] ?? null,
                'bed_type' => $validated['bed_type'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // Update or create room price (only one record for now)
            RoomPrice::updateOrCreate(
                ['room_type_id' => $roomType->id],
                [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'base_price' => $validated['base_price'],
                    'extra_person_price' => $validated['extra_person_price'] ?? 0,
                    'currency' => $validated['currency'],
                ]
            );

            // Update facilities — delete and reinsert
            RoomFacility::where('room_type_id', $roomType->id)->delete();
            if (!empty($validated['facilities'])) {
                foreach ($validated['facilities'] as $facilityId) {
                    RoomFacility::create([
                        'room_type_id' => $roomType->id,
                        'facility_id' => $facilityId,
                    ]);
                }
            }

            // Update addons — delete and reinsert
            RoomTypeAddonPrice::where('room_type_id', $roomType->id)->delete();
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $i => $addonId) {
                    RoomTypeAddonPrice::create([
                        'room_type_id' => $roomType->id,
                        'addon_id' => $addonId,
                        'price' => $validated['price'][$i] ?? 0,
                        'per_person' => $validated['person'][$i] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('hotel-room.index', $hotelId)
                ->with('success', 'Room updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating room', [
                'room_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Something went wrong while updating the room.'])->withInput();
        }
    }

    public function destroy($hotelId, $id)
    {
        DB::beginTransaction();

        try {
            $roomType = RoomType::findOrFail($id);

            $roomType->roomPrices()->delete();
            $roomType->facilities()->detach();
            $roomType->addons()->detach();

            // Finally, delete the room
            $roomType->delete();

            DB::commit();

            return redirect()->route('hotel-room.index', $hotelId)
                ->with('success', 'Room deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting room: ' . $e->getMessage());

            return redirect()->route('hotel-room.index', $hotelId)
                ->with('error', 'Failed to delete room.');
        }
    }
}
