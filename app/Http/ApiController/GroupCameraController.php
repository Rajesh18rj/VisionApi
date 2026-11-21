<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\group_camera;
use App\Http\Model\group_camera_detail;
use App\Http\Model\GroupCamera;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupCameraController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/GroupCameraList",
     *     summary="Get list of all GroupCameras",
     *     tags={"GroupCamera"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of GroupCameras"
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
            $data = group_camera::orderBy('created_on', 'desc')->get();
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/GroupCameraById/{id}",
     *     summary="Get GroupCamera by ID",
     *     tags={"GroupCamera"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="GroupCamera ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="GroupCamera data"
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
            $data = group_camera::with(['details'])->find($id);
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
     *     path="/api/GroupCameraInsert",
     *     tags={"GroupCamera"},
     *     summary="Insert GroupCamera",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example=""),
     *
     *              @OA\Property(property="details",type="array",
     *              @OA\Items(
     *              @OA\Property(property="camera_id", type="string", example="Manoj"),
     *              @OA\Property(property="created_by_id", type="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7")
     *               )
     *               ),
     *             @OA\Property(property="created_by_id", type="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Created successfully"),
     *     @OA\Response(response=500, description="Error")
     * )
     */
    public function Insert(Request $request)
    {
        try {

            //  Remove child arrays from main data
            $data = $request->except(['details']);


            if (empty($data['name'])) {
                return response(["Type" => "E", "Message" => "name is required"]);
            }

            if (group_camera::where('name', $data["name"])->exists()) {
                return response(["Type" => "E", "Message" => "Group camera already exists"]);
            }

            DB::beginTransaction();

            $result = group_camera::create($data);

            if (!empty($request->details)) {
                foreach ($request->details as $detail) {

                    if (empty($detail['camera_id']) || empty($detail['created_by_id'])) {
                        throw new \Exception("Invalid detail data");
                    }

                    $result->details()->create($detail);
                }
            }

            DB::commit();

            return response([
                "Type" => "S",
                "Message" => "Created successfully",
                "Id" => $result->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }

 /**
     * @OA\Put(
     *     path="/api/GroupCameraUpdate",
     *     tags={"GroupCamera"},
     *     summary="Update GroupCamera",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","name"},
     *
     *             @OA\Property(property="id", type="string", example="group-camera-uuid"),
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *
     *             @OA\Property(property="details", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", nullable=true),
     *                     @OA\Property(property="camera_id", type="string"),
     *                 )
     *             ),
     *
     *             @OA\Property(property="updated_by_id", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully"),
     *     @OA\Response(response=500, description="Error")
     * )
     */
    public function Update(Request $request)
    {
        try {

            if (empty($request->id)) {
                return response(["Type" => "E", "Message" => "id is required"]);
            }

            $group_camera = group_camera::find($request->id);
            if (!$group_camera) {
                return response(["Type" => "E", "Message" => "Group camera not found"]);
            }

            //  Remove child arrays
            $data = $request->except(['details']);


            DB::beginTransaction();

            $group_camera->update($data);



            // =====================================================
            //  CONTACTS SYNC
            // =====================================================
            if (isset($request->details)) {

                $existingIds = $group_camera->details()->pluck('id')->toArray();
                $incomingIds = collect($request->details)->pluck('id')->filter()->toArray();

                $toDelete = array_diff($existingIds, $incomingIds);
                $group_camera->details()->whereIn('id', $toDelete)->delete();

                foreach ($request->details as $detail) {

                    if (empty($detail['camera_id'])) {
                        throw new \Exception("Invalid detail data");
                    }

                    if (!empty($detail['id'])) {
                        $group_camera->details()->where('id', $detail['id'])->update($detail);
                    } else {
                        $group_camera->details()->create($detail);
                    }
                }
            }

            DB::commit();

            return response([
                "Type" => "S",
                "Message" => "Updated successfully"
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/GroupCameraDelete/{id}",
     *     summary="Delete a GroupCamera by ID",
     *     tags={"GroupCamera"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="GroupCamera ID",
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
            $group = group_camera_detail::where('group_camera_id', $id)->get();
            foreach($group as $g){
                $g->delete();
            }
            group_camera::findOrFail($id)->delete();
            return response(["Type" => "S", "Message" => "Deleted successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
        return response(["Type" => "E", "Message" => $e->getMessage()]);
    }



    }

    /**
     * @OA\Get(
     *     path="/api/GroupCameraDetailList",
     *     summary="Get list of all Group Camera Details",
     *     tags={"GroupCamera"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of Group Camera Details"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function GroupCameraDetailList()
    {
        try {
             $qry = "
                SELECT
                c.id,
                CONCAT(c.asset_no , ' - ' , c.ip_address) as name,
                c.asset_no,
                c.model_name,
                c.`type`,
                c.ip_address
            FROM
                camera c
            WHERE
                NOT EXISTS (
                    SELECT 1
                    FROM group_camera_detail h
                    WHERE FIND_IN_SET(c.id, h.camera_id ) > 0
                );

            ";
            $data = DB::select($qry);
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }
}
