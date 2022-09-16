<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fanpage;

class FanpageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fanpage = Fanpage::get();

        // $ch = curl_init("https://www.facebook.com/JustLaughVideo/");
        // curl_setopt($ch, CURLOPT_POST, false);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7 Chrome/91.0.4472.114");
        // curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $data = curl_exec($ch);
        // echo $data;

        return view('fanpage.index', compact('fanpage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fanpage.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        Fanpage::create($data);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $rq)
    {
        $edit_page = '';
        $fanpage = Fanpage::get();
        $fanpage_get = Fanpage::where('id', $id)->get();
        return view('fanpage.index', compact('edit_page', 'fanpage', 'fanpage_get'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $rq, $id)
    {
        $fanpage = Fanpage::where('id', $id)->first();
        $fanpage->theme = $rq->get('theme');
        $fanpage->page_name = $rq->get('page_name');
        $fanpage->link       = $rq->get('link');
        $fanpage->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fanpage = Fanpage::findOrFail($id);
        $fanpage->delete();
    }
}
