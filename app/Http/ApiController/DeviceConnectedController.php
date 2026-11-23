<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\device_connected;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

/**
 * @OA\Tag(
 *     name="DeviceConnected",
 *     description="Device Connected API"
 * )
 */
class DeviceConnectedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/DeviceConnectedList",
     *     tags={"DeviceConnected"},
     *     summary="Get Device Connected List",
     *     @OA\Response(
     *         response=200,
     *         description="Device Connected List"
     *     )
     * )
     */
    public function List()
    {
        return response(device_connected::all());
    }

    /**
     * @OA\Get(
     *     path="/api/DeviceConnectedById/{id}",
     *     tags={"DeviceConnected"},
     *     summary="Get Device Connected By Id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device Connected Data"
     *     )
     * )
     */
    public function ById($id)
    {
        try {
            $data = device_connected::findOrFail($id);

            return response($data);
        } catch (ModelNotFoundException $e) {
            return response([
                "Type" => "E",
                "Message" => "Data Not Found"
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/DeviceConnectedInsert",
     *     tags={"DeviceConnected"},
     *     summary="Insert Device Connected",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="date", type="string", format="date", example="2026-11-19"),
     *             @OA\Property(property="camera_name", type="string", example="Camera 1"),
     *             @OA\Property(property="camera_ip", type="string", example="192.168.1.100"),
     *             @OA\Property(property="connecting_status", type="string", example="Connected"),
     *             @OA\Property(property="created_by_id", type="string", example="USER_UUID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Created Successfully"
     *     )
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $data = new device_connected();

            $data = $request->only($data->fillable);

            $result = device_connected::create($data);

            return response([
                "Type" => "S",
                "Message" => "Created successfully",
                "Id" => $result->id
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
     *     path="/api/DeviceConnectedUpdate",
     *     tags={"DeviceConnected"},
     *     summary="Update Device Connected",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", example="DEVICE_CONNECTED_UUID"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-11-19"),
     *             @OA\Property(property="camera_name", type="string", example="Camera 1"),
     *             @OA\Property(property="camera_ip", type="string", example="192.168.1.100"),
     *             @OA\Property(property="connecting_status", type="string", example="Disconnected"),
     *             @OA\Property(property="updated_by_id", type="string", example="USER_UUID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated Successfully"
     *     )
     * )
     */
    public function Update(Request $request)
    {
        try {
            $data = new device_connected();

            $data = $request->only($data->fillable);

            device_connected::findOrFail($data['id'])->update($data);

            return response([
                "Type" => "S",
                "Message" => "Updated successfully"
            ]);
        } catch (ModelNotFoundException $e) {
            return response([
                "Type" => "E",
                "Message" => "Data Not Found"
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
     *     path="/api/DeviceConnectedDelete/{id}",
     *     tags={"DeviceConnected"},
     *     summary="Delete Device Connected",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted Successfully"
     *     )
     * )
     */
    public function Delete($id)
    {
        try {
            device_connected::findOrFail($id)->delete();

            return response([
                "Type" => "S",
                "Message" => "Deleted successfully"
            ]);
        } catch (ModelNotFoundException $e) {
            return response([
                "Type" => "E",
                "Message" => "Data Not Found"
            ]);
        } catch (Exception $e) {
            return response([
                "Type" => "E",
                "Message" => $e->getMessage()
            ]);
        }
    }
}