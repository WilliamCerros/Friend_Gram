<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
use Storage;

class ProfilesController extends Controller
{
    public function index(User $user)
    {

//        $user->id = (isset($user)) ? $user->id : auth()->user()->id;

        $follows =(auth()->user()) ? auth()->user()->following->contains($user->id) : false;


        $postCount = Cache::remember(
            'count.posts.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->posts->count();
            });

        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->profile->followers->count();
            });

        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->following->count();
            });


        $followersCount = $user->profile->followers->count();
        $followingCount = $user->following->count();


        return view('profiles.index', compact('user','follows', 'postCount', 'followersCount', 'followingCount'));
    }

    public function edit(User $user){
        $this->authorize('update', $user->profile);
        return view('profiles.edit', compact('user'));
    }

    public function update(User $user){
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);


        if (request('image')){

            $file = request()->file('image');

            $name = time() . $file->getClientOriginalName();
            $filePath = 'profile_picture/' . $name;
            $image = Image::make(request()->file('image'))->fit(1000,1000);
            Storage::disk('s3')->put($filePath, $image->stream());
            $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
            $imageSource = $url . $filePath;
            $imageArray = ['image' => $imageSource];
        }

        auth()->user()->profile->update(array_merge(
            $data,
            $imageArray ?? []
        ));

        return redirect("/profile/{$user->id}");
    }
}
