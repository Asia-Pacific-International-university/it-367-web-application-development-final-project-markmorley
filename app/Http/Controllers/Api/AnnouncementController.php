<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AnnouncementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Announcement::class);

        $query = Announcement::with('author')
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if (! $request->user()->hasRole(User::ROLE_ADMIN)) {
            $query->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        return AnnouncementResource::collection($query->paginate(10));
    }

    public function store(StoreAnnouncementRequest $request): AnnouncementResource
    {
        $announcement = $request->user()
            ->announcements()
            ->create($request->validated());

        return new AnnouncementResource($announcement->load('author'));
    }

    public function show(Announcement $announcement): AnnouncementResource
    {
        Gate::authorize('view', $announcement);

        return new AnnouncementResource($announcement->load('author'));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): AnnouncementResource
    {
        // Authorization happens in UpdateAnnouncementRequest::authorize().
        $announcement->update($request->validated());

        return new AnnouncementResource($announcement->load('author'));
    }

    public function destroy(Announcement $announcement): Response
    {
        Gate::authorize('delete', $announcement);

        $announcement->delete();

        return response()->noContent();
    }
}
