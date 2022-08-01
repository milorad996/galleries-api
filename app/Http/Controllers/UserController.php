<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index($id)
    {

        $author_galleries = Gallery::where(['user_id' => $id])->with('images', 'author')->paginate();

        return response()->json($author_galleries);
    }
    //public function index(Request $request, $id) {
    //$term = $request->input('term');

    // return Gallery::filter($term, $id);
    //}

}
