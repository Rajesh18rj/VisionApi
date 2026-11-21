<?php

namespace App\Http\ApiController;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Model\category;
use App\Http\Model\department;
use App\Http\Model\designation;
use App\Http\Model\person;
use App\Http\Model\person_face;
use App\Http\Model\user;
use App\Http\Model\user_login_info;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PersonController extends Controller
{
    public function __construct() {}

    /**
     * @OA\Get(
     *     path="/api/PersonList",
     *     summary="Get list of all Persons",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of Persons"
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
            $data = person::with(['department', 'category', 'designation'])->orderBy('created_on', 'desc')->get()->makeHidden(['face_embedding']);

            return response($data);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/PersonById/{id}",
     *     summary="Get Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *
     *         @OA\Schema(type="string", example="")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Person data"
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
            $data = person::find($id);
            if (empty($data)) {
                return response(['Type' => 's', 'Message' => 'Data Not Found']);
            }

            return response($data);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/PersonInsert",
     *      operationId="PersonInsert",
     *      tags={"Person"},
     *      summary="Insert a new Person",
     *      security={{"bearerAuth":{}}},
     *      description="PersonInsert",
     *
     *      @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *            required={"name", "gender" ,"register_number" ,"mobile_number" ,"email" ,"room_no" ,"image" ,"category_id","department_id" ,"designation_id","contact_person_name","contact_person_number","is_active", "display_order" , "created_by_id"},
     *
     *            @OA\Property(property="name", type="string", format="string", example=""),
     *            @OA\Property(property="gender", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="register_number", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="mobile_number", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="email", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="room_no", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="image", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="address", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="category_id", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="department_id", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="designation_id", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="contact_person_name", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="contact_person_number", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="is_active", type="boolean", example="true", nullable=true),
     *            @OA\Property(property="display_order", type="integer", format="int64", example=1, nullable=true),
     *            @OA\Property(property="created_by_id", type="string", format="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"),
     *         ),
     *      ),
     *
     *     @OA\Response(
     *          response=200, description="Success",
     *
     *          @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function Insert(Request $request)
    {
        try {
            $data = new person();
            $data = $request->only($data->fillable);
            if (person::where('register_number', $data['register_number'])->count() > 0) {
                return response(['Type' => 'E', 'Message' => 'Record already exits']);
            } else {
                if ($request->has('image') && ! empty($data['image'])) {
                    $data1 = explode(';', $data['image']);

                    if (isset($data1[0])) {
                        $data2 = explode('/', $data1[0]);

                        if (isset($data2[1])) {
                            $type = '.'.$data2[1];
                            $no = 'Person'.date('Ymd').'-'.date('His').'-'.person::count();

                            if ($data2[0] == 'data:image') {
                                $imagePath = ImageHelper::personImage($data['image'], $no, false, $type);
                                $data['image'] = env('Live_Url').$imagePath;
                            }
                        }
                    }
                }
                $result = person::create($data);

                return response(['Type' => 'S', 'Message' => 'Created successfully', 'Id' => $result['id']]);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/PersonUpdate",
     *     summary="Update an existing Person",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"id","name", "gender" ,"register_number" ,"mobile_number" ,"email" ,"room_no" ,"image" ,"category_id","department_id" ,"designation_id","contact_person_name","contact_person_number","is_active", "display_order","updated_by_id"},
     *
     *             @OA\Property(property="id", type="string",format="string", example=""),
     *             @OA\Property(property="name", type="string",format="string", example=""),
     *             @OA\Property(property="register_number", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="mobile_number", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="email", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="room_no", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="image", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="address", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="category_id", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="department_id", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="designation_id", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="contact_person_name", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="contact_person_number", type="string", format="string", example="", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example="true", nullable=true),
     *             @OA\Property(property="display_order", type="integer", format="int64", example="", nullable=true),
     *            @OA\Property(property="updated_by_id", type="string", format="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"),
     *         )
     *     ),
     *
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
            $data = new person();
            $data = $request->only($data->fillable);
            if (person::where('id', '!=', $data['id'])->where('register_number', $data['register_number'])->count() > 0) {
                return response(['Type' => 'E', 'Message' => 'Record already exits']);
            } else {
                if ($request->has('image') && ! empty($data['image'])) {
                    $data1 = explode(';', $data['image']);
                    if (isset($data1[0])) {
                        $data2 = explode('/', $data1[0]);
                        if (isset($data2[1])) {
                            $type = '.'.$data2[1];
                            $no = 'Person'.date('Ymd').'-'.date('His').'-'.person::count();
                            if ($data2[0] == 'data:image') {
                                ImageHelper::DeleteImage($data['image']);
                                $imagePath = ImageHelper::personImage($data['image'], $no, false, $type);
                                $data['image'] = env('Live_Url').$imagePath;
                            }
                        }
                    }
                }
                person::findOrFail($data['id'])->update($data);

                return response(['Type' => 'S', 'Message' => 'Updated successfully', 'Id' => $data['id']]);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/PersonDelete/{id}",
     *     summary="Delete a Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *
     *         @OA\Schema(type="string", example="")
     *     ),
     *
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
            $data = person::findOrFail($id);
            if ($data->image) {
                ImageHelper::DeleteImage($data['image']);
            }
            person::findOrFail($id)->delete();

            return response(['Type' => 'S', 'Message' => 'Deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Type' => 'E', 'Message' => 'Data Not Found'], 404);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/ImageInsert",
     *     operationId="ImageInsert",
     *     tags={"Person"},
     *     summary="Upload Person Face Image",
     *     description="Uploads a Base64 encoded face image, stores the image, and creates a record in the person_face table.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={
     *                 "person_id",
     *                 "camera_id",
     *                 "camera_unique_id",
     *                 "date",
     *                 "title",
     *                 "filename",
     *                 "image"
     *             },
     *
     *             @OA\Property(
     *                 property="person_id",
     *                 type="string",
     *                 format="uuid",
     *                 example="a21920b6-cfc1-4a23-a993-462f041971a8"
     *             ),
     *             @OA\Property(
     *                 property="camera_id",
     *                 type="string",
     *                 format="uuid",
     *                 example="a2315e33-13ee-4b29-ac3c-56366542947d"
     *             ),
     *             @OA\Property(
     *                 property="camera_unique_id",
     *                 type="string",
     *                 example="CAM001"
     *             ),
     *             @OA\Property(
     *                 property="person_no",
     *                 type="string",
     *                 nullable=true,
     *                 example="RN2345"
     *             ),
     *             @OA\Property(
     *                 property="date",
     *                 type="string",
     *                 format="date",
     *                 example="2026-07-23"
     *             ),
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 example="IN"
     *             ),
     *             @OA\Property(
     *                 property="filename",
     *                 type="string",
     *                 example="suriya-20260723-172857-033094-1.jpeg"
     *             ),
     *             @OA\Property(
     *                 property="image",
     *                 type="string",
     *                 format="byte",
     *                 description="Base64 encoded image with data URI prefix",
     *                 example="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD..."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="Type", type="string", example="S"),
     *             @OA\Property(property="Message", type="string", example="Created Successfully"),
     *             @OA\Property(
     *                 property="Data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="person_id", type="string", format="uuid"),
     *                 @OA\Property(property="camera_id", type="string", format="uuid"),
     *                 @OA\Property(property="camera_unique_id", type="string", example="CAM001"),
     *                 @OA\Property(property="person_no", type="string", example="RN2345"),
     *                 @OA\Property(property="date", type="string", example="2026-07-23"),
     *                 @OA\Property(property="title", type="string", example="IN"),
     *                 @OA\Property(property="image", type="string", example="http://192.168.29.62:8000/storage/attendance/2026-07-23/suriya-20260723-172857-033094-1.jpeg")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="Type", type="string", example="E"),
     *             @OA\Property(property="Message", type="string", example="SQLSTATE error")
     *         )
     *     )
     * )
     */
    public function ImageInsert(Request $request)
    {
        try {

            $request->validate([
                'person_id' => 'required',
                'camera_id' => 'required',
                'camera_unique_id' => 'required',
                'person_no' => 'nullable',
                'date' => 'required',
                'title' => 'required',
                'filename' => 'required',
                'image' => 'required',
            ]);

            // Remove extension if provided
            $filename = pathinfo($request->filename, PATHINFO_FILENAME);

            // Save image using provided filename
            $imagePath = ImageHelper::AttendanceImage(
                $request->image,
                $filename,
                false,
                ''
            );

            $imageUrl = env('Live_Url').$imagePath;

            $personFace = person_face::create([
                'person_id' => $request->person_id,
                'camera_id' => $request->camera_id,
                'camera_unique_id' => $request->camera_unique_id,
                'person_no' => $request->person_no,
                'date' => $request->date,
                'title' => $request->title,
                'image' => $imageUrl,
            ]);

            $person = person::find($request->person_id);

            $departmentName = DB::table('department')
                ->where('id', $person->department_id)
                ->value('name');

            $designationName = DB::table('designation')
                ->where('id', $person->designation_id)
                ->value('name');

            $cameraName = DB::table('camera')
                ->where('id', $request->camera_id)
                ->value('model_name');

            $departmentName = $departmentName ?? 'N/A';
            $designationName = $designationName ?? 'N/A';
            $cameraName = $cameraName ?? 'N/A';

            if (! $person) {
                return response()->json([
                    'Type' => 'E',
                    'Message' => 'Person not found',
                ], 404);
            }

            $departmentId = $person->department_id;

            $users = User::select('user.id', 'user.notification')
                ->join('user_role', 'user.user_role_id', '=', 'user_role.id')
                ->where('user_role.name', 'Notification')
                ->get();

            $userIds = [];

            foreach ($users as $user) {

                $notifications = json_decode($user->notification, true);

                if (! is_array($notifications)) {
                    continue;
                }

                foreach ($notifications as $notification) {

                    if (
                        isset($notification['id']) &&
                        $notification['id'] == $departmentId &&
                        (! isset($notification['is_active']) || $notification['is_active'])
                    ) {
                        $userIds[] = $user->id;
                        break;
                    }
                }
            }

            $subscriptions = user_login_info::whereIn('user_id', $userIds)
                ->whereNotNull('onesignal_subscription_id')
                ->where('onesignal_subscription_id', '!=', '')
                ->distinct()
                ->pluck('onesignal_subscription_id')
                ->toArray();

            if (empty($subscriptions)) {
                return response()->json([
                    'Type' => 'S',
                    'Message' => 'Person inserted. No active users found for notification.',
                    'Data' => $personFace,
                ]);
            }
            if (count($subscriptions) == 0) {

                return response()->json([
                    'Type' => 'S',
                    'Message' => 'No Active Devices',
                ]);

            }

            $url = rtrim(env('PWA_URL'), '/').'/person/'.$request->person_id;

            $attendanceType = strtolower($request->title) === 'in'
    ? 'Entry Notification'
    : 'Exit Notification';

            $action = strtolower($request->title) === 'in'
    ? 'entered'
    : 'exited';

            $message = $person->name
                .' - '.$departmentName
                .' - '.$designationName
                .' '.$action
                .' '.$cameraName;

            $response = Http::withHeaders([
                'Authorization' => 'Key '.env('ONESIGNAL_REST_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.onesignal.com/notifications', [

                'app_id' => env('ONESIGNAL_APP_ID'),

                'include_subscription_ids' => $subscriptions,

                'headings' => [
                    'en' => $attendanceType,
                ],

                'contents' => [
                    'en' => $message,
                ],

                'big_picture' => $imageUrl,

                'url' => $url,

                'data' => [
                    'type' => 'person_detection',
                    'person_id' => $person->id,
                    'person_name' => $person->name,
                    'department_id' => $departmentId,
                    'camera_id' => $request->camera_id,
                    'camera_unique_id' => $request->camera_unique_id,
                    'image' => $imageUrl,
                ],
            ]);

            return response()->json([

                'Type' => 'S',

                'Message' => 'Created Successfully And Notification Sent',

                'Data' => $personFace,

                'NotificationUrl' => $url,

                'OneSignal' => $response->json(),

            ]);

        } catch (\Exception $e) {

            return response()->json([
                'Type' => 'E',
                'Message' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * @OA\Post(
     *      path="/api/PersonBulkUpload",
     *      operationId="PersonBulkUpload",
     *      tags={"Person"},
     *      summary="Bulk Upload Persons From Excel",
     *      security={{"bearerAuth":{}}},
     *      description="Upload Excel (.xlsx, .xls, .csv) file and create persons in bulk",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *
     *              @OA\Schema(
     *                  required={"file"},
     *
     *                  @OA\Property(
     *                      property="file",
     *                      type="string",
     *                      format="binary",
     *                      description="Excel File (.xlsx, .xls, .csv)"
     *                  ),
     *                  @OA\Property(
     *                      property="created_by_id",
     *                      type="string",
     *                      example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"
     *                  )
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Upload Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="Type",
     *                  type="string",
     *                  example="S"
     *              ),
     *              @OA\Property(
     *                  property="Message",
     *                  type="string",
     *                  example="Upload completed"
     *              ),
     *              @OA\Property(
     *                  property="Inserted",
     *                  type="integer",
     *                  example=100
     *              ),
     *              @OA\Property(
     *                  property="Duplicate",
     *                  type="integer",
     *                  example=5
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="Type",
     *                  type="string",
     *                  example="E"
     *              ),
     *              @OA\Property(
     *                  property="Message",
     *                  type="string",
     *                  example="Excel file required"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="Type",
     *                  type="string",
     *                  example="E"
     *              ),
     *              @OA\Property(
     *                  property="Message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function PersonBulkUpload(Request $request)
    {
        try {

            if (! $request->hasFile('file')) {
                return response([
                    'Type' => 'E',
                    'Message' => 'Excel file required',
                ]);
            }

            $spreadsheet = IOFactory::load(
                $request->file('file')->getRealPath()
            );

            $rows = $spreadsheet->getActiveSheet()->toArray();

            $success = 0;
            $duplicate = 0;

            foreach ($rows as $index => $row) {

                // Skip Header
                if ($index == 0) {
                    continue;
                }

                $registerNumber = trim($row[2] ?? '');

                if (empty($registerNumber)) {
                    continue;
                }

                if (
                    person::where('register_number', $registerNumber)->exists()
                ) {
                    $duplicate++;

                    continue;
                }

                $categoryName = trim($row[6] ?? '');

                $category = category::whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($categoryName)]
                )->first();
                if (! $category) {
                    $category = new category();
                    $category->name = $categoryName;
                    $category->code = $categoryName;
                    $category->created_by_id = $request->created_by_id;
                    $category->save();
                }

                $departmentName = trim($row[7] ?? '');

                $department = department::whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($departmentName)]
                )->first();

                if (! $department) {
                    $department = new department();
                    $department->name = $departmentName;
                    $department->code = $departmentName;
                    $department->category_id = $category?->id;
                    $department->created_by_id = $request->created_by_id;
                    $department->save();
                }

                $designationName = trim($row[8] ?? '');

                $designation = designation::whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($designationName)]
                )->where('department_id', $department?->id)->first();

                if (! $designation) {
                    $designation = new designation();
                    $designation->name = $designationName;
                    $designation->code = $designationName;
                    $designation->department_id = $department?->id;
                    $designation->created_by_id = $request->created_by_id;
                    $designation->save();
                }

                person::create([
                    'name' => trim($row[0] ?? ''),
                    'gender' => trim($row[1] ?? ''),
                    'register_number' => trim($row[2] ?? ''),
                    'mobile_number' => trim($row[3] ?? ''),
                    'email' => trim($row[4] ?? ''),
                    'address' => trim($row[5] ?? ''),
                    'category_id' => $category?->id,
                    'department_id' => $department?->id,
                    'designation_id' => $designation?->id,
                    'contact_person_name' => trim($row[9] ?? ''),
                    'contact_person_number' => trim($row[10] ?? ''),
                    'is_active' => 1,
                    'display_order' => 1,
                    'created_by_id' => $request->created_by_id,
                ]);

                $success++;
            }

            return response([
                'Type' => 'S',
                'Message' => 'Upload completed',
                'Inserted' => $success,
                'Duplicate' => $duplicate,
            ]);
        } catch (\Exception $e) {

            return response([
                'Type' => 'E',
                'Message' => $e->getMessage(),
            ]);
        }
    }
}
