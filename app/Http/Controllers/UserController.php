<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware für Berechtigungen
        /*$this->middleware('permission:view users', ['only' => ['index']]);
        $this->middleware('permission:create users', ['only' => ['create', 'store', 'invite', 'sendInvitation']]);
        $this->middleware('permission:edit users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete users', ['only' => ['destroy']]);*/
    }
    
    public function index(Request $request)
    {
        $query = User::query();
    
        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Sammle alle aktiven Filter
        $activeFilters = [];
        
        // Verifiziert
        if ($request->has('verified') && $request->input('verified') === '1') {
            $activeFilters[] = ['column' => 'email_verified_at', 'condition' => 'not_null'];
        }
        
        // Nicht verifiziert
        if ($request->has('not_verified') && $request->input('not_verified') === '1') {
            $activeFilters[] = ['column' => 'email_verified_at', 'condition' => 'null'];
        }
        
        // 2FA aktiv
        if ($request->has('two_fa_active') && $request->input('two_fa_active') === '1') {
            $activeFilters[] = ['column' => 'two_factor_confirmed_at', 'condition' => 'not_null'];
        }
        
        // 2FA nicht aktiv
        if ($request->has('two_fa_not_active') && $request->input('two_fa_not_active') === '1') {
            $activeFilters[] = ['column' => 'two_factor_confirmed_at', 'condition' => 'null'];
        }
        
        // Wenn Filter aktiv sind, OR-Logik anwenden
        if (!empty($activeFilters)) {
            $query->where(function ($q) use ($activeFilters) {
                foreach ($activeFilters as $filter) {
                    if ($filter['condition'] === 'null') {
                        $q->orWhereNull($filter['column']);
                    } else {
                        $q->orWhereNotNull($filter['column']);
                    }
                }
            });
        }
        
        $users = $query->with(['roles.permissions'])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                    'created_at' => $user->created_at,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'color' => $role->color ?? null
                        ];
                    }),
                    'permission_count' => $user->getAllPermissions()->count()
                ];
            });
    
        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => [
                    'verified' => $request->input('verified') === '1',
                    'not_verified' => $request->input('not_verified') === '1',
                    'two_fa_active' => $request->input('two_fa_active') === '1',
                    'two_fa_not_active' => $request->input('two_fa_not_active') === '1',
                ]
            ]
        ]);
    }

    public function create()
    {
        // Hole alle verfügbaren Rollen aus Spatie
        $roles = Role::orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description ?? '',
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'is_default' => $role->is_default ?? false
                ];
            });

        return Inertia::render('users/Create', [
            'availableRoles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'email_verified' => ['boolean'],
            'send_welcome_email' => ['boolean']
        ]);

        // Benutzer erstellen
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $validated['email_verified'] ? now() : null
        ]);

        // Rolle mit Spatie zuweisen
        $role = Role::findOrFail($validated['role_id']);
        $user->assignRole($role);

        if ($validated['send_welcome_email'] ?? false) {
            // Hier kannst du eine Willkommens-E-Mail senden
            // event(new UserCreated($user, $validated['password']));
        }

        event(new Registered($user));


        return to_route('users.index');

    }

    public function edit(User $user)
    {
        // Berechtigungsprüfung
        if (!auth()->user()->can('edit users') && auth()->user()->id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diesen Benutzer zu bearbeiten.');
        }

        $roles = Role::orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->getAttribute('description') ?? '',
                    'permissions' => $role->permissions->pluck('name')->toArray()
                ];
            });

        $userRoles = $user->roles->pluck('id')->toArray();

        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'roles' => $userRoles
            ],
            'availableRoles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Berechtigungsprüfung
        if (!auth()->user()->can('edit users') && auth()->user()->id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diesen Benutzer zu bearbeiten.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'email_verified' => ['boolean']
        ]);

        DB::beginTransaction();

        try {
            // Benutzer aktualisieren
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'email_verified_at' => $validated['email_verified'] ? now() : null
            ]);

            // Rollen mit Spatie synchronisieren
            $role = Role::findOrFail($validated['role_id']);
            $user->syncRoles([$role->id]);

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Benutzer wurde erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Fehler beim Aktualisieren des Benutzers: ' . $e->getMessage()
                ]);
        }
    }

    public function destroy(User $user)
    {
        // Verhindere, dass sich ein Benutzer selbst löscht
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Sie können Ihren eigenen Account nicht löschen.');
        }

        DB::beginTransaction();

        try {
            // Rollen mit Spatie entfernen
            $user->roles()->detach();
            
            // Benutzer löschen
            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Benutzer wurde erfolgreich gelöscht.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors([
                    'error' => 'Fehler beim Löschen des Benutzers: ' . $e->getMessage()
                ]);
        }
    }

    public function invite()
    {
        // Hole verfügbare Rollen für Einladung
        $roles = Role::orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->getAttribute('description') ?? ''
                ];
            });
        
        return Inertia::render('users/Invite', [
            'availableRoles' => $roles,
            'pendingInvitations' => []
        ]);
    }

}