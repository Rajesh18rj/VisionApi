<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\session;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/SessionList",
     *     summary="Get list of all sessions",
     *     tags={"Session"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of sessions"
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
            $data = session::get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/SessionById/{id}",
     *     summary="Get session by ID",
     *     tags={"Session"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="session ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="session data"
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
            $data = session::find($id);
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
     *      path="/api/SessionInsert",
     *      operationId="sessionInsert",
     *      tags={"Session"},
     *      summary="Insert a new session",
     *      security={{"bearerAuth":{}}},
     *      description="sessionInsert",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "start_time" , "end_time" , "created_by_id"},
     *            @OA\Property(property="name", type="string", format="string", example="Test"),
     *            @OA\Property(property="start_time", type="string", format="string", example=""),
     *            @OA\Property(property="end_time", type="string", format="string", example=""),
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
            $data = new session();
            $data = $request->only($data->fillable);
            if (session::where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
                // if(session::whereBetween('start_time', [$data["start_time"], $data["end_time"]])->orWhereBetween('end_time', [$data["start_time"], $data["end_time"]])->count() > 0){
                //     return response(["Type" => "E", "Message" => "Session Time already exits"]);
                // }
                $data["start_time"] = Carbon::parse($data["start_time"]);
                $data["end_time"] = Carbon::parse($data["end_time"]);

                $conflictExists = session::where(function ($query) use ($data) {
                    $query->where('start_time', '<', $data["end_time"])
                        ->where('end_time', '>', $data["start_time"]);
                })->exists();

                if ($conflictExists) {
                    return response([
                        "Type" => "E",
                        "Message" => "Session time already exists"
                    ]);
                }
                $result = session::create($data);
                return response(["Type" => "S", "Message" => "Created successfully", "Id" => $result["id"]]);
            }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/SessionUpdate",
     *     summary="Update an existing session",
     *     tags={"Session"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id" , "name", "start_time" , "end_time" , "updated_by_id"},
     *            @OA\Property(property="id", type="string", example="1"),
     *            @OA\Property(property="name", type="string", format="string", example="Test"),
     *            @OA\Property(property="start_time", type="string", format="string", example=""),
     *            @OA\Property(property="end_time", type="string", format="string", example=""),
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
            $data = new session();
            $data =  $request->only($data->fillable);
            if (session::where('id', '!=', $data["id"])->where('name', $data["name"])->count() > 0) {
                return response(["Type" => "E", "Message" => "Record already exits"]);
            } else {
               $data["start_time"] = Carbon::parse($data["start_time"]);
                $data["end_time"] = Carbon::parse($data["end_time"]);

                $conflictExists = session::where('id', '!=', $data["id"])
                    ->where(function ($query) use ($data) {
                        $query->where('start_time', '<', $data["end_time"])
                            ->where('end_time', '>', $data["start_time"]);
                    })
                    ->exists();

                if ($conflictExists) {
                    return response([
                        "Type" => "E",
                        "Message" => "Session time already exists"
                    ]);
                }
                session::findOrFail($data['id'])->update($data);
                return response(["Type" => "S", "Message" => "Updated successfully", "Id" => $data['id']]);
            }
         } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/SessionDelete/{id}",
     *     summary="Delete a session by ID",
     *     tags={"Session"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="session ID",
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
            session::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }

    }
}
