<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\nvr;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NvrController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/NvrList",
     *     summary="Get list of all NVRs",
     *     tags={"NVR"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of NVRs"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function List()
    {
        try {
            $data = nvr::with(['section', 'section.floor','section.floor.block'])->orderBy('created_on', 'desc')->get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/NvrById/{id}",
     *     summary="Get NVR by ID",
     *     tags={"NVR"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NVR ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NVR data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function ById($id)
    {
        try{
            $data = nvr::find($id);
            if(empty($data)){
                return response(["Type" => "s", "Message" => "Data Not Found"]);
            }
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }


    /**
     * @OA\Post(
     *      path="/api/NvrInsert",
     *      operationId="NvrInsert",
     *      tags={"NVR"},
     *      summary="Insert a new NVR",
     *      security={{"bearerAuth":{}}},
     *      description="NVRInsert",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"section_id","asset_no", "serial_number" ,"model_name","ip_address","manufacturer","vendor","install_date","last_working_on", "is_working", "display_order", "created_by_id"},
     *            @OA\Property(property="section_id", type="string", format="string", example=""),
     *            @OA\Property(property="asset_no", type="string", format="string", example=""),
     *            @OA\Property(property="serial_number", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="model_name", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="ip_address", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="manufacturer", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="vendor", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="install_date", type="string", format="date", example="2025-01-15", nullable=true),
     *            @OA\Property(property="last_working_on", type="string", format="date-time", example="2025-01-15 14:30:00", nullable=true),
     *            @OA\Property(property="is_working", type="boolean", example=true, nullable=true),
     *            @OA\Property(property="display_order", type="integer", format="int64", example=1, nullable=true),
     *            @OA\Property(property="created_by_id", type="string", format="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function Insert(Request $request)
    {
        try{
            $data = new nvr();
            $data = $request->only($data->fillable);
            // if (nvr::where('name', $data["name"])->count() > 0) {
            //     return response(["Type" => "E", "Message" => "Record already exits"]);
            // } else {
                $result = nvr::create($data);
                return response(["Type" => "S", "Message" => "Created successfully", "Id" => $result["id"]]);
           // }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/NvrUpdate",
     *     summary="Update an existing NVR",
     *     tags={"NVR"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","section_id", "asset_no", "serial_number" ,"model_name","ip_address","manufacturer","vendor","install_date","last_working_on","is_working", "display_order","updated_by_id"},
     *             @OA\Property(property="id", type="string", format="string" ,example=""),
     *             @OA\Property(property="section_id", type="string",format="string", example=""),
     *             @OA\Property(property="asset_no", type="string",format="string", example=""),
     *             @OA\Property(property="serial_number", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="model_name", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="ip_address", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="manufacturer", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="vendor", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="install_date", type="string", format="date", example="2025-01-15", nullable=true),
     *             @OA\Property(property="last_working_on", type="string", format="date-time", example="2025-01-15 14:30:00", nullable=true),
     *             @OA\Property(property="is_working", type="boolean", example=true, nullable=true),
     *             @OA\Property(property="display_order", type="integer", format="int64", example="", nullable=true),
     *            @OA\Property(property="updated_by_id", type="string", format="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function Update(Request $request)
    {
        try{
            $data = new nvr();
            $data =  $request->only($data->fillable);
            // if (nvr::where('id', '!=', $data["id"])->where('name', $data["name"])->count() > 0) {
            //     return response(["Type" => "E", "Message" => "Record already exits"]);
            // } else {
                nvr::findOrFail($data['id'])->update($data);
                return response(["Type" => "S", "Message" => "Updated successfully", "Id" => $data['id']]);
           // }
         } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/NvrDelete/{id}",
     *     summary="Delete a NVR by ID",
     *     tags={"NVR"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NVR ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function Delete($id)
    {
        try{
            nvr::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }

    }
}
