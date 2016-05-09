<?php

namespace App\Http\Controllers;

use Response;
use View;
use Illuminate\Support\Facades\Input;
use hazelnuts23\BillPlzv3\Billplzv3;


class BillPlzPaymentController extends Controller
{
    public function index()
    {
        return View::make('index');
    }

    public function createBill()
    {
        $title = Input::get('title');
        $price = str_replace(".", "", Input::get('price'));
        $fullname = Input::get('fullname');
        $nric = Input::get('nric');
        $email = Input::get('email');
        $telno = Input::get('telno');
        $description = Input::get('description');

        $bplz = new Billplzv3(array('api_key' => 'YOUR API KEY'));
        $bplz->set_data('title', $title);
        $result = $bplz->create_collection();
        $result = json_decode($result);
        $id = $result->id;

        $callback_url = 'http://' . $_SERVER['SERVER_NAME'] . '/payment/processing';
        $redirect_url = 'http://' . $_SERVER['SERVER_NAME'] . '/payment/complete/';

        $secret_key = $this->generateRandomString(42);


        $bplz->set_data(array(
            'collection_id' => $id,
            'description' => $description,
            'amount' => $price,
            'name' => $fullname,
            'email' => $email,
            'mobile' => $telno,
            'callback_url' => $callback_url,
            'redirect_url' => $redirect_url,
            'metadata[token]' => $secret_key,
            'metadata[nric]' => $nric
        ));


        $result = json_decode($bplz->create_bill());
        return Response::json($result);


    }


    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
