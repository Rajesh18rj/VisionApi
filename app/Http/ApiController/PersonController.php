<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person;
use App\Http\Model\person_type;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/PersonList",
     *     summary="Get list of Persons",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of Persons")
     * )
     */
    public function List(Request $request)
    {
        try {
            $query = person::where('category', 'Person');

            if ($request->person_type_id) {
                $query->where('person_type_id', $request->person_type_id);
            }

            $data = $query
                ->orderBy('display_order')
                ->get();

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
     *     path="/api/PersonById/{id}",
     *     summary="Get Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Person data"),
     *     @OA\Response(response=404, description="Data not found")
     * )
     */
    public function ById($id)
    {
        try {
            $data = person::where('category', 'Person')->find($id);

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
     *     path="/api/PersonInsert",
     *     summary="Insert a new Person",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="register_number", type="string"),
     *             @OA\Property(property="mobile_number", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="person_type_id", type="string"),
     *             @OA\Property(property="display_order", type="integer"),
     *             @OA\Property(property="created_by_id", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Created successfully")
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $data = new person();

            $data = $request->only($data->fillable);

            // Person Master only
            $data['category'] = 'Person';

            $result = person::create($data);

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
     *     path="/api/PersonUpdate",
     *     summary="Update an existing Person",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="register_number", type="string"),
     *             @OA\Property(property="mobile_number", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="person_type_id", type="string"),
     *             @OA\Property(property="display_order", type="integer"),
     *             @OA\Property(property="updated_by_id", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function Update(Request $request)
    {
        try {
            $data = new person();

            $data = $request->only($data->fillable);

            // Person Master only
            $data['category'] = 'Person';

            person::where('category', 'Person')
                ->findOrFail($data['id'])
                ->update($data);

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
     *     path="/api/PersonDelete/{id}",
     *     summary="Delete Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function Delete($id)
    {
        try {
            person::where('category', 'Person')
                ->findOrFail($id)
                ->delete();

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

    /**
     * Get Person Types used by Person category
     */
    public function PersonTypeFilterList()
    {
        try {
            $data = person_type::whereIn(
                'id',
                person::where('category', 'Person')
                    ->whereNotNull('person_type_id')
                    ->select('person_type_id')
                    ->distinct()
            )->orderBy('name')->get();

            return response($data);

        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }
}
