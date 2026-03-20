<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UserManagementService
{
    public function getUsersForManagement($userRole)
    {
        $query = User::query();
        
        // Managers can see all users except other admins
        if ($userRole === 'manager') {
            $query->where('role', '!=', 'admin');
        }
        
        return $query;
    }
    
    public function canCreateUser($userRole, $targetRole): bool
    {
        // Managers can create cashiers and inventory clerks only
        if ($userRole === 'manager') {
            return in_array($targetRole, ['cashier', 'inventory_clerk']);
        }
        
        // Admins can create anyone
        return $userRole === 'admin';
    }
    
    public function canEditUser($userRole, $targetUserId): bool
    {
        $targetUser = User::find($targetUserId);
        
        if (!$targetUser) {
            return false;
        }
        
        // Admins can edit anyone
        if ($userRole === 'admin') {
            return true;
        }
        
        // Managers can edit cashiers and inventory clerks only
        if ($userRole === 'manager') {
            return in_array($targetUser->role, ['cashier', 'inventory_clerk']);
        }
        
        return false;
    }
    
    public function canDeleteUser($userRole, $targetUserId): bool
    {
        // Only admins can delete users
        return $userRole === 'admin';
    }
    
    public function canChangeRole($userRole, $targetRole): bool
    {
        // Only admins can change roles
        return $userRole === 'admin';
    }
    
    public function getAvailableRolesForCreation($userRole): array
    {
        if ($userRole === 'manager') {
            return [
                'cashier' => 'Cashier',
                'inventory_clerk' => 'Inventory Clerk'
            ];
        }
        
        if ($userRole === 'admin') {
            return [
                'cashier' => 'Cashier',
                'inventory_clerk' => 'Inventory Clerk',
                'manager' => 'Manager',
                'admin' => 'Admin'
            ];
        }
        
        return [];
    }
    
    public function getAvailableRolesForEditing($userRole, $targetUserId): array
    {
        $targetUser = User::find($targetUserId);
        
        if (!$targetUser) {
            return [];
        }
        
        // Admins can edit anyone's role
        if ($userRole === 'admin') {
            return [
                'cashier' => 'Cashier',
                'inventory_clerk' => 'Inventory Clerk',
                'manager' => 'Manager',
                'admin' => 'Admin'
            ];
        }
        
        // Managers cannot change roles
        return [];
    }
    
    public function logUserAction($userId, $action, $details = null)
    {
        \App\Models\UserActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
        
        return true;
    }
}
