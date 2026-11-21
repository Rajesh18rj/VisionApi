<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\user_login_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Your Camera model
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    // /**
    //  * @OA\Post(
    //  *     path="/api/animal-detected",
    //  *     summary="Trigger Animal Detection Notification",
    //  *     description="Called by the Python AI service whenever an animal is detected. Sends a push notification to all active OneSignal subscriptions.",
    //  *     tags={"Notification"},
    //  *
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *
    //  *         @OA\JsonContent(
    //  *             required={
    //  *                 "title",
    //  *                 "message",
    //  *                 "animal_face_id"
    //  *             },
    //  *
    //  *             @OA\Property(
    //  *                 property="title",
    //  *                 type="string",
    //  *                 example="Animal Detected"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="message",
    //  *                 type="string",
    //  *                 example="Tiger detected near Camera 01"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="animal_face_id",
    //  *                 type="string",
    //  *                 example="a7e38bc4-93c7-4dcb-9a34-270e72f89866"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="animal_name",
    //  *                 type="string",
    //  *                 example="Tiger"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="camera_name",
    //  *                 type="string",
    //  *                 example="Camera 01"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="confidence",
    //  *                 type="number",
    //  *                 format="float",
    //  *                 example=98.75
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="detected_time",
    //  *                 type="string",
    //  *                 example="2026-07-16 12:45:10"
    //  *             ),
    //  *             @OA\Property(
    //  *                 property="image",
    //  *                 type="string",
    //  *                 example="https://yourdomain.com/uploads/tiger.jpg"
    //  *             )
    //  *         )
    //  *     ),
    //  *
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Notification sent successfully"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=400,
    //  *         description="Validation Error"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         description="Internal Server Error"
    //  *     )
    //  * )
    //  */
    // public function animalDetected(Request $request)
    // {
    //     try {

    //         $request->validate([
    //             'title' => 'required|string',
    //             'message' => 'required|string',
    //             'animal_face_id' => 'required|string',
    //         ]);

    //         $subscriptions = user_login_info::whereNotNull('onesignal_subscription_id')
    //             ->where('onesignal_subscription_id', '!=', '')
    //             ->distinct()
    //             ->pluck('onesignal_subscription_id')
    //             ->toArray();

    //         if (count($subscriptions) == 0) {

    //             return response()->json([
    //                 'Type' => 'S',
    //                 'Message' => 'No Active Devices',
    //             ]);

    //         }

    //         $url = rtrim(env('PWA_URL'), '/'). '/animals/'. $request->animal_face_id;

    //             // http://localhost:5173/employees/06003d3c-34dd-43f0-85dd-213a6b6eb8d1

    //             // http://localhost:5173/animals/a2558c4c-4053-4f5e-8ffc-e70e9756edf1

    //         $response = Http::withHeaders([
    //             'Authorization' => 'Key '.env('ONESIGNAL_REST_API_KEY'),
    //             'Content-Type' => 'application/json',
    //         ])->post('https://api.onesignal.com/notifications', [

    //             'app_id' => env('ONESIGNAL_APP_ID'),

    //             'include_subscription_ids' => $subscriptions,

    //             'headings' => [
    //                 'en' => $request->title,
    //             ],

    //             'contents' => [
    //                 'en' => $request->message,
    //             ],

    //             'big_picture' => $request->image,

    //             'url' => $url,

    //             'data' => [

    //                 'type' => 'animal_detection',

    //                 'animal_face_id' => $request->animal_face_id,

    //                 'animal_name' => $request->animal_name,

    //                 'camera_name' => $request->camera_name,

    //                 'confidence' => $request->confidence,

    //                 'detected_time' => $request->detected_time,

    //                 'image' => $request->image,

    //             ],

    //         ]);

    //         return response()->json([

    //             'Type' => 'S',

    //             'Message' => 'Notification Sent',

    //             'NotificationUrl' => $url,

    //             'OneSignal' => $response->json(),

    //         ]);

    //     } catch (\Exception $e) {

    //         return response()->json([

    //             'Type' => 'E',

    //             'Message' => $e->getMessage(),

    //         ], 500);
    //     }
    // }

    /**
     * @OA\Post(
     *     path="/api/UpdateSubscription",
     *     summary="Update OneSignal Subscription ID",
     *     tags={"Notification"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"device_id","onesignal_subscription_id"},
     *
     *             @OA\Property(
     *                 property="device_id",
     *                 type="string",
     *                 example="ANDROID-123456"
     *             ),
     *             @OA\Property(
     *                 property="onesignal_subscription_id",
     *                 type="string",
     *                 example="8c0a54f6-8b6d-4a42-9e40-2f4fd7b8d4f1"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Subscription updated successfully"),
     *     @OA\Response(response=404, description="Device not found")
     * )
     */
    public function UpdateSubscription(Request $request)
    {
        try {

            $request->validate([
                'device_id' => 'required',
                'onesignal_subscription_id' => 'required',
            ]);

            $device = user_login_info
            ::where('user_id', Auth::id())
                ->where('device_id', $request->device_id)
                ->first();

            if (! $device) {

                return response()->json([
                    'Type' => 'E',
                    'Message' => 'Device not found.',
                ], 404);

            }

            $device->onesignal_subscription_id = $request->onesignal_subscription_id;
            $device->last_seen = now();
            $device->save();

            return response()->json([
                'Type' => 'S',
                'Message' => 'Subscription updated successfully.',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'Type' => 'E',
                'Message' => $e->getMessage(),
            ], 500);

        }
    }
}
