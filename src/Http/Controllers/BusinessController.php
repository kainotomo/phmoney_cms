<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Jetstream::inertia()->render(request(), 'Business/Index');
    }

}
