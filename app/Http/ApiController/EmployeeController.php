<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person;
use App\Http\Model\person_type;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/EmployeeList",
     *     summary="Get list of Employees",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="person_type_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="List of Employees")
     * )
     */
    public function List(Request $request)
    {
        try {
            $employeeTypeIds = person_type::where('category', 'Employee')->select('id');

            $query = person::whereIn('person_type_id', $employeeTypeIds);

            if ($request->filled('person_type_id')) {
                $query->where('person_type_id', $request->person_type_id);
            }

            $data = $query->orderBy('display_order')->get();

            $data->transform(function ($item) {
                $item->image_url = $this->getEmployeeImageUrl($item->image);
                return $item;
            });

            return response($data);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/EmployeeById/{id}",
     *     summary="Get Employee by ID",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Employee data"),
     *     @OA\Response(response=404, description="Data not found")
     * )
     */
    public function ById($id)
    {
        try {
            $employeeTypeIds = person_type::where('category', 'Employee')->select('id');

            $data = person::whereIn('person_type_id', $employeeTypeIds)->find($id);

            if (!$data) {
                return response([
                    'Type' => 'E',
                    'Message' => 'Data Not Found'
                ], 404);
            }

            $data->image_url = $this->getEmployeeImageUrl($data->image);

            return response($data);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/EmployeeInsert",
     *     summary="Insert a new Employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="register_number", type="string"),
     *                 @OA\Property(property="mobile_number", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="person_type_id", type="string", nullable=true),
     *                 @OA\Property(property="display_order", type="integer"),
     *                 @OA\Property(property="is_active", type="integer"),
     *                 @OA\Property(property="created_by_id", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Created successfully")
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'gender' => 'nullable|string|max:20',
                'register_number' => 'nullable|string|max:100',
                'mobile_number' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'person_type_id' => 'nullable|exists:person_type,id',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'created_by_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response([
                    'Type' => 'E',
                    'Message' => $validator->errors()->first()
                ], 422);
            }

            if ($request->filled('person_type_id')) {
                $validType = person_type::where('id', $request->person_type_id)
                    ->where('category', 'Employee')
                    ->exists();

                if (!$validType) {
                    return response([
                        'Type' => 'E',
                        'Message' => 'Selected Employee Type is invalid'
                    ], 422);
                }
            }

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $this->uploadEmployeeImage($request->file('image'));
            }

            $employeeData = new person();
            $employeeData->name = $request->name;
            $employeeData->gender = $request->gender;
            $employeeData->register_number = $request->register_number;
            $employeeData->mobile_number = $request->mobile_number;
            $employeeData->email = $request->email;
            $employeeData->image = $imagePath;
            $employeeData->address = $request->address;
            $employeeData->person_type_id = $request->filled('person_type_id')
                ? $request->person_type_id
                : null;
            $employeeData->display_order = $request->input('display_order', 0);
            $employeeData->is_active = $request->input('is_active', 0);
            $employeeData->created_by_id = $request->created_by_id;
            $employeeData->save();

            return response([
                'Type' => 'S',
                'Message' => 'Created successfully',
                'Id' => $employeeData->id,
                'image' => $employeeData->image,
                'image_url' => $this->getEmployeeImageUrl($employeeData->image)
            ]);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/EmployeeUpdate",
     *     summary="Update an existing Employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id", "name"},
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="register_number", type="string"),
     *                 @OA\Property(property="mobile_number", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="image_removed", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="person_type_id", type="string", nullable=true),
     *                 @OA\Property(property="display_order", type="integer"),
     *                 @OA\Property(property="is_active", type="integer"),
     *                 @OA\Property(property="updated_by_id", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function Update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:person,id',
                'name' => 'required|string|max:255',
                'gender' => 'nullable|string|max:20',
                'register_number' => 'nullable|string|max:100',
                'mobile_number' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'person_type_id' => 'nullable|exists:person_type,id',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'image_removed' => 'nullable|boolean',
                'updated_by_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response([
                    'Type' => 'E',
                    'Message' => $validator->errors()->first()
                ], 422);
            }

            if ($request->filled('person_type_id')) {
                $validType = person_type::where('id', $request->person_type_id)
                    ->where('category', 'Employee')
                    ->exists();

                if (!$validType) {
                    return response([
                        'Type' => 'E',
                        'Message' => 'Selected Employee Type is invalid'
                    ], 422);
                }
            }

            $employeeTypeIds = person_type::where('category', 'Employee')->select('id');

            $employeeData = person::whereIn('person_type_id', $employeeTypeIds)
                ->findOrFail($request->id);

            if ($request->hasFile('image')) {
                $newImagePath = $this->uploadEmployeeImage($request->file('image'));

                $this->deleteEmployeeImage($employeeData->image);

                $employeeData->image = $newImagePath;
            } elseif ($request->boolean('image_removed')) {
                $this->deleteEmployeeImage($employeeData->image);
                $employeeData->image = null;
            }

            $employeeData->name = $request->name;
            $employeeData->gender = $request->gender;
            $employeeData->register_number = $request->register_number;
            $employeeData->mobile_number = $request->mobile_number;
            $employeeData->email = $request->email;
            $employeeData->address = $request->address;
            $employeeData->person_type_id = $request->filled('person_type_id')
                ? $request->person_type_id
                : null;
            $employeeData->display_order = $request->input('display_order', 0);
            $employeeData->is_active = $request->input('is_active', 0);
            $employeeData->updated_by_id = $request->updated_by_id;
            $employeeData->save();

            return response([
                'Type' => 'S',
                'Message' => 'Updated successfully',
                'Id' => $employeeData->id,
                'image' => $employeeData->image,
                'image_url' => $this->getEmployeeImageUrl($employeeData->image)
            ]);
        } catch (ModelNotFoundException $e) {
            return response([
                'Type' => 'E',
                'Message' => 'Data Not Found'
            ], 404);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/EmployeeDelete/{id}",
     *     summary="Delete Employee by ID",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function Delete($id)
    {
        try {
            $employeeTypeIds = person_type::where('category', 'Employee')->select('id');

            $data = person::whereIn('person_type_id', $employeeTypeIds)->findOrFail($id);

            $this->deleteEmployeeImage($data->image);
            $data->delete();

            return response([
                'Type' => 'S',
                'Message' => 'Deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response([
                'Type' => 'E',
                'Message' => 'Data Not Found'
            ], 404);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/EmployeeTypeFilterList",
     *     summary="Get Employee Types for Employee Master",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of Employee Types")
     * )
     */
    public function EmployeeTypeFilterList()
    {
        try {
            $data = person_type::where('category', 'Employee')
                ->orderBy('display_order')
                ->get();

            return response($data);
        } catch (Exception $e) {
            return response([
                'Type' => 'E',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save uploaded employee image inside public/employees.
     * Database stores a relative path such as employees/employee_xxx.jpg.
     */
    private function uploadEmployeeImage($file)
    {
        $folder = public_path('employees');

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = uniqid('employee_', true) . '.' . $extension;

        $file->move($folder, $fileName);

        return 'employees/' . $fileName;
    }

    /**
     * Delete an old image only if it belongs to public/employees.
     */
    private function deleteEmployeeImage($imagePath)
    {
        if (empty($imagePath)) {
            return;
        }

        $fileName = basename($imagePath);
        $fullPath = public_path('employees/' . $fileName);

        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Convert the stored relative image path into a public URL.
     */
    private function getEmployeeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        return asset(ltrim($imagePath, '/'));
    }
}
