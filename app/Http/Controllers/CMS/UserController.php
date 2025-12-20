<?php

namespace App\Http\Controllers\CMS;

use App\DTOs\UserData;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\CMS\UserService;
use App\Services\impl\ExcelService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'role_id' => $request->input('role_id'),
                'status' => $request->input('status'),
                'sort_by' => $request->input('sort_by', 'id'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ];

            $perPage = $request->get('per_page', 15);
            $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15;

            $users = $this->userRepository->getNonAdminUsers($filters, $perPage);

            $roles = Role::select('id', 'name', 'slug')->get();

            return view('admin.users.index', compact('users', 'roles'));
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
            $user = $this->userService->createUser(
                UserData::fromRequest($request)
            );

            if ($request->input('action') === 'save_and_continue') {
                return redirect()
                    ->route('admin.users.create')
                    ->with('success', 'User created successfully!');
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
            $user = $this->userRepository->find($id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $user = $this->userRepository->findWithTrashed($id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            $roles = Role::select('id', 'name', 'slug')->get();

            return view('admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userRepository->findWithTrashed($id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            $this->userService->updateUser(
                $user,
                UserData::fromRequest($request)
            );

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
            $user = $this->userRepository->find($id);

            if (!$user) {
                return back()->with('error', 'User not found');
            }

            $this->userService->deleteUser($user);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully!'
                ]);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $this->userService->restoreUser($id);

            $user = $this->userRepository->find($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User restored successfully!',
                    'status' => $user?->status ?? true
                ]);
            }

            return back()->with('success', 'User restored successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to restore user: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->userService->forceDeleteUser($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User permanently deleted!'
                ]);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User permanently deleted!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = $this->userRepository->findWithTrashed($id);

            if (!$user) {
                throw new \Exception('User not found');
            }

            $this->userService->toggleUserStatus($user);

            $status = $user->status ? 'active' : 'inactive';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User marked as {$status} successfully!",
                    'status' => $user->status
                ]);
            }

            return back()->with('success', "User marked as {$status} successfully!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function export(ExcelService $excelService)
    {
        $excel = $excelService->exportUsers();
        $filename = 'users_export_' . date('Y-m-d_His') . '.xls';

        return response($excel, 200, $excelService->getDownloadHeaders($filename));
    }

    public function downloadTemplate(ExcelService $excelService)
    {
        $excel = $excelService->generateUserTemplate();
        $filename = 'users_import_template.xls';

        return response($excel, 200, $excelService->getDownloadHeaders($filename));
    }

    public function import(Request $request, ExcelService $excelService)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx|max:2048',
        ]);

        try {
            $file = $request->file('excel_file');
            $content = file_get_contents($file->getRealPath());

            $result = $excelService->importUsers($content);

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
                session()->flash('error', 'No users were imported.');

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
