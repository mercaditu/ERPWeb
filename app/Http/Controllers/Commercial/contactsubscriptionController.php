<?php
namespace App\Http\Controllers\Commercial;

use App\Empresa;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Validator;
use Session;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Routing\Route;
use Auth;
use App\Subcription;
use App\Contact;
use App\ContactSubsciption;
use App\Items;

class contactsubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $username = Session::get('username');

        $contacts = Contact::where('id_company', Auth::user()->id_company)->orderBy('name')->simplePaginate(10000);

        //$usuarios= User::buscar($palabra)->orderBy('id','DESC')->get();
        return view('commercial/list/contact')
        ->with('contacts',$contacts)
        ->with('username',$username);

    }

    public function indexCustomers(Request $request)
    {
        $username = $request->session()->get('username');
        $contacts = Contact::where('is_customer', true)->where('id_company', Auth::user()->id_company)->orderBy('name')->simplePaginate(10000);

        //$usuarios= User::buscar($palabra)->orderBy('id','DESC')->get();
        return view('commercial/list/contact')->with('contacts',$contacts)->with('username',$username);
    }

    public function indexSuppliers(Request $request)
    {
        $username = $request->session()->get('username');
        $contacts = Contact::where('is_supplier', true)->where('id_company', Auth::user()->id_company)->orderBy('name')->simplePaginate(10000);

        //$usuarios= User::buscar($palabra)->orderBy('id','DESC')->get();
        return view('commercial/list/contact')->with('contacts',$contacts)->with('username',$username);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

      $username = $request->session()->get('username');
      $contacts = Contact::where('id_contact', $id)->first();
      //$usuarios= User::buscar($palabra)->orderBy('id','DESC')->get();
      return view('commercial/form/contact')
      ->with('contacts',$contacts)
        ->with('username',$username);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

      $username = Session::get('username');
      //$contacts = Contact::where('id_contact', $id)->get();
      $contacts= Contact::find($id);
      $contact_subscription = ContactSubsciption::where('id_contact', $id)->first();
    //  dd($contact_subscription);
      //$usuarios= User::buscar($palabra)->orderBy('id','DESC')->get();
      return view('commercial/form/subscription')
      ->with('contacts',$contacts)
      ->with('username',$username)
      ->with('contact_subscription',$contact_subscription);
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
        // dd($id);


              $contact_subscription= ContactSubsciption::findOrFail($id);
              $contact_subscription->fill($request->all());

              $contact_subscription->save();


      return redirect('contacts');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}