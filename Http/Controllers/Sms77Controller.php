<?php

namespace Modules\Sms77\Http\Controllers;

use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Sms77\Entities\Sms;
use Modules\Sms77\Misc\HttpClient;

class Sms77Controller extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * @return View
     */
    public function index(): View {
        $msg = (object)[
            'flash' => 0,
            'text' => '',
        ];

        return view('sms77::index', array_merge(compact('msg'), [
            'messages' => Sms::all(),
        ]));
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return View
     * @throws GuzzleException
     */
    public function submit(Request $request): View {
        (new HttpClient)->sms(
            $request,
            ...User::query()->where('phone', '<>', '')->pluck('phone')->unique()->all()
        );

        return $this->index();
    }
}
