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
            'locale' => null,
            'role' => null,
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
        $builder = User::query()->where('phone', '<>', '');

        $filters = ['locale', 'role'];

        foreach ($filters as $filter) {
            $value = $request->post($filter);
            if ($value) $builder = $builder->where($filter, '=', $value);
        }

        (new HttpClient)->sms($request, ...$builder->pluck('phone')->unique()->all());

        return $this->index();
    }
}
