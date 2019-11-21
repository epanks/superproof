<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Paket_data;

class PaketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return Paket_data::paginate(10);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nmpaket' => 'required|string|max:255'
        ]);

        return Paket_data::create([
            'nmpaket' => $request['nmpaket'],
            'pagurmp' => $request['pagurmp'],
            'keuangan' => $request['keuangan'],
            'progres_fisik' => $request['progres_fisik']
        ]);
    }

    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $paket = Paket_data::findOrFail($id);

        $this->validate($request, [
            'nmpaket' => 'required|string|max:255'
        ]);

        $paket->update($request->all());
        return ['message' => 'Updated Paket'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $paket = Paket_data::findOrFail($id);
        // delete the Paket

        $paket->delete();

        return ['message' => 'Paket Deleted'];
    }

    public function search()
    {

        if ($search = \Request::get('q')) {
            $paketdt = Paket_data::where(function ($query) use ($search) {
                $query->where('nmpaket', 'LIKE', "%$search%")
                    ->orWhere('kdsatker', 'LIKE', "%$search%");
            })->paginate(20);
        } else {
            $paketdt = Paket_data::paginate(10);
        }

        return $paketdt;
    }
}
