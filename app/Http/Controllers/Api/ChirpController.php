<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChirpRequest;
use App\Http\Resources\ChirpResource;
use App\Models\Chirp;
use Illuminate\Http\Request;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chirps = Chirp::query()
            ->with('user')
            ->when($request->user_id,
                fn ($query, $userId) => $query->where('user_id', $userId)
            )
            ->when($request->search,
                fn ($query, $search) => $query->where('message', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate($request->input('per_page', 25));

        return ChirpResource::collection($chirps);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp): ChirpResource
    {
        return ChirpResource::make($chirp);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChirpRequest $request): ChirpResource
    {
        $chirp = $request->user()
            ->chirps()
            ->create($request->validated());

        return ChirpResource::make($chirp);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): ChirpResource
    {
        $this->authorize('update', $chirp);

        $chirp->update($request->all());

        return ChirpResource::make($chirp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return response()->noContent();
    }
}
