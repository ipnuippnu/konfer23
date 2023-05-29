<?php

namespace App\Http\Controllers\Admin;

use App\Generators\Invitation;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Types\Face;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->eloquent(Guest::with('code'))->addColumn('vip', Guest::whereType('vip')->count())->addColumn('vvip', Guest::whereType('vvip')->count())->addIndexColumn()->toJson();
        
        return view('admin.guests');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:guests',
            'jabatan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'type' => 'required|in:vip,vvip',
            'event' => 'required|in:ymf,konfercab'
        ]);

        DB::beginTransaction();
        $result = Guest::create([
            'name' => trim($request->get('name')),
            'jabatan' => trim($request->get('jabatan')),
            'address' => trim($request->get('alamat')),
            'type' => trim($request->get('type')),
            'keterangan' => trim($request->get('keterangan')),
            'event' => trim($request->get('event')),
        ])->name;
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => "$result berhasil ditambahkan."
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest, Request $request)
    {
        $request->validate([
            'type' => 'in:front,back,pdf'
        ]);

        switch ($request->get('type')) {
            case 'front':
                return response(Invitation::generate($guest, Face::FRONT), 200, [
                    'Content-Type' => 'image/jpeg',
                    'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "DEPAN_$guest->name") . '.jpg"'
                ]);
                break;
            
            
            case 'back':
                return response(Invitation::generate($guest, Face::BACK), 200, [
                    'Content-Type' => 'image/jpeg',
                    'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "BELAKANG_$guest->name") . '.jpg"'
                ]);
                break;
            
            
            case 'pdf':
                return response(Invitation::pdf($guest), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "$guest->name") . '.pdf"'
                ]);
                break;
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        $guest->delete();

        return response()->json([
            'status' => true
        ]);
    }

}
