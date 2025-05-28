<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poll\StorePollRequest;
use App\Http\Requests\Poll\UpdatePollRequest;
use App\Models\Poll;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Poll::with(['options', 'user'])->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePollRequest $request)
    {
        $poll = Poll::create($request->validated());
        return response()->json($poll, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poll = Poll::with(['options', 'user'])->findOrFail($id);
        return response()->json($poll);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePollRequest $request, string $id)
    {
        $poll = Poll::findOrFail($id);
        $poll->update($request->validated());

        return response()->json($poll);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $poll = Poll::findOrFail($id);
        $poll->delete();

        return response()->json(null, 204);
    }
}
