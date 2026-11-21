<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\section;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/SectionList",
     *     summary="Get list of all Sections",
     *     tags={"Section"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of Sections"
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
            $data = section::with(['floor' , 'floor.block'])->orderBy('display_order')->get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/SectionById/{id}",
     *     summary="Get Section by ID",
     *     tags={"Section"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Section ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Section data"
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
            $data = section::find($id);
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
     *      path="/api/SectionInsert",
     *      operationId="SectionInsert",
     *      tags={"Section"},
     *      summary="Insert a new Section",
     *      security={{"bearerAuth":{}}},
     *      description="SectionInsert",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"floor_id","name", "description" , "display_order" , "created_by_id"},
     *            @OA\Property(property="floor_id", type="string", format="string", example=""),
     *            @OA\Property(property="name", type="string", format="string", example=""),
     *            @OA\Property(property="description", type="string", format="string", example="", nullable=true),
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
            $data = new section();
            $data = $request->only($data->fillable);
            if (section::where('floor_id', $data["floor_id"])->where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                $result = section::create($data);
                return response(["Type" => "S", "Message" => "Created successfully", "Id" => $result["id"]]);
            }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/SectionUpdate",
     *     summary="Update an existing Section",
     *     tags={"Section"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","floor_id", "name", "description" , "display_order","updated_by_id"},
     *             @OA\Property(property="id", type="string", format="string" ,example=""),
     *             @OA\Property(property="floor_id", type="string",format="string", example=""),
     *             @OA\Property(property="name", type="string",format="string", example=""),
     *             @OA\Property(property="description", type="string", format="string", example="", nullable=true),
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
            $data = new section();
            $data =  $request->only($data->fillable);
            if (section::where('id', '!=', $data["id"])->where('floor_id', $data["floor_id"])->where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                section::findOrFail($data['id'])->update($data);
                return response(["Type" => "S", "Message" => "Updated successfully", "Id" => $data['id']]);
            }
         } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/SectionDelete/{id}",
     *     summary="Delete a Section by ID",
     *     tags={"Section"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Section ID",
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
            section::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }

    }
}
