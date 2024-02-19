<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChirpRequest;
use App\Http\Resources\ChirpResource;
use App\Models\Chirp;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\ResponseFromFile;

#[Authenticated]
#[Group('Chirps', 'Chirps API')]
#[Response(status: 401, description: 'Unauthenticated', content: '{ "message" : "Unauthenticated" }')]
class ChirpController extends Controller
{
    /**
     * List Chirps.
     *
     * Get a list of Chirps, ordered by latest Chirp first.
     * Rate limited to 60 requests per minute.
     */
    #[ResponseFromApiResource(ChirpResource::class, Chirp::class, collection: true)]
    #[QueryParam('user_id', 'integer', description: 'Filter chirps by user ID', required: false)]
    #[QueryParam('search', 'string', description: 'Search for chirps by message', required: false, example: 'PHP UK')]
    #[QueryParam('per_page', 'integer', description: 'Number of items per page', required: false)]
    #[QueryParam('page', 'integer', description: 'Page number', required: false)]
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
     * Get Chirp.
     */
    #[ResponseFromApiResource(ChirpResource::class, Chirp::class)]
    public function show(Chirp $chirp): ChirpResource
    {
        return ChirpResource::make($chirp);
    }

    /**
     * Create Chirp.
     */
    #[ResponseFromApiResource(ChirpResource::class, Chirp::class)]
    #[ResponseFromFile('resources/api-responses/422.json', status: 422, description: 'Validation error')]
    public function store(StoreChirpRequest $request): ChirpResource
    {
        $chirp = $request->user()
            ->chirps()
            ->create($request->validated());

        return ChirpResource::make($chirp);
    }

    /**
     * Edit Chirp.
     */
    #[ResponseFromApiResource(ChirpResource::class, Chirp::class)]
    #[ResponseFromFile('resources/api-responses/422.json', status: 422, description: 'Validation error')]
    public function update(Request $request, Chirp $chirp): ChirpResource
    {
        $this->authorize('update', $chirp);

        $request->validate([
            // Example: PHP UK is awesome.
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($request->all());

        return ChirpResource::make($chirp);
    }

    /**
     * Delete Chirp.
     */
    #[Response(status: 204, description: 'Chirp deleted successfully.')]
    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return response()->noContent();
    }
}
