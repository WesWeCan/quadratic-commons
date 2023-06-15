<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Http\Requests\StoreElectionRequest;
use App\Http\Requests\UpdateElectionRequest;
use Inertia\Inertia;

use illuminate\Support\Str;


class ElectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($uuid)
    {


        $election = Election::where('uuid', $uuid)->firstOrFail();

        return Inertia::render('ElectionVote', [
            'election' => $election,
        ]);



    }

    public function results($uuid)
    {

        $election = Election::where('uuid', $uuid)->with('votes')->firstOrFail();


        return Inertia::render('ElectionResults', [
            'election' => $election,
        ]);



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('ElectionCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreElectionRequest $request)
    {

        $validated = $request->validated();



        $election = Election::create([
            'name' => $validated['name'],
            'uuid' => Str::uuid(),
            'description' => $validated['description'],
            'credits' => $validated['credits'],
            'motions' => $validated['motions'],
            'options' => [],
        ]);

        return redirect()->route('election.created', ['uuid' => $election->uuid]);


    }


    public function created($uuid)
    {

        $election = Election::where('uuid', $uuid)->firstOrFail();

        return Inertia::render('ElectionCreated', [
            'election' => $election,
        ]);


    }



    /**
     * Display the specified resource.
     */
    public function show(Election $election)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Election $election)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateElectionRequest $request, Election $election)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Election $election)
    {
        //
    }
}
