<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\user_role;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserRoleController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/UserRoleList",
     *     tags={"User Role"},
     *     summary="Get list of user roles (excluding Super Admin)",
     *     @OA\Response(
     *         response=200,
     *         description="List of user roles"
     *     )
     * )
     */
    public function List()
    {
        try {

            // $directories = Storage::disk('ftp')->allFiles('Face Recognition');
            // foreach ($directories as $key => $value) {
            //     $filename = str_replace('Face Recognition/', '', $value);
            //     $part = Str::after($filename, '_');
            //     $code = Str::before($part, '(');
            //     $tt = $filename;
            // }
            $data = user_role::where('name', '!=', 'Super Admin')->get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/UserRoleById/{id}",
     *     tags={"User Role"},
     *     summary="Get user role by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User role not found"
     *     )
     * )
     */
    public function ById($id)
    {
        try{
            $data = user_role::find($id);
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
     *     path="/api/UserRoleInsert",
     *     tags={"User Role"},
     *     summary="Insert a new user role",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Manager")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Record already exists"
     *     )
     * )
     */
    public function Insert(Request $request)
    {
        try{
            $data = new user_role();
            $data = $request->only($data->fillable);
            if (user_role::where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                $result = user_role::create($data);
                return response(["Type" => "S", "Message" => "Created successfully", "Id" => $result["id"]]);
            }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/UserRoleUpdate",
     *     tags={"User Role"},
     *     summary="Update an existing user role",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *             @OA\Property(property="id", type="integer", example=3),
     *             @OA\Property(property="name", type="string", example="Supervisor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Record already exists"
     *     )
     * )
     */
    public function Update(Request $request)
    {
        try{
            $data = new user_role();
            $data =  $request->only($data->fillable);
            if (user_role::where('id', '!=', $data["id"])->where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                user_role::findOrFail($data['id'])->update($data);
                return response(["Type" => "S", "Message" => "Updated successfully", "Id" => $data['id']]);
            }
         } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/UserRoleDelete/{id}",
     *     tags={"User Role"},
     *     summary="Delete a user role by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User role not found"
     *     )
     * )
     */
    public function Delete($id)
    {
        try{
            user_role::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }

    }
}
