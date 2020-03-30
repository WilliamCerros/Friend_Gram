<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Storage;
use function GuzzleHttp\Psr7\copy_to_string;

class PostsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $users = auth()->user()->following()->pluck('profiles.user_id');

        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);

        $total = $posts->toArray()["total"];

        if($total == 0){
           return redirect("/profile/" . auth()->user()->id);
        }

        $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
        $images = [];
        $files = Storage::disk('s3')->files('images');
        foreach ($files as $file) {
            $images[] = [
                'name' => str_replace('images/', '', $file),
                'src' => $url . $file
            ];
        }


        return view('posts.index', compact('posts'));
    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'caption' => 'required',
            'image' => ['required', 'image'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $name = time() . $file->getClientOriginalName();

            $filePath = 'images/' . $name;


            $image = Image::make($request->file('image'))->fit(1200,1200);
            Storage::disk('s3')->put($filePath, $image->stream());

            $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
            $imageSource = $url . $filePath;


            auth()->user()->posts()->create([
                'caption' => $data['caption'],
                'image' => $imageSource,
        ]);
        }


        return redirect('/profile/' . auth()->user()->id);
    }

    public function show(\App\Post $post){
        return view('posts.show', compact('post'));
    }
}
