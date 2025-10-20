<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Services\CsvService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = User::withTrashed()->with('role')
                ->whereDoesntHave('role', function ($q) {
                    $q->where('slug', Role::ADMIN);
                });

            $this->applyUserFilters($query, $request);

            $totalUsers = (clone $query)->count();
            $activeUsers = (clone $query)->where('status', true)->whereNull('deleted_at')->count();
            $inactiveUsers = (clone $query)->where('status', false)->whereNull('deleted_at')->count();
            $deletedUsers = (clone $query)->onlyTrashed()->count();

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
                }
            }

            $this->applySorting(
                $query,
                $request,
                'id',
                'desc',
                ['id', 'fullname', 'email', 'phone', 'created_at', 'updated_at']
            );

            $perPage = $request->get('per_page', 15);
            $allowedPerPage = [15, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 15;
            }

            $users = $query->paginate($perPage)->withQueryString();

            $roles = Role::select('id', 'name', 'slug')->get();

            return view('admin.users.index', compact(
                'users',
                'roles',
                'totalUsers',
                'activeUsers',
                'inactiveUsers',
                'deletedUsers'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    protected function applyUserFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        return $query;
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
                $imageService = new ImageService();

                if (!$imageService->validateImage($request->file('image'))) {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid avatar image. Please check file size (max 2MB) and format (jpg, png, gif, webp).');
                }

                $data['image'] = $imageService->uploadAvatar($request->file('image'));
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
                $imageService = new ImageService();

                if (!$imageService->validateImage($request->file('image'))) {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid avatar image. Please check file size (max 2MB) and format (jpg, png, gif, webp).');
                }

                $data['image'] = $imageService->uploadAvatar(
                    $request->file('image'),
                    $user->image
                );
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
                $query = User::withTrashed()->with('role')
                    ->whereDoesntHave('role', function ($q) {
                        $q->where('slug', Role::ADMIN);
                    });
                $this->applyUserFilters($query, request());

                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully! You can restore it later.',
                    'counts' => [
                        'total' => (clone $query)->count(),
                        'active' => (clone $query)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $query)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $query)->onlyTrashed()->count(),
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
                $query = User::withTrashed()->with('role')
                    ->whereDoesntHave('role', function ($q) {
                        $q->where('slug', Role::ADMIN);
                    });
                $this->applyUserFilters($query, request());

                return response()->json([
                    'success' => true,
                    'message' => 'User restored successfully!',
                    'status' => $user->status,
                    'counts' => [
                        'total' => (clone $query)->count(),
                        'active' => (clone $query)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $query)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $query)->onlyTrashed()->count(),
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

            if ($user->image) {
                $imageService = new ImageService();
                $imageService->deleteImage($user->image);
            }

            $user->forceDelete();

            if (request()->ajax()) {
                $query = User::withTrashed()->with('role')
                    ->whereDoesntHave('role', function ($q) {
                        $q->where('slug', Role::ADMIN);
                    });
                $this->applyUserFilters($query, request());

                return response()->json([
                    'success' => true,
                    'message' => 'User permanently deleted!',
                    'counts' => [
                        'total' => (clone $query)->count(),
                        'active' => (clone $query)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $query)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $query)->onlyTrashed()->count(),
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
                $query = User::withTrashed()->with('role')
                    ->whereDoesntHave('role', function ($q) {
                        $q->where('slug', Role::ADMIN);
                    });
                $this->applyUserFilters($query, request());

                return response()->json([
                    'success' => true,
                    'message' => "User marked as {$status} successfully!",
                    'status' => $user->status,
                    'counts' => [
                        'total' => (clone $query)->count(),
                        'active' => (clone $query)->where('status', true)->whereNull('deleted_at')->count(),
                        'inactive' => (clone $query)->where('status', false)->whereNull('deleted_at')->count(),
                        'deleted' => (clone $query)->onlyTrashed()->count(),
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



    /**
     * Export users to CSV
     */
    public function export(CsvService $csvService)
    {
        $csv = $csvService->exportUsers();
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';

        return response($csv, 200, $csvService->getDownloadHeaders($filename));
    }

    /**
     * Download CSV template for import
     */
    public function downloadTemplate(CsvService $csvService)
    {
        $csv = $csvService->generateUserTemplate();
        $filename = 'users_import_template.csv';

        return response($csv, 200, $csvService->getDownloadHeaders($filename));
    }

    /**
     * Import users from CSV
     */
    public function import(Request $request, CsvService $csvService)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            $result = $csvService->importUsers($filePath);

            if ($result['success'] > 0) {
                $message = "Successfully imported {$result['success']} user(s).";

                if ($result['failed'] > 0) {
                    $message .= " {$result['failed']} user(s) failed.";
                }

                session()->flash('success', $message);

                if (!empty($result['errors'])) {
                    session()->flash('import_errors', $result['errors']);
                }
            } else {
                session()->flash('error', 'No users were imported. Please check your CSV file.');

                if (!empty($result['errors'])) {
                    session()->flash('import_errors', $result['errors']);
                }
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index');
    }
}
