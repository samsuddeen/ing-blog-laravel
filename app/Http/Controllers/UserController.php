<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;


class UserController extends Controller
{

    public function index(Request $request)
    {
        try {
            $users = QueryBuilder::for(User::class)
                ->with('roles')
                ->allowedFilters([
                    AllowedFilter::partial('name'),
                    AllowedFilter::partial('email'),
                    AllowedFilter::exact('role', 'roles.name'),
                ])
                ->latest()
                ->paginate($request->get('per_page', 10))
                ->appends($request->query());

            return UserResource::collection($users)
                ->additional(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Hash password
            $data['password'] = Hash::make($data['password']);

            // Create user
            $user = User::create($data);

            // Assign role if provided
            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();

            return new UserResource($user->load('roles'));
        } catch (\Exception $e) { // Only catch DB or other exceptions
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.',
                $e->getMessage()
            ], 500);
        }
    }


    public function show(User $user)
    {

        try {
            return new UserResource($user->load('roles'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $authUser = $request->user();

        // if (!$authUser->isAdmin() && $authUser->id !== $user->id) {
        //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        // }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if (isset($data['role']) && $authUser->isAdmin()) {
                $user->syncRoles([$data['role']]);
            }

            DB::commit();

           return (new UserResource($user->load('roles')))
        ->additional([
            'success' => true,
            'message' => 'User updated successfully'
        ]);

        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
