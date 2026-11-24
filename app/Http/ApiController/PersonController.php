<?php

namespace App\Http\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Model\person;
use App\Http\Model\person_type;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/PersonList",
     *     summary="Get list of Persons",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="person_type_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="List of Persons")
     * )
     */
    public function List(Request $request)
    {
        try {
            $personTypeIds = person_type::where('category', 'General')->select('id');

            $query = person::whereIn('person_type_id', $personTypeIds);

            if ($request->filled('person_type_id')) {
                $query->where('person_type_id', $request->person_type_id);
            }

            $data = $query->orderBy('display_order')->get();

            $data->transform(function ($item) {
                $item->image_url = $this->getPersonImageUrl($item->image);
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
     *     path="/api/PersonById/{id}",
     *     summary="Get Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Person data"),
     *     @OA\Response(response=404, description="Data not found")
     * )
     */
    public function ById($id)
    {
        try {
            $personTypeIds = person_type::where('category', 'General')->select('id');

            $data = person::whereIn('person_type_id', $personTypeIds)->find($id);

            if (!$data) {
                return response([
                    'Type' => 'E',
                    'Message' => 'Data Not Found'
                ], 404);
            }

            $data->image_url = $this->getPersonImageUrl($data->image);

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
     *     path="/api/PersonInsert",
     *     summary="Insert a new Person",
     *     tags={"Person"},
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
                    ->where('category', 'General')
                    ->exists();

                if (!$validType) {
                    return response([
                        'Type' => 'E',
                        'Message' => 'Selected Person Type is invalid'
                    ], 422);
                }
            }

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $this->uploadPersonImage($request->file('image'));
            }

            $personData = new person();
            $personData->name = $request->name;
            $personData->gender = $request->gender;
            $personData->register_number = $request->register_number;
            $personData->mobile_number = $request->mobile_number;
            $personData->email = $request->email;
            $personData->image = $imagePath;
            $personData->address = $request->address;
            $personData->person_type_id = $request->filled('person_type_id')
                ? $request->person_type_id
                : null;
            $personData->display_order = $request->input('display_order', 0);
            $personData->is_active = $request->input('is_active', 0);
            $personData->created_by_id = $request->created_by_id;
            $personData->save();

            return response([
                'Type' => 'S',
                'Message' => 'Created successfully',
                'Id' => $personData->id,
                'image' => $personData->image,
                'image_url' => $this->getPersonImageUrl($personData->image)
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
     *     path="/api/PersonUpdate",
     *     summary="Update an existing Person",
     *     tags={"Person"},
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
                    ->where('category', 'General')
                    ->exists();

                if (!$validType) {
                    return response([
                        'Type' => 'E',
                        'Message' => 'Selected Person Type is invalid'
                    ], 422);
                }
            }

            $personTypeIds = person_type::where('category', 'General')->select('id');

            $personData = person::whereIn('person_type_id', $personTypeIds)
                ->findOrFail($request->id);

            if ($request->hasFile('image')) {
                $newImagePath = $this->uploadPersonImage($request->file('image'));

                $this->deletePersonImage($personData->image);

                $personData->image = $newImagePath;
            } elseif ($request->boolean('image_removed')) {
                $this->deletePersonImage($personData->image);
                $personData->image = null;
            }

            $personData->name = $request->name;
            $personData->gender = $request->gender;
            $personData->register_number = $request->register_number;
            $personData->mobile_number = $request->mobile_number;
            $personData->email = $request->email;
            $personData->address = $request->address;
            $personData->person_type_id = $request->filled('person_type_id')
                ? $request->person_type_id
                : null;
            $personData->display_order = $request->input('display_order', 0);
            $personData->is_active = $request->input('is_active', 0);
            $personData->updated_by_id = $request->updated_by_id;
            $personData->save();

            return response([
                'Type' => 'S',
                'Message' => 'Updated successfully',
                'Id' => $personData->id,
                'image' => $personData->image,
                'image_url' => $this->getPersonImageUrl($personData->image)
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
     *     path="/api/PersonDelete/{id}",
     *     summary="Delete Person by ID",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function Delete($id)
    {
        try {
            $personTypeIds = person_type::where('category', 'General')->select('id');

            $data = person::whereIn('person_type_id', $personTypeIds)->findOrFail($id);

            $this->deletePersonImage($data->image);
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
     * Get all Person Types belonging to Person category.
     *
     * @OA\Get(
     *     path="/api/PersonTypeFilterList",
     *     summary="Get Person Types for Person Master",
     *     tags={"Person"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of Person Types")
     * )
     */
    public function PersonTypeFilterList()
    {
        try {
            $data = person_type::where('category', 'General')
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
     * Save the uploaded image inside public/persons.
     * Database stores a relative path such as persons/person_xxx.jpg.
     */
    private function uploadPersonImage($file)
    {
        $folder = public_path('persons');

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = uniqid('person_', true) . '.' . $extension;

        $file->move($folder, $fileName);

        return 'persons/' . $fileName;
    }

    /**
     * Delete an old image only if it belongs to public/persons.
     */
    private function deletePersonImage($imagePath)
    {
        if (empty($imagePath)) {
            return;
        }

        $fileName = basename($imagePath);
        $fullPath = public_path('persons/' . $fileName);

        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Convert the stored relative image path into a public URL.
     */
    private function getPersonImageUrl($imagePath)
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
