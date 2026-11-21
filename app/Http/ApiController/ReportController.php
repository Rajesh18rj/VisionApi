<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Post(
     *      path="/api/ReportStaffList",
     *      operationId="ReportStaffList",
     *      tags={"Report"},
     *      summary="Get dashboard data for staff",
     *      security={{"bearerAuth":{}}},
     *      description="ReportStaffList",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"start_date", "end_date", "department_id", "staff_id"},
     *            @OA\Property(property="start_date", type="string", format="string", example="2026-06-24", nullable=true),
     *            @OA\Property(property="end_date", type="string", format="string", example="2026-06-28", nullable=true),
     *            @OA\Property(property="department_id", type="string", format="string", example="", nullable=true),
     *            @OA\Property(property="staff_id", type="string", format="string", example="", nullable=true),
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
    public function ReportStaffList(Request $request)
    {
        try {
            $codition = "";
            $datas = $request->all();
            $start_date = $datas['start_date'];
            $end_date = $datas['end_date'];
            if ($datas["department_id"] != null || $datas["department_id"] != '') {
                $codition .= " AND p.department_id = '" . $datas["department_id"] . "'";
            }
            if ($datas["staff_id"] != null || $datas["staff_id"] != '') {
                $codition .= " AND p.id = '" . $datas["staff_id"] . "'";
            }

                $qry = "
                SELECT
                    p.name,
                    p.email,
                    p.register_number,
                    p.mobile_number,
                    p.gender,
                    d.name AS department_name,
                    d2.name AS designation_name,
                    MAX(CASE WHEN pf.id IS NOT NULL THEN 1 ELSE 0 END) AS attendance,
                    GROUP_CONCAT(DISTINCT pf.`date` ORDER BY pf.`date`) AS datee
                FROM person p
                left outer JOIN
                    department d
                ON
                    d.id = p.department_id
                LEFT outer JOIN
                    designation d2
                ON
                    d2.id = p.designation_id
                LEFT outer JOIN
                    person_face pf
                ON
                    pf.person_id = p.id
                AND
                    pf.`date` BETWEEN '$start_date' AND '$end_date'
                WHERE
                    p.is_active = 1
                    " . $codition . "
                GROUP BY
                    p.id,
                    p.name,
                    p.email,
                    p.mobile_number,
                    p.register_number,
                    p.gender,
                    d.name,
                    d2.name,
                    pf.`date`;
            ";

            $result = DB::select($qry);
            return response($result);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ReportStudentDetailList/{person_id}/{date}",
     *     summary="Get Student Detail by ID",
     *     tags={"Report"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="person_id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=true,
     *         description="Date",
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
    public function ReportStudentDetailList($person_id ,$date)
    {
        try{
           $qry = "
                SELECT
                    c.asset_no,
                    c.type,
                    COALESCE(pf.time_in, pf.time_out) AS time
                FROM person_face pf
                INNER JOIN camera c
                    ON c.id = pf.camera_id
                WHERE pf.person_id = '$person_id'
                and
                    pf.date = '$date'
                order by
                    pf.created_on asc

            ";
            $data = DB::select($qry);
            return response($data);
        } catch (Exception $e) {
            return response(["Type" => "E", "Message" => $e->getMessage()]);
        }
    }

}
