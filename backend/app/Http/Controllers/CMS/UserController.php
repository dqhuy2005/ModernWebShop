<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $baseQuery = $this->buildBaseUserQuery($request);

            $totalUsers = (clone $baseQuery)->count();
            $activeUsers = (clone $baseQuery)->where('status', true)->whereNull('deleted_at')->count();
            $inactiveUsers = (clone $baseQuery)->where('status', false)->whereNull('deleted_at')->count();
            $deletedUsers = (clone $baseQuery)->onlyTrashed()->count();

            $query = (clone $baseQuery);

            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'active':
                        $query->where('status', true)->whereNull('deleted_at');
                        break;
                    case 'inactive':
                        $query->where('status', false)->whereNull('deleted_at');
                        break;
                    case 'deleted':
                        $query->onlyTrashed();
                        break;
                    default:
                        break;
                }
            }

            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['id', 'fullname', 'email', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'id';
            }

            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $allowedPerPage = [15, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 15;
            }

            $users = $query->paginate($perPage)->withQueryString();

            $roles = Role::select('id', 'name', 'slug')->get();

            return view('admin.users.index', compact('users', 'roles', 'totalUsers', 'activeUsers', 'inactiveUsers', 'deletedUsers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $roles = Role::select('id', 'name', 'slug')->get();
            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->except(['image', 'password', 'password_confirmation', 'action']);

            $data['password'] = Hash::make($request->password);

            $data['status'] = $request->has('status') ? 1 : 0;

            if (!isset($data['role_id'])) {
                $userRole = Role::where('slug', 'user')->first();
                if ($userRole) {
                    $data['role_id'] = $userRole->id;
                }
            } else {
                $selectedRole = Role::find($data['role_id']);
                if ($selectedRole && strtolower($selectedRole->name) === 'admin') {
                    $userRole = Role::where('slug', 'user')->first();
                    if ($userRole) {
                        $data['role_id'] = $userRole->id;
                    }
                }
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $request->fullname) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('users', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $user = User::create($data);

            if ($request->input('action') === 'save_and_continue') {
                return redirect()
                    ->route('admin.users.create')
                    ->with('success', 'User created successfully! You can add another one.');
            }

            return redirect()
                ->route('admin.users.show', $user->id)
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = User::withTrashed()
                ->with(['role', 'carts', 'orders'])
                ->findOrFail($id);

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $roles = Role::select('id', 'name', 'slug')->get();

            return view('admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $data = $request->except(['image', 'password', 'password_confirmation', '_method', '_token']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $data['status'] = $request->has('status') ? 1 : 0;

            if (isset($data['role_id'])) {
                $currentRole = Role::find($user->role_id);
                $newRole = Role::find($data['role_id']);

                if ($currentRole && strtolower($currentRole->name) === 'admin') {
                } elseif ($newRole && strtolower($newRole->name) === 'admin') {
                    $userRole = Role::where('slug', 'user')->first();
                    if ($userRole) {
                        $data['role_id'] = $userRole->id;
                    }
                }
            }

            if ($request->hasFile('image')) {
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $request->fullname) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('users', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $user->update($data);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                return back()->with('error', 'You cannot delete yourself!');
            }

            $user->delete();

            if (request()->ajax()) {
                $base = $this->buildBaseUserQuery(request());
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully! You can restore it later.',
                    'counts' => [
                        'total' => (clone $base)->count(),
                        'active' => (clone $base)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $base)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $base)->onlyTrashed()->count(),
                    ],
                ]);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully! You can restore it later.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            if (request()->ajax()) {
                $base = $this->buildBaseUserQuery(request());
                return response()->json([
                    'success' => true,
                    'message' => 'User restored successfully!',
                    'status' => $user->status,
                    'counts' => [
                        'total' => (clone $base)->count(),
                        'active' => (clone $base)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $base)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $base)->onlyTrashed()->count(),
                    ],
                ]);
            }

            return back()->with('success', 'User restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore user: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if ($user->id === Auth::id()) {
                return back()->with('error', 'You cannot delete yourself!');
            }

            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $user->forceDelete();

            if (request()->ajax()) {
                $base = $this->buildBaseUserQuery(request());
                return response()->json([
                    'success' => true,
                    'message' => 'User permanently deleted!',
                    'counts' => [
                        'total' => (clone $base)->count(),
                        'active' => (clone $base)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $base)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $base)->onlyTrashed()->count(),
                    ],
                ]);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User permanently deleted!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete user: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if ($user->id === Auth::id()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot change your own status!',
                    ], 403);
                }

                return back()->with('error', 'You cannot change your own status!');
            }

            $user->status = !$user->status;
            $user->save();

            $status = $user->status ? 'active' : 'inactive';

            if (request()->ajax()) {
                $base = $this->buildBaseUserQuery(request());
                return response()->json([
                    'success' => true,
                    'message' => "User marked as {$status} successfully!",
                    'status' => $user->status,
                    'counts' => [
                        'total' => (clone $base)->count(),
                        'active' => (clone $base)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $base)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $base)->onlyTrashed()->count(),
                    ],
                ]);
            }

            return back()->with('success', "User marked as {$status} successfully!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle status: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to toggle status: ' . $e->getMessage());
        }
    }

    protected function buildBaseUserQuery(Request $request)
    {
        $baseQuery = User::withTrashed()->with('role');

        $baseQuery->whereDoesntHave('role', function ($q) {
            $q->where('slug', Role::ADMIN);
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $baseQuery;
    }
}
