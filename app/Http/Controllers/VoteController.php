<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Election;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\UpdateVoteRequest;

use Illuminate\Support\Str;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoteRequest $request)
    {

        $validated = $request->validated();
        // dd($validated['motions']);

        $election = Election::where('uuid', $validated['election_uuid'])->firstOrFail();

        $vote = Vote::create([
            'name' => $validated['name'],
            'uuid' => Str::uuid(),
            'remainingCredits' => $validated['remainingCredits'],
            'motions' => $validated['motions'],
            "votecode" => str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'election_id' => $election->id,
        ]);


        // dd($vote);

        return redirect()->route('election.results.code', ['uuid' => $election->uuid, 'votecode' => $vote->votecode]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Vote $vote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVoteRequest $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
