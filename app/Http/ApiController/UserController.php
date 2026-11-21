<?php

namespace App\Http\ApiController;

use App\Helper\MailHelper;
use App\Http\Controllers\Controller;
use App\Http\Model\user;
use App\Http\Model\user_login_info;
use App\Http\Model\user_role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="API",
 *     version="1.0.0",
 *     description="API documentation for the project."
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000/",
 *     description="Main API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class UserController extends Controller
{
    private $apiToken;

    public function __construct()
    {
        $this->apiToken = uniqid(base64_encode(Str::random(60)));
    }

    public function List()
    {
        $data = user::where('user_role_id', '!=', 'e58907f1-2baa-4ffe-97c0-02d9fd28c7f8')->with(['user_role'])->get();
        return response($data);
    }

    /**
     * @OA\Get(
     *     path="/api/UserList",
     *     summary="Get list of all users except Super Admin",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="List of users"),
     *     @OA\Response(response=404, description="Super Admin role not found")
     * )
     */
    public function WithList()
    {
        // Find the user_role_id where the role name is "Super Admin"
        $superAdminRole = user_role::where('name', 'Super Admin')->first();

        if (! $superAdminRole) {
            return response()->json(['error' => 'Super Admin role not found'], 404);
        }

        // Use the found user_role_id in the query
        $data = User::with(['user_role'])->where('user_role_id', '>', $superAdminRole->id)->get();

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/UserById/{id}",
     *     summary="Get user by ID",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(response=200, description="User data"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function ById($id)
    {
        $data = user::find($id);

        return response($data);
    }

    /**
     * @OA\Post(
     *     path="/api/UserInsert",
     *     summary="Insert a new user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_role_id", "email"},
     *
     *             @OA\Property(property="user_role_id", type="integer"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="User created successfully"),
     *     @OA\Response(response=400, description="Email already exists")
     * )
     */
    public function Insert(Request $request)
    {
        try {
            $data = new user();
            $data = $request->only($data->fillable);
            if (user::where('user_role_id', $data['user_role_id'])->where('email', $data['email'])->count() > 0) {
                return response(['Type' => 'E', 'Message' => $data['email'].' already exits']);
            } else {
                $new_password = rand(100000, 999999);
                $data['password'] = Crypt::encryptString($data['password']);
                $data['confirm_password'] = Crypt::encryptString($data['password']);

                $view = view('Otp', ["new_password" => $new_password]);
                $view->render();
                $Mail = MailHelper::SendMail($data['email'], "Otp password", $view);
                if($Mail['Type'] == 'S'){
                    $result = user::create($data);
                    return response(['Type' => 'S', 'Message' => 'Created successfully', 'Id' => $result['id']]);
                }
                else{
                    return response(['Type' => 'E', 'Message' => $Mail['Message']]);
                }
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/UserUpdate",
     *     summary="Update user details",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"id", "email"},
     *
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=400, description="Email already exists")
     * )
     */
    public function Update(Request $request)
    {
        try {
            $data = new user();
            $data = $request->only($data->fillable);
            if (user::where('id', '!=', $data['id'])->where('email', $data['email'])->count() > 0) {
                return response(['Type' => 'E', 'Message' => $data['email'].' already exits']);
            } else {
                user::findOrFail($data['id'])->update($data);

                return response(['Type' => 'S', 'Message' => 'Updated successfully', 'Id' => $data['id']]);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/UserDelete/{id}",
     *     summary="Delete a user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function Delete($id)
    {
        user::findOrFail($id)->delete();

        return response(['Type' => 'S', 'Message' => 'Deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/UserLogin",
     *     summary="Login user",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="device_id", type="string", example="ANDROID-123456"),
     *             @OA\Property(property="system_name", type="string", example="android"),
     *             @OA\Property(property="system_version", type="string", example="Android 15"),
     *            @OA\Property(property="model_number", type="string", example="Samsung S24 Ultra"),
     *            @OA\Property(property="app_version", type="string", example="1.0.0"),
     *           @OA\Property(property="onesignal_subscription_id", type="string", example="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function Login(Request $request)
    {
        try {

            $user = new User();
            $user = $request->only($user->fillable);

            $data = User::with('user_role')
                ->where('email', $user['email'])
                ->first();

            if (! $data) {
                return response([
                    'Type' => 'E',
                    'Message' => 'User name incorrect',
                ]);
            }

            if ($data->status != 1) {
                return response([
                    'Type' => 'E',
                    'Message' => 'Your account deactivated, please contact administrator',
                ]);
            }

            if (Crypt::decryptString($data->password) != $user['password']) {
                return response([
                    'Type' => 'E',
                    'Message' => 'Password incorrect',
                ]);
            }

            // Generate API Token
            DB::table('user')
                ->where('id', $data->id)
                ->update([
                    'api_token' => $this->apiToken,
                ]);

            $data->api_token = $this->apiToken;

            /*
            |--------------------------------------------------------------------------
            | Save Logged Device
            |--------------------------------------------------------------------------
            */

            if ($request->filled('device_id')) {

                user_login_info::updateOrCreate(

                    [
                        'user_id' => $data->id,
                        'device_id' => $request->device_id,
                    ],

                    [
                        'system_name' => $request->system_name,
                        'system_version' => $request->system_version,
                        'model_number' => $request->model_number,
                        'app_version' => $request->app_version,
                        'onesignal_subscription_id' => $request->onesignal_subscription_id,
                        'ip_address' => $request->ip(),
                        'login_time' => now(),
                        'last_seen' => now(),
                        'created_by_id' => $data->id ?? '00000000-0000-0000-0000-000000000000',
                        'updated_by_id' => $data->id ?? '00000000-0000-0000-0000-000000000000',
                    ]

                );
            }

            return response([
                'Type' => 'S',
                'Message' => 'Login successfully',
                'AdditionalData' => [
                    'User' => $data,
                ],
            ]);
        } catch (Exception $e) {

            return response([
                'Type' => 'E',
                'Message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ChangePassword",
     *     tags={"User"},
     *     summary="Change user password",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id","old_password","password"},
     *
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="old_password", type="string", format="password"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Password changed successfully")
     * )
     */
    public function ChangePassword(Request $request)
    {
        try {
            $userdata = new user();
            $user = user::find($request['user_id']);
            if (! $user) {
                return response(['Type' => 'E', 'Message' => 'User not found']);
            }
            if (Crypt::decryptString($user['password']) == $request['old_password']) {
                $userdata['password'] = Crypt::encryptString($request['password']);
                $userdata['api_token'] = null;
                $userdata['remember_token'] = null;
                $userdata['reset_password'] = false;
                user::findOrFail($user['id'])->update($userdata->toArray());

                return response(['Type' => 'S', 'Message' => 'User Change Password successfully']);
            } else {
                return response(['Type' => 'E', 'Message' => 'Old password is wrong']);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ForgetPassword",
     *     tags={"User"},
     *     summary="Send forgot password email",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Password reset email sent")
     * )
     */
    public function ForgetPassword(Request $request)
    {
        try {
            if (user::where('email', $request['email'])->count() > 0) {
                $new_password = rand(100000, 999999);
                $data = user::where('email', $request['email'])->first();
                $domain_name = 'https://tapitbizcard.app/Admin/resetpassword/'.$data['id'];
                $view = view('Forgot', ['new_password' => $new_password, 'domain_name' => $domain_name]);
                $view->render();
                MailHelper::SendMail($request['email'], 'Forgot password', $view);

                user::find($data['id'])->update(['password' => Crypt::encryptString($new_password)]);

                return response(['Type' => 'S', 'Message' => 'Check mail, password reset sucessfully.', 'Id' => $data['id']]);
            } else {
                return response(['Type' => 'E', 'Message' => 'Email is incorrect']);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ResetPassword",
     *     tags={"User"},
     *     summary="Reset password using OTP",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"id","temp_password","password","confirmPassword"},
     *
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="temp_password", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="confirmPassword", type="string", format="password")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Password reset successfully")
     * )
     */
    public function ResetPassword(Request $request)
    {
        try {
            $user = user::find($request['id']);
            if (Crypt::decryptString($user['password']) == $request['temp_password']) {
                $user['password'] = Crypt::encryptString($request['password']);
                $user['confirm_password'] = Crypt::encryptString($request['confirmPassword']);
                unset($request['confirmPassword']);
                unset($request['temp_password']);
                user::findOrFail($request['id'])->update($user->toArray());

                return response(['Type' => 'S', 'Message' => 'Password Reset successfully']);
            } else {
                return response(['Type' => 'E', 'Message' => 'OTP is wrong']);
            }
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    public function Decrypt($data)
    {
        return Crypt::decryptString($data);
    }

    /**
     * @OA\Post(
     *     path="/api/UserLogout",
     *     tags={"User"},
     *     summary="Logout user",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id","device_id"},
     *
     *             @OA\Property(property="user_id", type="string", example="b5cec1c6-8783-4e60-b88b-d49d8ae658a7"),
     *             @OA\Property(property="device_id", type="string", example="ANDROID123456")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Logout Successfully")
     * )
     */
    public function UserLogout(Request $request)
    {
        try {

            $request->validate([
                'user_id' => 'required',
                'device_id' => 'required',
            ]);

            // Clear API Token
            DB::table('user')
                ->where('id', $request->user_id)
                ->update([
                    'api_token' => '',
                ]);

            // Remove logged-in device
            user_login_info::where('user_id', $request->user_id)
                ->where('device_id', $request->device_id)
                ->delete();

            return response()->json([
                'Type' => 'S',
                'Message' => 'Logout Successfully',
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
     *     path="/api/NewUserInsert",
     *     tags={"User"},
     *     summary="Insert a new user with default password",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","user_role_id"},
     *
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="user_role_id", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="User created successfully")
     * )
     */
    public function NewUserInsert(Request $request)
    {
        try {
            $user = new user();
            $data = $request->only($user->getFillable());
            $exists = user::where('user_role_id', $data['user_role_id'])
                ->where('email', $data['email'])
                ->exists();
            if ($exists) {
                return response(['Type' => 'E', 'Message' => $data['email'].' already exists']);
            }

            $defaultPassword = 'Login@123';
            $data['password'] = Crypt::encryptString($defaultPassword);
            $data['confirm_password'] = Crypt::encryptString($defaultPassword);

            // Create user
            $result = user::create($data);

            return response(['Type' => 'S', 'Message' => 'User created successfully', 'Id' => $result->id,
            ]);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/DecryptPassword",
     *     tags={"User"},
     *     summary="Decrypt password",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"encrypted"},
     *
     *             @OA\Property(property="encrypted", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Decrypted password")
     * )
     */
    public function DecryptPassord(Request $request)
    {
        try {
            return Crypt::decryptString($request);
        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/RefreshOneSignalUpdate",
     *     summary="Update user details",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"user_id", "device_id", "onesignal_subscription_id"},
     *             @OA\Property(property="user_id", type="string",format="string", example=""),
     *             @OA\Property(property="device_id", type="string",format="string", example=""),
     *             @OA\Property(property="onesignal_subscription_id", type="string",format="string", example=""),
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=400, description="Email already exists")
     * )
     */
    public function RefreshOneSignalUpdate(Request $request)
    {
        try {
            $data = new user_login_info();
            $data = $request->only($data->fillable);
            $user = user_login_info::where('user_id', $data['user_id'])->where('device_id', $data['device_id'])->first();
            if (! $user) {
                return response(['Type' => 'E', 'Message' => 'User or device not found']);
            }
            else{
                $user['onesignal_subscription_id'] = $data['onesignal_subscription_id'];
                user_login_info::findOrFail($user['id'])->update($user->toArray());
                return response(['Type' => 'S', 'Message' => 'Updated successfully']);
            }


        } catch (Exception $e) {
            return response(['Type' => 'E', 'Message' => $e->getMessage()]);
        }
    }
}
