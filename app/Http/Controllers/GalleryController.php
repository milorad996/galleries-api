<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryRequest;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->query('per_page', 10);
        $galleries = Gallery::with('author', 'images')->paginate($per_page);

        return response()->json($galleries);
    }
    public function filter(Request $request)
    {
        $term = $request->input('term');
        $userId = $request->input('author');

        return Gallery::filter($term, $userId);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GalleryRequest $request)
    {



        $gallery = new Gallery();
        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->user_id = Auth::user()->id;
        $gallery->save();

        $imgs = [];
        foreach ($request->images as $img) {
            $imgs[] = new Image($img);
        }
        $gallery->images()->saveMany($imgs);

        return response()->json($gallery);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gallery = Gallery::with(['images', 'author', 'comments', 'comments.user'])->find($id);

        return response()->json($gallery);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GalleryRequest $request, $id)
    {
        $gallery = Gallery::find($id);
        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->user_id = Auth::user()->id;
        $gallery->save();

        $gallery->images()->delete();
        $imgs = [];
        foreach (request('images') as $img) {
            $imgs[] = new Image($img);
        }
        $gallery->images()->saveMany($imgs);

        return response()->json($gallery->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gallery = Gallery::find($id);
        $gallery->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}
