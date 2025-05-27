<?php

namespace ShortenerUrl\Shortener\Http\Controllers;

use Illuminate\Http\Request;
use ShortenerUrl\Shortener\Models\ShortLink;
use App\Http\Controllers\Controller;

class ShortLinkController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $code = substr(md5($request->url . now()), 0, 6);

        $short = ShortLink::create([
            'original_url' => $request->url,
            'code' => $code,
        ]);

        return response()->json([
            'short_url' => url('/s/' . $code)
        ]);
    }

    public function redirect($code)
    {
        $short = ShortLink::where('code', $code)->firstOrFail();
        return redirect()->to($short->original_url);
    }

    public static function make($url)
    {
        $code = substr(md5($url . now()), 0, 6);

        $short = ShortLink::create([
            'original_url' => $url,
            'code' => $code,
        ]);

        return url('/s/' . $code);
    }
}
