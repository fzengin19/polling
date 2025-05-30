<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollVoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PollVote::paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $option = PollVote::create($request->validated());
        return response()->json($option, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PollVote $option)
    {
        return $option;
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
    public function destroy(string $id)
    {
        //
    }
}
