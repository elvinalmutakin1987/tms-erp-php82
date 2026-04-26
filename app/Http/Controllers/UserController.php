<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $user = User::whereNot('username', 'superadmin')->get();
            return DataTables::of($user)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                data-id="' . $item->id . '">Edit</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('role', function ($item) {
                    return $item->roles->pluck('name')->first();
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Setting',
            'route-module' => null,
            'sub-module' => 'User',
            'route-sub-module' => 'user.index',
        ];
        return view('user.index', compact('breadcrum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'username' => 'required|unique:users,username',
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|string|max:255|confirmed',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            $user = new User();
            $user->username = $request->username;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->pass_mobile = bcrypt($request->password);
            $user->email_verified_at = now();
            $user->remember_token = Str::random(10);
            $user->request_token = $request->request_token;
            if ($request->sign_path) {
                $file = $request->file('sign_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "sign_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $user->sign_path = $directory . '/' . $filename;
            }
            $user->save();
            $role = Role::find($request->role_id);
            $user->assignRole($role);
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data saved!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'title' => 'Opps..',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $imagePath = asset('storage/' . $user->sign_path);
        $role_id = $user->roles->pluck('id');
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $user,
            'sign_path' => $imagePath,
            'role_id' => $role_id,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            if ($request->password) {
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|unique:users,email,' . $user->id . ',id',
                    'password' => 'required|string|max:255|confirmed',
                ]);
            } else {
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|unique:users,email,' . $user->id . ',id'
                ]);
            }
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            if ($request->sign_path) {
                $file = $request->file('sign_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "sign_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $user->sign_path = $directory . '/' . $filename;
            }
            $user->save();
            $role = Role::find($request->role_id);
            $user->syncRoles([$role]);
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Updated!',
                'message' => 'Data updated!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'title' => 'Opps..',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->syncRoles();
            $user->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Deleted!',
                'message' => 'Data Deleted'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ambil data role selain superadmin
     */
    public function get_role_all(Request $request)
    {
        try {
            $role = Role::whereNot('name', 'superadmin')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $role
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Hapus image tanda tangan
     */
    public function delete_sign(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            if (Storage::exists('storage/' . $user->sign_path)) {
                Storage::delete('storage/' . $user->sign_path);
            }
            $user->sign_path = null;
            $user->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Deleted!',
                'message' => 'Data Deleted'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
