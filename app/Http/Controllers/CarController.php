<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\Car;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hash =  $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        if($checkToken){
            $cars = Car::all();
            return response()->json(array(
                'cars'=>$cars,
                'status'=>'success'
            ), 200);
        }else{
            echo "Index de CarController No Autenticado"; die();
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $hash =  $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Recoger los datos por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);

            //Validación de laravel
            $request->merge($params_array);
            try{
                $validate = $this->validate($request,[
                    'title' => 'required',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
                ]);

            }catch (\Illuminate\Validation\ValidationException $e){
                return $e->getResponse();
            }


            //Guardar el coche
                $car = new Car();
                $car->user_id = $user->sub;
                $car->title = $params->title;
                $car->description = $params->description;
                $car->price = $params->price;
                $car->status = $params->status;
                $car->save();

                $data = array(
                    'car'=>$car,
                    'status' => 'success',
                    'code' => 200
                );

        }else{
            //Devolver un error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'success',
                'code' => 200
            );
        }
        return response()->json($data, 300);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
