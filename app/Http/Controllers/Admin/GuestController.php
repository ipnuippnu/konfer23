<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->eloquent(Guest::with('code'))->editColumn('created_at', fn($data) => $data->created_at->format('Y-m-d H:i:s'))->toJson();
        
        return view('admin.guests');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^([-a-zA-Z\.,\' ]*)$/',
            'jabatan' => 'nullable|regex:/^([-a-zA-Z\.,\' ]*)$/',
            'alamat' => 'nullable|regex:/^([-a-zA-Z\.,\' ]*)$/',
            'keterangan' => 'nullable|string',
            'type' => 'required|in:vip,vvip'
        ]);

        DB::beginTransaction();
        $result = Guest::create([
            'name' => trim($request->get('name')),
            'jabatan' => trim($request->get('jabatan')),
            'address' => trim($request->get('alamat')),
            'type' => trim($request->get('type')),
            'keterangan' => trim($request->get('keterangan')),
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
