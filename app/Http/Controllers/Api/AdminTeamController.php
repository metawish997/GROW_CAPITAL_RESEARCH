<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminTeamController extends Controller
{


    private function authorizeSuperAdmin(Request $request)
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized. Super Admin access required.'
            ], 403));
        }
    }

    /**
     * Get list of all team members (admins and staff).
     * GET /api/admin/team
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);
        $query = User::whereIn('role', ['admin', 'staff']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $team = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'team'    => $team
        ]);
    }

    /**
     * Create a new team member.
     * POST /api/admin/team
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin($request);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['required', Rule::in(['admin', 'staff'])],
        ]);

        $member = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully.',
            'member'  => $member
        ], 201);
    }

    /**
     * Update a team member's details.
     * PUT /api/admin/team/{id}
     */
    public function update(Request $request, $id)
    {
        $this->authorizeSuperAdmin($request);
        $member = User::whereIn('role', ['admin', 'staff'])->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($member->id)],
            'password' => 'nullable|string|min:6',
            'role'     => ['required', Rule::in(['admin', 'staff'])],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $member->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully.',
            'member'  => $member
        ]);
    }

    /**
     * Delete a team member.
     * DELETE /api/admin/team/{id}
     */
    public function destroy(Request $request, $id)
    {
        $this->authorizeSuperAdmin($request);
        $member = User::whereIn('role', ['admin', 'staff'])->findOrFail($id);

        // Prevent self-deletion
        if ($member->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        $member->tokens()->delete();
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully.'
        ]);
    }
}
