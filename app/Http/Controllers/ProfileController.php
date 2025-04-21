<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile;
        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $request->validate([
            'twitter_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();
        $profile = $user->profile;

        // プロフィール画像のアップロード処理
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（defaultでなければ）
            if ($profile->profile_image !== 'default.png') {
                Storage::delete('public/profile_images/' . $profile->profile_image);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/profile_images', $imageName);
            $profile->profile_image = $imageName;
        }

        $profile->twitter_url = $request->twitter_url;
        $profile->github_url = $request->github_url;
        $profile->save();

        return response()->json([
            'message' => 'プロフィールを更新しました',
            'profile' => $profile
        ]);
    }
}

