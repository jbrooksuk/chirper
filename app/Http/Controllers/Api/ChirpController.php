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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $chirps = Chirp::query()
            ->with('user')
            ->when($request->user_id, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($request->search, fn ($query, $search) => $query->where('message', 'like', "%{$search}%"))
            ->latest()
            ->paginate($request->input('per_page', 25));

        return ChirpResource::collection($chirps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreChirpRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChirpRequest $request)
    {
        $chirp = $request->user()->chirps()->create($request->validated());

        return ChirpResource::make($chirp);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function show(Chirp $chirp)
    {
        return ChirpResource::make($chirp);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        $chirp->update($request->all());

        return ChirpResource::make($chirp);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return response()->noContent();
    }
}
