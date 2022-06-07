<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index(){
        $tariffs = [];
        return view('admin.tariff.index',compact('tariffs'));
    }

    public function create(){
        return view('admin.tariff.create');
    }
}
