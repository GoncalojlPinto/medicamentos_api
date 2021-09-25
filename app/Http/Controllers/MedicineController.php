<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Exception;
use Illuminate\Contracts\Validation\Validator as ContractValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Medicine::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = $this->ValidateMedicineData($input);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $medicine = new Medicine();
        $medicine->drug = $request->drug;
        $medicine->brand = $request->brand;

        try{
            $medicine->save();
            return response()->json($medicine, Response::HTTP_CREATED);
        }catch(Exception $e){
            return response()->json($this->getInternalErrorMessage("Ocorreu um erro ao tentar gravar o medicamento!"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function show(Medicine $medicine)
    {
        return response()->json($medicine, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medicine $medicine)
    {
        $input = $request->all();
        $validator = $this->validateMedicineData($input);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $medicine->brand = $input["brand"];
        $medicine->drug = $input["drug"];

        try{
            $medicine->save();
            return response()->json($medicine, Response::HTTP_OK);
        }catch(Exception $e){
            return response()->json($this->getInternalErrorMessage("Ocorreu um erro ao tentar gravar o medicamento!"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medicine $medicine)
    {

        try{
            $medicine->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        }catch(Exception $e){
            return response()->json($this->getInternalErrorMessage("Erro ao tentar apagar medicamento"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateMedicineData(array $data): ContractValidator{

        $rules = [
            "drug" => "required|min:2",
            "brand" => "required|min:2"
        ];

        return Validator::make($data, $rules);
    }

    private function getInternalErrorMessage(string $message): array{
        return ["errors" => new MessageBag(["internal_error" => $message])];
    }
}
