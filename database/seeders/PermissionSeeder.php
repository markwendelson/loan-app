<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Branch Management' => [
                'view-branch',
                'create-branch',
                'edit-branch',
                'delete-branch'
            ],
            'Department Management' => [
                'view-department',
                'create-department',
                'edit-department',
                'delete-department'
            ],
            'Job Management' => [
                'view-job',
                'create-job',
                'edit-job',
                'delete-job'
            ],
            'User Management' => [
                'view-user',
                'create-user',
                'edit-user',
                'delete-user'
            ],
            'Insurance Fee Management' => [
                'view-insurance-fee',
                'create-insurance-fee',
                'edit-insurance-fee',
                'delete-insurance-fee'
            ],
            'Notary Fee Management' => [
                'view-notary-fee',
                'create-notary-fee',
                'edit-notary-fee',
                'delete-notary-fee'
            ],
            'Loan Product Management' => [
                'view-loan-product',
                'create-loan-product',
                'edit-loan-product',
                'delete-loan-product'
            ],
            'Loan Type Management' => [
                'view-loan-type',
                'create-loan-type',
                'edit-loan-type',
                'delete-loan-type'
            ],
            'Client Management' => [
                'view-client',
                'create-client',
                'edit-client',
                'delete-client'
            ],
            'Employee Management' => [
                'view-employee',
                'create-employee',
                'edit-employee',
                'delete-employee'
            ],
            'Loan Management' => [
                'view-loan',
                'create-loan',
                'edit-loan',
                'delete-loan',
                'approve-or-decline-loan',
            ],
            'Role Management' => [
                'view-role',
                'create-role',
                'edit-role',
                'delete-role',
                'assign-role',
            ],
            'Permission Management' => [
                'view-permission',
                'create-permission',
                'edit-permission',
                'delete-permission',
                'assign-permission',
            ],

        ];
    }
}
