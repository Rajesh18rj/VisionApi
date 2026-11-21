<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\department;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/DepartmentList",
     *     summary="Get list of all Departments",
     *     tags={"Department"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of Departments"
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
            $data = department::with(['category'])->orderBy('display_order')->get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/DepartmentById/{id}",
     *     summary="Get Department by ID",
     *     tags={"Department"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department data"
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
            $data = department::find($id);
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
     *      path="/api/DepartmentInsert",
     *      operationId="DepartmentInsert",
     *      tags={"Department"},
     *      summary="Insert a new Department",
     *      security={{"bearerAuth":{}}},
     *      description="DepartmentInsert",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "code", "category_id" , "display_order" , "created_by_id"},
     *            @OA\Property(property="name", type="string", format="string", example=""),
     *            @OA\Property(property="code", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="category_id", type="string", format="string", example="", nullable=true),
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
            $data = new department();
            $data = $request->only($data->fillable);
            if (department::where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                $result = department::create($data);
                return response(["Type" => "S", "Message" => "Created successfully", "Id" => $result["id"]]);
            }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/DepartmentUpdate",
     *     summary="Update an existing Department",
     *     tags={"Department"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name", "code", "category_id" , "display_order","updated_by_id"},
     *             @OA\Property(property="id", type="string",format="string", example=""),
     *             @OA\Property(property="name", type="string",format="string", example=""),
     *             @OA\Property(property="code", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="category_id", type="string", format="string", example="", nullable=true),
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
            $data = new department();
            $data =  $request->only($data->fillable);
            if (department::where('id', '!=', $data["id"])->where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                department::findOrFail($data['id'])->update($data);
                return response(["Type" => "S", "Message" => "Updated successfully", "Id" => $data['id']]);
            }
         } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/DepartmentDelete/{id}",
     *     summary="Delete a Department by ID",
     *     tags={"Department"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID",
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
            department::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }

    }

    /**
     * @OA\Get(
     *     path="/api/DepartmentByCategoryId/{category_id}",
     *     summary="Get Department by ID",
     *     tags={"Department"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         required=true,
     *         description="category_id",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department data"
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
    public function DepartmentByCategoryId($category_id)
    {
        try{
            $data = department::where('category_id' , $category_id)->get();
            if(empty($data)){
                return response(["Type" => "s", "Message" => "Data Not Found"]);
            }
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }
}
