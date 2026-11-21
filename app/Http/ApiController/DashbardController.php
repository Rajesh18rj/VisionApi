<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\user;
use App\Http\Model\camera;
use App\Http\Model\group_camera_detail;
use App\Http\Model\nvr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashbardController extends Controller
{
    public function __construct() {}

    /**
     * @OA\Post(
     *      path="/api/DashBoardStaffList",
     *      operationId="DashBoardStaffList",
     *      tags={"DashBoard"},
     *      summary="Get dashboard data for staff",
     *      security={{"bearerAuth":{}}},
     *      description="DashBoardStaffList",
     *
     *      @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *            required={"gender", "present", "department_id"},
     *            @OA\Property(property="gender", type="string", format="string", example="Male"),
     *            @OA\Property(property="present", type="integer", example="1"),
     *            @OA\Property(property="department_id", type="string", format="string", example="", nullable=true),
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
    public function DashBoardStaffList(Request $request)
    {
        try {
            $codition = "";
            $Vcodition = "";
            $wherecodition = "";
            $datas = $request->all();
            if ($datas["gender"] != null || $datas["gender"] != '') {
                $codition .= " AND p.gender = '" . $datas["gender"] . "'";
                $Vcodition .= " AND p.gender = '" . $datas["gender"] . "'";
            }
            if ($datas["department_id"] != null || $datas["department_id"] != '') {
                $codition .= " AND p.department_id = '" . $datas["department_id"] . "'";
                $Vcodition .= " AND p.department_id = '" . $datas["department_id"] . "'";
                $wherecodition .= " AND p.department_id = '" . $datas["department_id"] . "'";
            }
            if($datas["present"] != null || $datas["present"] != ''){
                if($datas["present"] == 1){
                    $codition .= " AND EXISTS (
                                    SELECT 1
                                    FROM person_face pf
                                    WHERE pf.person_id = p.id
                                    AND pf.`date` = CURDATE()
                                ) ";
                }
                else if($datas["present"] == 0){
                    $codition .= " AND NOT EXISTS (
                                    SELECT 1
                                    FROM person_face pf
                                    WHERE pf.person_id = p.id
                                    AND pf.`date` = CURDATE()
                                )";
                }
            }


            $qry = "
                select
                    p.id,
                    p.name,
                    p.email,
                    p.mobile_number,
                    p.address,
                    p.register_number,
                    p.gender,
                    p.image,
                    p.id,
                    d.name AS department_name,
                    b.name AS designation_name,
                    EXISTS (
                        SELECT 1
                        FROM person_face pf
                        WHERE pf.person_id = p.id
                        AND pf.`date` = CURDATE()
                    ) as present

                FROM person p
                INNER JOIN department d ON d.id = p.department_id
                left outer join designation b ON b.id = p.designation_id
                WHERE
                    p.is_active = 1
                     " . $codition . "

            ";

            $qry1 = "
                SELECT
                    COUNT(DISTINCT p.id) AS gender_staff,
                    COUNT(DISTINCT pf.person_id) AS present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                     " . $Vcodition . "

            ";

             $count = DB::select($qry1);

            $qry2 = '
                SELECT
                    COUNT(DISTINCT p.id) AS total_staff,
                    COUNT(DISTINCT pf.person_id) AS total_present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS total_absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                    '.$wherecodition.'
            ';
            $Totalcount = DB::select($qry2);

            $data = DB::select($qry);

            $result = [
                'count' => $count,
                'Totalcount' => $Totalcount,
                'data' => $data,
            ];

            return response($result);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/DashBoardStudentDetailList/{person_id}",
     *     summary="Get Student Detail by ID",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="person_id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *
     *         @OA\Schema(type="string", example="")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category data"
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
    public function DashBoardStudentDetailList($person_id)
    {
        try {
            $qry = "
                SELECT
                    c.asset_no,
                    c.model_name,
                    c.type,
                    COALESCE(pf.created_on) AS datatime
                FROM person_face pf
                INNER JOIN camera c
                    ON c.id = pf.camera_id
                WHERE pf.person_id = '$person_id'
                and
                    pf.date = CURDATE()
                order by
                    pf.created_on DESC

            ";
            $data = DB::select($qry);

            return response($data);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/CameraPing",
     *     tags={"Camera"},
     *     summary="Ping all CCTV cameras",
     *     description="Triggers the FastAPI service to ping all CCTV cameras. Updates the camera status in the database and returns the operation result.",
     *     operationId="pingCamera",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Camera status updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Camera status updated successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Python service unavailable",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Python service unavailable")
     *         )
     *     )
     * )
     */
    public function pingCamera()
    {
        $response = Http::withBasicAuth(
            env('FASTAPI_USERNAME'),
            env('FASTAPI_PASSWORD')
        )
            ->timeout(300)
            ->get(env('FASTAPI_URL').'/api/ping-status');

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Python service unavailable',
            ], 500);
        }

        $result = $response->json();

        // if($result){
        //     return response()->json([
        //     'success' => true,
        //     'data' => $result,
        // ]);
        // }

        $failedCameras = $result['response']['camera']['failed_cameras'] ?? [];

        foreach ($failedCameras as $camera) {

            camera::where('id', $camera['id'])->update([
                'status' => 0,
                'is_working' => 0,
                'last_working_on' => now(),
            ]);
        }

        $failedNvrs = $result['reponse']['nvr']['failed_cameras'] ?? [];

        foreach ($failedNvrs as $nvr) {

            nvr::where('id', $nvr['id'])->update([
                'status' => 0,
                'is_working' => 0,
                'last_working_on' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Camera status updated successfully.',
            'data' => $result,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/DashBoardStudentViewById/{person_id}",
     *     summary="Get Student Detail by ID",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="person_id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category data"
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
    public function DashBoardStudentViewById($person_id)
    {
        try{
           $qry = "
                select
                    c.asset_no,
                    c.model_name,
                    c.type,
                    COALESCE(pf.created_on) AS datatime,
                    p.name,
                    p.contact_person_name,
                    p.contact_person_number,
                    p.gender,
                    p.image,
                    p.register_number,
                    p.mobile_number,
                    d.name as department_name,
                    d2.name as designation_name

                from
                    person_face pf
                inner join
                    person p
                on
                    p.id = pf.person_id
                inner join
                    camera c
                on
                    c.id = pf.camera_id
                left outer join
                    department d
                on
                    d.id = p.department_id
                left outer join
                    designation d2
                on
                    d2.id = p.designation_id
                WHERE
                    pf.person_id = '$person_id'
                and
                    pf.date = CURDATE()
                order by
                    pf.created_on DESC
            ";
            $data = DB::select($qry);
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    // /**
    //  * @OA\Post(
    //  *      path="/api/DashBoardNotificationList",
    //  *      operationId="DashBoardNotificationList",
    //  *      tags={"DashBoard"},
    //  *      summary="Get dashboard data for staff",
    //  *      security={{"bearerAuth":{}}},
    //  *      description="DashBoardNotificationList",
    //  *      @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *            required={"user_id" , "gender" , "department_id"},
    //  *            @OA\Property(property="user_id", type="string", format="string", example=""),
    //  *            @OA\Property(property="gender", type="string", format="string", example="Male"),
    //  *            @OA\Property(property="department_id", type="string", format="string", example="", nullable=true),
    //  *         ),
    //  *      ),
    //  *     @OA\Response(
    //  *          response=200, description="Success",
    //  *          @OA\JsonContent(
    //  *             @OA\Property(property="status", type="integer", example=""),
    //  *             @OA\Property(property="data",type="object")
    //  *          )
    //  *       )
    //  *  )
    //  */
    // public function DashBoardNotificationList(Request $request)
    // {
    //     try{

    //         $codition = "";
    //         $datas = $request->all();
    //         $department_id = $datas["department_id"] ?? null;
    //         if ($datas["gender"] != null || $datas["gender"] != '') {
    //             $codition .= " AND p.gender = '" . $datas["gender"] . "'";
    //         }


    //         if($department_id == "all" || $department_id == ''){

    //                 $user = user::findOrFail($datas["user_id"]);

    //                 $data = json_decode($user->notification, true);

    //                 $ids = array_column($data ?? [], 'id');

    //                 $ids = array_map('trim', $ids);

    //                 if (empty($ids)) {
    //                     return [];
    //                 }

    //                 $result = "('" . implode("', '", $ids) . "')";

    //                 $qry = "
    //             select
    //                 p.id,
    //                 p.name,
    //                 p.email,
    //                 p.mobile_number,
    //                 p.address,
    //                 p.register_number,
    //                 p.gender,
    //                 p.image,
    //                 p.id,
    //                 d.name AS department_name,
    //                 b.name AS designation_name

    //             FROM person p
    //             INNER JOIN department d ON d.id = p.department_id
    //             left outer join designation b ON b.id = p.designation_id
    //             WHERE
    //                 p.is_active = 1
    //                 " . $codition . "
    //                 AND p.department_id IN $result
    //                 AND EXISTS (
    //                     SELECT 1
    //                     FROM person_face pf
    //                     WHERE pf.person_id = p.id
    //                     AND pf.`date` = CURDATE()
    //                 );
    //         ";

    //         $qry1 = "
    //             SELECT
    //                 COUNT(DISTINCT p.id) AS gender_staff,
    //                 COUNT(DISTINCT pf.person_id) AS present_staff,
    //                 COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS absent_staff
    //             FROM
    //                 person p
    //             LEFT JOIN
    //                 person_face pf
    //             ON
    //                 pf.person_id = p.id
    //             AND
    //                 pf.`date` = CURDATE()
    //             WHERE
    //                 p.is_active = 1
    //                 " . $codition . "
    //                 AND p.department_id IN $result

    //         ";
    //         $count = DB::select($qry1);

    //         $qry2 = "
    //             SELECT
    //                 COUNT(DISTINCT p.id) AS total_staff,
    //                 COUNT(DISTINCT pf.person_id) AS total_present_staff,
    //                 COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS total_absent_staff
    //             FROM
    //                 person p
    //             LEFT JOIN
    //                 person_face pf
    //             ON
    //                 pf.person_id = p.id
    //             AND
    //                 pf.`date` = CURDATE()
    //             WHERE
    //                 p.is_active = 1
    //                 AND p.department_id IN $result
    //         ";
    //         $Totalcount = DB::select($qry2);

    //         $data = DB::select($qry);

    //         $result = [
    //             "count" => $count,
    //             "Totalcount" => $Totalcount,
    //             "data" => $data
    //         ];
    //         return response($result);

    //         }
    //         else{
    //             $qry = "
    //             select
    //                 p.id,
    //                 p.name,
    //                 p.email,
    //                 p.mobile_number,
    //                 p.address,
    //                 p.register_number,
    //                 p.gender,
    //                 p.image,
    //                 p.id,
    //                 d.name AS department_name,
    //                 b.name AS designation_name

    //             FROM person p
    //             INNER JOIN department d ON d.id = p.department_id
    //             left outer join designation b ON b.id = p.designation_id
    //             WHERE
    //                 p.is_active = 1
    //                 AND p.department_id = '$department_id'
    //                 " . $codition . "
    //                 AND EXISTS (
    //                     SELECT 1
    //                     FROM person_face pf
    //                     WHERE pf.person_id = p.id
    //                     AND pf.`date` = CURDATE()
    //                 );
    //         ";

    //         $qry1 = "
    //             SELECT
    //                 COUNT(DISTINCT p.id) AS gender_staff,
    //                 COUNT(DISTINCT pf.person_id) AS present_staff,
    //                 COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS absent_staff
    //             FROM
    //                 person p
    //             LEFT JOIN
    //                 person_face pf
    //             ON
    //                 pf.person_id = p.id
    //             AND
    //                 pf.`date` = CURDATE()
    //             WHERE
    //                 p.is_active = 1
    //                 AND p.department_id = '$department_id'
    //                 " . $codition . "

    //         ";
    //         $count = DB::select($qry1);

    //         $qry2 = "
    //             SELECT
    //                 COUNT(DISTINCT p.id) AS total_staff,
    //                 COUNT(DISTINCT pf.person_id) AS total_present_staff,
    //                 COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS total_absent_staff
    //             FROM
    //                 person p
    //             LEFT JOIN
    //                 person_face pf
    //             ON
    //                 pf.person_id = p.id
    //             AND
    //                 pf.`date` = CURDATE()
    //             WHERE
    //                 p.is_active = 1
    //                 AND p.department_id = '$department_id'
    //         ";
    //         $Totalcount = DB::select($qry2);

    //         $data = DB::select($qry);

    //         $result = [
    //             "count" => $count,
    //             "Totalcount" => $Totalcount,
    //             "data" => $data
    //         ];
    //         return response($result);

    //         }
    //     } catch (Exception $e) {
    //         return response(["Type" => "E", "Message" => $e->getMessage()]);
    //     }
    // }

    /**
     * @OA\Get(
     *     path="/api/DashBoardNotificationList/{user_id}/{gender}/{department_id}",
     *     summary="Get Student Detail by ID",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="path",
     *         required=false,
     *         description="Gender",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="department_id",
     *         in="path",
     *         required=false,
     *         description="Department ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category data"
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
    public function DashBoardNotificationList($user_id , $gender ,$department_id)
    {
        try{

            $codition = "";
            if ($gender != null || $gender != '' || $gender != 'all') {
                $codition .= " AND p.gender = '" . $gender . "'";
            }


            if($department_id == "all" || $department_id == '' || $department_id == null){

                    $user = user::findOrFail($user_id);

                    $data = json_decode($user->notification, true);

                    $ids = array_column($data ?? [], 'id');

                    $ids = array_map('trim', $ids);

                    if (empty($ids)) {
                        return [];
                    }

                    $result = "('" . implode("', '", $ids) . "')";

                    $qry = "
                select
                    p.id,
                    p.name,
                    p.email,
                    p.mobile_number,
                    p.address,
                    p.register_number,
                    p.gender,
                    p.image,
                    p.id,
                    d.name AS department_name,
                    b.name AS designation_name,
                    EXISTS (
                        SELECT 1
                        FROM person_face pf
                        WHERE pf.person_id = p.id
                        AND pf.`date` = CURDATE()
                    ) as present

                FROM person p
                INNER JOIN department d ON d.id = p.department_id
                left outer join designation b ON b.id = p.designation_id
                WHERE
                    p.is_active = 1
                    " . $codition . "
                    AND p.department_id IN $result

            ";

            $qry1 = "
                SELECT
                    COUNT(DISTINCT p.id) AS gender_staff,
                    COUNT(DISTINCT pf.person_id) AS present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                    " . $codition . "
                    AND p.department_id IN $result

            ";
            $count = DB::select($qry1);

            $qry2 = "
                SELECT
                    COUNT(DISTINCT p.id) AS total_staff,
                    COUNT(DISTINCT pf.person_id) AS total_present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS total_absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                    AND p.department_id IN $result
            ";
            $Totalcount = DB::select($qry2);

            $data = DB::select($qry);

            $result = [
                "count" => $count,
                "Totalcount" => $Totalcount,
                "data" => $data
            ];
            return response($result);

            }
            else{
                $qry = "
                select
                    p.id,
                    p.name,
                    p.email,
                    p.mobile_number,
                    p.address,
                    p.register_number,
                    p.gender,
                    p.image,
                    p.id,
                    d.name AS department_name,
                    b.name AS designation_name,
                    EXISTS (
                        SELECT 1
                        FROM person_face pf
                        WHERE pf.person_id = p.id
                        AND pf.`date` = CURDATE()
                    ) as present

                FROM person p
                INNER JOIN department d ON d.id = p.department_id
                left outer join designation b ON b.id = p.designation_id
                WHERE
                    p.is_active = 1
                    AND p.department_id = '$department_id'
                    " . $codition . "

            ";

            $qry1 = "
                SELECT
                    COUNT(DISTINCT p.id) AS gender_staff,
                    COUNT(DISTINCT pf.person_id) AS present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                    AND p.department_id = '$department_id'
                    " . $codition . "

            ";
            $count = DB::select($qry1);

            $qry2 = "
                SELECT
                    COUNT(DISTINCT p.id) AS total_staff,
                    COUNT(DISTINCT pf.person_id) AS total_present_staff,
                    COUNT(DISTINCT p.id) - COUNT(DISTINCT pf.person_id) AS total_absent_staff
                FROM
                    person p
                LEFT JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` = CURDATE()
                WHERE
                    p.is_active = 1
                    AND p.department_id = '$department_id'
            ";
            $Totalcount = DB::select($qry2);

            $data = DB::select($qry);

            $result = [
                "count" => $count,
                "Totalcount" => $Totalcount,
                "data" => $data
            ];
            return response($result);

            }
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/DashBoardChartList",
     *     summary="Get dashBoard",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="DashBoard data"
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
    public function DashBoardChartList()
    {
        try {
            $qry = '
                select
                    SUM(tbl.working) as working,
                    SUM(tbl.non_working) as non_working
                from
                (select
                    count(c.id) as working,
                    0 as non_working
                from
                    camera c
                where
                    c.is_working = 1

                union all

                select
                    0 as working,
                    count(c.id) as non_working
                from
                    camera c
                where
                    c.is_working = 0) as tbl

            ';
            $camrea = DB::select($qry);

            $qry1 = '
                select
                    SUM(tbl.working) as working,
                    SUM(tbl.non_working) as non_working
                from
                (select
                    count(c.id) as working,
                    0 as non_working
                from
                    nvr c
                where
                    c.is_working = 1

                union all

                select
                    0 as working,
                    count(c.id) as non_working
                from
                    nvr c
                where
                    c.is_working = 0) as tbl

            ';
            $nvr = DB::select($qry1);
            $result = [
                'camrea' => $camrea,
                'nvr' => $nvr,
            ];

            return response($result);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/LiveAttendanceList",
     *     summary="Get live attendance data",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="DashBoard data"
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
    public function LiveAttendanceList()
    {
        try {
            $qry = '
                select
                    af.created_on,
                    a.name as person_name,
                    c.asset_no,
                    af.image,
                    af.title,
                    a.is_active,
                    c.model_name,
                    d.name as department_name,
                    d2.name as designation_name,
                    a.register_number
                from
                    person_face af
                inner join
                    person a
                on
                    a.id = af.person_id
                inner join
                    camera c
                on
                    c.id = af.camera_id
                left outer JOIN
                	department d
                ON
                	d.id = a.department_id
                left outer JOIN
                	designation d2
                ON
                	d2.id = a.designation_id
                where
                    af.`date` = CURDATE()
                order by
                    af.created_on desc

            ';
            $result = DB::select($qry);

            return response($result);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/LiveGroupAttendanceList/{group_id}",
     *     summary="Get live attendance data for a specific group",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="group_id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="DashBoard data"
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
    public function LiveGroupAttendanceList($group_id)
    {
        try {
            $group = group_camera_detail::where(
                    'group_camera_id',
                    $group_id
                )->get();

                $cameraIds = array_column($group->toArray(), 'camera_id');

                if (empty($cameraIds)) {
                    return response([]);
                }

                $Camera_ids = "('" . implode("','", $cameraIds) . "')";

                $qry = "
                    SELECT
                        af.created_on,
                        a.name AS person_name,
                        c.asset_no,
                        af.image,
                        af.title,
                        a.is_active,
                        c.model_name,
                        d.name AS department_name,
                        d2.name AS designation_name,
                        a.register_number
                    FROM
                        person_face af
                    INNER JOIN
                        person a
                    ON
                        a.id = af.person_id
                    INNER JOIN
                        camera c
                    ON
                        c.id = af.camera_id
                    LEFT OUTER JOIN
                        department d
                    ON
                        d.id = a.department_id
                    LEFT OUTER JOIN
                        designation d2
                    ON
                        d2.id = a.designation_id
                    WHERE
                        af.`date` = CURDATE()
                    AND
                        af.camera_id IN $Camera_ids
                    ORDER BY
                        af.created_on DESC
                ";

                $result = DB::select($qry);

                return response($result);

        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/Footprint/{person_id}/{date}",
     *     summary="Person Footprint",
     *     tags={"DashBoard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="person_id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         required=true,
     *         description="YYYY-MM-DD",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Footprint List"
     *     )
     * )
     */
    public function Footprint($person_id, $date)
    {
        try {

            $qry = "
            SELECT
                c.latitude AS lat,
                c.longitude AS lng,
                c.asset_no AS label,
                DATE_FORMAT(pf.updated_on, '%h:%i %p') AS time
            FROM person_face pf
            INNER JOIN camera c
                ON c.id = pf.camera_id
            WHERE
                pf.person_id = '$person_id'
            AND
                DATE(pf.date) = '$date'
            ORDER BY
                pf.updated_on DESC
        ";

            $data = DB::select($qry);

            return response($data);

        } catch (Exception $e) {

            return response([
                'Type' => 'E',
                'Message' => $e->getMessage(),
            ]);
        }
    }
}
