<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PersonFaceController extends Controller
{
    public function __construct()
    {
    }



    /**
     * @OA\Get(
     *     path="/api/AttendanceList/{start_date}/{end_date}/{student_id}",
     *     summary="Get dashBoard by date",
     *     tags={"Person Face"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="path",
     *         required=true,
     *         description="Start Date",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="path",
     *         required=true,
     *         description="End Date",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=false,
     *         description="Student ID",
     *         @OA\Schema(type="string", format="uuid",  nullable=true, example=null)
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
    public function AttendanceList($start_date , $end_date , $student_id = null)
    {
        try {
            if($student_id == null || $student_id == '' || $student_id == 'null' || $student_id == 'all'){
                $qry = "
                WITH time_ins AS (
                    SELECT
                        si.person_id,
                        s.name AS student_name,
                        s.register_number,
                        d.name as department_name,
                        d2.name as designation_name,
                        s.image,
                        si.person_no,
                        si.date,
                        si.created_on AS time_in_datetime,
                        DATE_FORMAT(si.created_on, '%H:%i:%s') AS time_in,
                        si.image AS time_in_image
                    FROM person_face si
                    INNER JOIN person s ON s.id = si.person_id
                    left outer JOIN
                        department d
                    on
                        d.id = s.department_id
                    left outer JOIN
                        designation d2
                    on
                        d2.id = s.designation_id
                    WHERE si.title = 'IN'
                    and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'

                ),
                time_outs AS (
                    SELECT
                        si.person_id,
                        si.date,
                        si.created_on AS time_out_datetime,
                        DATE_FORMAT(si.created_on, '%H:%i:%s') AS time_out,
                        si.image AS time_out_image
                    FROM person_face si
                    WHERE si.title = 'OUT'
                    and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
                )
                SELECT
                    ti.person_id,
                    ti.student_name,
                    ti.person_no,
                    ti.register_number,
                    ti.department_name,
                    ti.designation_name,
                    ti.image,
                    ti.date,
                    ti.time_in,
                    MIN(to_.time_out) AS time_out,
                    TIME_FORMAT(
                        TIMEDIFF(
                            MIN(to_.time_out_datetime),
                            ti.time_in_datetime
                        ),
                        '%H:%i:%s'
                    ) AS worked_hours,
                    ti.time_in_image,
                    (
                        SELECT to2.time_out_image
                        FROM time_outs to2
                        WHERE to2.person_id = ti.person_id
                        AND to2.date = ti.date
                        AND to2.time_out_datetime > ti.time_in_datetime
                        ORDER BY to2.time_out_datetime
                        LIMIT 1
                    ) AS time_out_image
                FROM time_ins ti
                LEFT JOIN time_outs to_
                    ON ti.person_id = to_.person_id
                    AND ti.date = to_.date
                    AND to_.time_out_datetime > ti.time_in_datetime
                GROUP BY
                    ti.person_id,
                    ti.student_name,
                    ti.person_no,
                    ti.register_number,
                    ti.department_name,
                    ti.designation_name,
                    ti.image,
                    ti.date,
                    ti.time_in,
                    ti.time_in_datetime,
                    ti.time_in_image
                ORDER BY
                    ti.person_id,
                    ti.date,
                    ti.time_in_datetime;
            ";


            }
            else{
                $qry = "
                WITH time_ins AS (
                    SELECT
                        si.person_id,
                        s.name AS student_name,
                        s.register_number,
                        d.name as department_name,
                        d2.name as designation_name,
                        s.image,
                        si.person_no,
                        si.date,
                        si.created_on AS time_in_datetime,
                        DATE_FORMAT(si.created_on, '%H:%i:%s') AS time_in,
                        si.image AS time_in_image
                    FROM person_face si
                    INNER JOIN person s ON s.id = si.person_id
                    left outer JOIN
                        department d
                    on
                        d.id = s.department_id
                    left outer JOIN
                        designation d2
                    on
                        d2.id = s.designation_id
                    WHERE si.title = 'IN'
                    and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
                    AND si.person_id = '$student_id'
                ),
                time_outs AS (
                    SELECT
                        si.person_id,
                        si.date,
                        si.created_on AS time_out_datetime,
                        DATE_FORMAT(si.created_on, '%H:%i:%s') AS time_out,
                        si.image AS time_out_image
                    FROM person_face si
                    WHERE si.title = 'OUT'
                    and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
                    AND si.person_id = '$student_id'
                )
                SELECT
                    ti.person_id,
                    ti.student_name,
                    ti.person_no,
                    ti.register_number,
                    ti.department_name,
                    ti.designation_name,
                    ti.image,
                    ti.date,
                    ti.time_in,
                    MIN(to_.time_out) AS time_out,
                    TIME_FORMAT(
                        TIMEDIFF(
                            MIN(to_.time_out_datetime),
                            ti.time_in_datetime
                        ),
                        '%H:%i:%s'
                    ) AS worked_hours,
                    ti.time_in_image,
                    (
                        SELECT to2.time_out_image
                        FROM time_outs to2
                        WHERE to2.person_id = ti.person_id
                        AND to2.date = ti.date
                        AND to2.time_out_datetime > ti.time_in_datetime
                        ORDER BY to2.time_out_datetime
                        LIMIT 1
                    ) AS time_out_image
                FROM time_ins ti
                LEFT JOIN time_outs to_
                    ON ti.person_id = to_.person_id
                    AND ti.date = to_.date
                    AND to_.time_out_datetime > ti.time_in_datetime
                GROUP BY
                    ti.person_id,
                    ti.student_name,
                    ti.person_no,
                    ti.register_number,
                    ti.department_name,
                    ti.designation_name,
                    ti.image,
                    ti.date,
                    ti.time_in,
                    ti.time_in_datetime,
                    ti.time_in_image
                ORDER BY
                    ti.person_id,
                    ti.date,
                    ti.time_in_datetime;
            ";

            }

            $data = DB::select($qry);
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    // public function AttendanceList($start_date , $end_date , $student_id = null)
    // {
    //     try {
    //         if($student_id == null || $student_id == '' || $student_id == 'null'){
    //             $qry = "
    //             WITH time_ins AS (
    //             SELECT
    //                 si.person_id,
    //                 s.name AS student_name,
    //                 s.register_number,
    //                 s.image,
    //                 si.person_no,
    //                 si.date,
    //                 DATE_FORMAT(si.created_on, '%H:%i:%s') as time_in,
    //                 si.image AS time_in_image,
    //                 ROW_NUMBER() OVER (PARTITION BY si.person_id, si.date ORDER BY DATE_FORMAT(si.created_on, '%H:%i:%s')) AS rn
    //             FROM person_face si
    //             inner JOIN person s ON s.id = si.person_id
    //             WHERE si.title = 'IN'
    //             and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
    //         ),

    //         time_outs AS (
    //             SELECT
    //                 si.person_id,
    //                 si.date,
    //                 DATE_FORMAT(si.created_on, '%H:%i:%s') as time_out,
    //                 si.image AS time_out_image,
    //                 ROW_NUMBER() OVER (PARTITION BY si.person_id, si.date ORDER BY DATE_FORMAT(si.created_on, '%H:%i:%s')) AS rn
    //             FROM person_face si
    //             WHERE si.title = 'OUT'
    //             and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
    //         )

    //         SELECT
    //             ti.person_id,
    //             ti.student_name,
    //             ti.person_no,
    //             ti.register_number,
    //             ti.image,
    //             ti.date,
    //             ti.time_in,
    //             to_.time_out,
    //             ti.time_in_image,
    //             to_.time_out_image
    //         FROM time_ins ti
    //         LEFT JOIN time_outs to_
    //             ON ti.person_id = to_.person_id
    //         AND ti.date = to_.date
    //         AND ti.rn = to_.rn
    //         ORDER BY ti.person_id, ti.date, ti.time_in;
    //         ";


    //         }
    //         else{
    //             $qry = "
    //             WITH time_ins AS (
    //             SELECT
    //                 si.person_id,
    //                 s.name AS student_name,
    //                 s.register_number,
    //                 s.image,
    //                 si.person_no,
    //                 si.date,
    //                 DATE_FORMAT(si.created_on, '%H:%i:%s') as time_in,
    //                 si.image AS time_in_image,
    //                 ROW_NUMBER() OVER (PARTITION BY si.person_id, si.date ORDER BY DATE_FORMAT(si.created_on, '%H:%i:%s')) AS rn
    //             FROM person_face si
    //             inner JOIN person s ON s.id = si.person_id
    //             WHERE si.title = 'IN'
    //             and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
    //             AND si.person_id = '$student_id'
    //         ),

    //         time_outs AS (
    //             SELECT
    //                 si.person_id,
    //                 si.date,
    //                 DATE_FORMAT(si.created_on, '%H:%i:%s') as time_out,
    //                 si.image AS time_out_image,
    //                 ROW_NUMBER() OVER (PARTITION BY si.person_id, si.date ORDER BY DATE_FORMAT(si.created_on, '%H:%i:%s')) AS rn
    //             FROM person_face si
    //             WHERE si.title = 'OUT'
    //             and DATE_FORMAT(si.created_on, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
    //             AND si.person_id = '$student_id'
    //         )

    //         SELECT
    //             ti.person_id,
    //             ti.student_name,
    //             ti.person_no,
    //             ti.register_number,
    //             ti.image,
    //             ti.date,
    //             ti.time_in,
    //             to_.time_out,
    //             ti.time_in_image,
    //             to_.time_out_image
    //         FROM time_ins ti
    //         LEFT JOIN time_outs to_
    //             ON ti.person_id = to_.person_id
    //         AND ti.date = to_.date
    //         AND ti.rn = to_.rn
    //         ORDER BY ti.person_id, ti.date, ti.time_in;
    //         ";

    //         }

    //         $data = DB::select($qry);
    //         return response($data);
    //     } catch (Exception $e) {
    //         return response(["Type" => "E", "Message" => $e->getMessage()]);
    //     }
    // }

    /**
     * @OA\Get(
     *     path="/api/PersonFaceList",
     *     tags={"Person Face"},
     *     summary="Get list of person faces",
     *     @OA\Response(
     *         response=200,
     *         description="List of person faces"
     *     )
     * )
     */
    public function PersonFaceList()
    {
        try {
            $path = storage_path('app/public/Upload/personimage/');
            $files = File::allFiles($path);
            foreach ($files as $file) {
                $filename = $file->getFilename(); // 1266_Karthikeyan.JPG
                $id = explode('_', $filename)[0];
                $names = explode('_', $filename)[1];
                $dd = explode('.', $names);
                $name = $dd[0];
                $person = person::where('register_number', $id)
                    ->where('name', $name)
                    ->first();
                if ($person) {
                        $person->image = env('Live_Url') . 'Upload/personimage/' . $filename;
                        $person->save();
                }
            }
            $data = "";
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }
}
