<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person_type;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PersonTypeController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/PersonTypeList",
     *     summary="Get list of all Person Types",
     *     tags={"PersonType"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of Person Types"
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
            $data = person_type::all();

            return response($data);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/PersonTypeById/{id}",
     *     summary="Get Person Type by ID",
     *     tags={"PersonType"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person Type ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person Type data"
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
        try {
            $data = person_type::find($id);

            if (empty($data)) {
                return response([
                    "Type" => "S",
                    "Message" => "Data Not Found"
                ]);
            }

            return response($data);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/PersonTypeInsert",
     *     operationId="PersonTypeInsert",
     *     tags={"PersonType"},
     *     summary="Insert a new Person Type",
     *     security={{"bearerAuth":{}}},
     *     description="PersonTypeInsert",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Employee"
     *             ),
     *             @OA\Property(
     *                 property="rgb_code",
     *                 type="string",
     *                 example="232, 235, 52",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="hex_code",
     *                 type="string",
     *                 example="#e8eb34",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="created_by_id",
     *                 type="string",
     *                 example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Created successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $data = new person_type();
            $data = $request->only($data->fillable);

            if (person_type::where('name', $data["name"])->count() > 0) {
                return response([
                    "Type" => "E",
                    "Message" => "Record already exists"
                ]);
            }

            $result = person_type::create($data);

            return response([
                "Type" => "S",
                "Message" => "Created successfully",
                "Id" => $result["id"]
            ]);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/PersonTypeUpdate",
     *     summary="Update an existing Person Type",
     *     tags={"PersonType"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *             @OA\Property(
     *                 property="id",
     *                 type="string",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Employee"
     *             ),
     *             @OA\Property(
     *                 property="rgb_code",
     *                 type="string",
     *                 example="232, 235, 52",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="hex_code",
     *                 type="string",
     *                 example="#e8eb34",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="updated_by_id",
     *                 type="string",
     *                 example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"
     *             )
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
        try {
            $data = new person_type();
            $data = $request->only($data->fillable);

            if (
                person_type::where('id', '!=', $data["id"])
                    ->where('name', $data["name"])
                    ->count() > 0
            ) {
                return response([
                    "Type" => "E",
                    "Message" => "Record already exists"
                ]);
            }

            person_type::findOrFail($data['id'])->update($data);

            return response([
                "Type" => "S",
                "Message" => "Updated successfully",
                "Id" => $data['id']
            ]);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/PersonTypeDelete/{id}",
     *     summary="Delete a Person Type by ID",
     *     tags={"PersonType"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person Type ID",
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
        try {
            person_type::findOrFail($id)->delete();

            return response([
                "Type" => "S",
                "Message" => "Deleted successfully"
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'Type' => 'E',
                'Message' => 'Data Not Found'
            ], 404);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }
}
