<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        // Hanya admin dan teknisi yang boleh mengakses halaman daftar user.
        return $user->hasRole('admin') || $user->hasRole('teknisi');
    }

    /**
     * Determine whether the user can view the model.
     */
    // app/Policies/UserPolicy.php

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        // Jika pengguna saat ini adalah admin, dia bisa melihat semua.
        if ($currentUser->role === 'admin') {
            return true;
        }

        // Jika pengguna saat ini adalah teknisi,
        // dia hanya bisa melihat profil pengguna lain (pelanggan)
        // jika mereka berada di cabang yang sama.
        if ($currentUser->role === 'teknisi') {
            return $currentUser->branch_id === $targetUser->branch_id;
        }

        // Pelanggan hanya bisa melihat profilnya sendiri.
        return $currentUser->id === $targetUser->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
