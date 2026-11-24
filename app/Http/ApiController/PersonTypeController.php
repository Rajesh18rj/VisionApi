<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * @OA\Tag(
 *     name="Person Type",
 *     description="Person Type API"
 * )
 */
class PersonTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/PersonTypeList",
     *     tags={"Person Type"},
     *     summary="Get all Person Types",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function List()
    {
        try {
            $data = person_type::orderBy('display_order', 'asc')
                ->get();

            return response()->json([
                "Type" => "S",
                "Message" => "Success",
                "Data" => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                "Type" => "E",
                "Message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/PersonTypeById/{id}",
     *     tags={"Person Type"},
     *     summary="Get Person Type by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function ById($id)
    {
        try {
            $data = person_type::find($id);

            if (!$data) {
                return response()->json([
                    "Type" => "E",
                    "Message" => "Person Type not found"
                ], 404);
            }

            return response()->json([
                "Type" => "S",
                "Message" => "Success",
                "Data" => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                "Type" => "E",
                "Message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/PersonTypeInsert",
     *     tags={"Person Type"},
     *     summary="Insert Person Type",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(
     *                 property="category",
     *                 type="string",
     *                 nullable=true,
     *                 enum={"General","Employee"}
     *             ),
     *             @OA\Property(property="display_order", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category' => 'nullable|in:General,Employee',
                'display_order' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "Type" => "E",
                    "Message" => $validator->errors()->first()
                ], 422);
            }

            $duplicate = person_type::where(
                'name',
                $request->name
            )->exists();

            if ($duplicate) {
                return response()->json([
                    "Type" => "E",
                    "Message" => "Person Type already exists"
                ]);
            }

            $data = new person_type();

            $data->name = $request->name;
            $data->category = $request->category;
            $data->display_order = $request->display_order ?? 0;

            $data->save();

            return response()->json([
                "Type" => "S",
                "Message" => "Person Type created successfully",
                "Id" => $data->id
            ]);

        } catch (Exception $e) {

            return response()->json([
                "Type" => "E",
                "Message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/PersonTypeUpdate",
     *     tags={"Person Type"},
     *     summary="Update Person Type",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(
     *                 property="category",
     *                 type="string",
     *                 nullable=true,
     *                 enum={"General","Employee"}
     *             ),
     *             @OA\Property(property="display_order", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function Update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required|string|max:255',
                'category' => 'nullable|in:General,Employee',
                'display_order' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "Type" => "E",
                    "Message" => $validator->errors()->first()
                ], 422);
            }

            $data = person_type::find($request->id);

            if (!$data) {
                return response()->json([
                    "Type" => "E",
                    "Message" => "Person Type not found"
                ], 404);
            }

            $duplicate = person_type::where(
                'name',
                $request->name
            )
                ->where('id', '!=', $request->id)
                ->exists();

            if ($duplicate) {
                return response()->json([
                    "Type" => "E",
                    "Message" => "Person Type already exists"
                ]);
            }

            $data->name = $request->name;
            $data->category = $request->category;
            $data->display_order = $request->display_order ?? 0;

            $data->save();

            return response()->json([
                "Type" => "S",
                "Message" => "Person Type updated successfully",
                "Id" => $data->id
            ]);

        } catch (Exception $e) {

            return response()->json([
                "Type" => "E",
                "Message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/PersonTypeDelete/{id}",
     *     tags={"Person Type"},
     *     summary="Delete Person Type",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function Delete($id)
    {
        try {
            $data = person_type::find($id);

            if (!$data) {
                return response()->json([
                    "Type" => "E",
                    "Message" => "Person Type not found"
                ], 404);
            }

            $data->delete();

            return response()->json([
                "Type" => "S",
                "Message" => "Person Type deleted successfully"
            ]);

        } catch (Exception $e) {

            return response()->json([
                "Type" => "E",
                "Message" => $e->getMessage()
            ], 500);
        }
    }
}
