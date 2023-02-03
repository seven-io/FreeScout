<?php

namespace Modules\Seven\Http\Controllers;

use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Seven\Entities\Sms;
use Modules\Seven\Misc\Messenger;

class SevenController extends Controller {
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

        return view('seven::index', array_merge(compact('msg'), [
            'messages' => Sms::all(),
        ]));
    }

    /**
     * @param int $id
     * @return View
     */
    public function user(int $id): View {
        return view('seven::user', [
            'msg' => (object)[
                'flash' => 0,
                'text' => '',
            ],
            'user' => User::findOrFail($id),
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return View
     * @throws GuzzleException
     */
    public function userSubmit(int $id, Request $request): View {
        /** @var User $user */
        $user = User::findOrFail($id);

        (new Messenger)->sms($request, $user->getAttribute('phone'));

        return $this->user($id);
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

        (new Messenger)->sms($request, ...$builder->pluck('phone')->unique()->all());

        return $this->index();
    }
}
