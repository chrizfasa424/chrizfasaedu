<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::transaction(function () use ($now): void {
            $adminRoles = ['super_admin', 'school_admin'];
            $staffRoles = ['teacher', 'principal', 'vice_principal', 'accountant', 'librarian', 'nurse', 'driver', 'staff'];

            $adminUsers = DB::table('users')
                ->whereIn('role', $adminRoles)
                ->get();

            foreach ($adminUsers as $user) {
                DB::table('admins')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'school_id' => $user->school_id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'other_names' => $user->other_names,
                        'phone' => $user->phone,
                        'password' => $user->password,
                        'avatar' => $user->avatar,
                        'is_active' => (bool) $user->is_active,
                        'last_login_at' => $user->last_login_at,
                        'last_login_ip' => $user->last_login_ip,
                        'remember_token' => $user->remember_token,
                        'created_at' => $user->created_at ?? $now,
                        'updated_at' => $now,
                        'deleted_at' => $user->deleted_at,
                    ]
                );
            }

            $legacyStaffUsers = DB::table('staff')
                ->join('users', 'users.id', '=', 'staff.user_id')
                ->whereIn('users.role', $staffRoles)
                ->select([
                    'staff.school_id',
                    'staff.staff_id_number',
                    'staff.employee_type',
                    'staff.department',
                    'staff.designation',
                    'staff.qualification',
                    'staff.date_of_employment',
                    'staff.date_of_birth',
                    'staff.gender',
                    'staff.marital_status',
                    'staff.nationality',
                    'staff.state_of_origin',
                    'staff.address',
                    'staff.city',
                    'staff.state',
                    'staff.bank_name',
                    'staff.account_number',
                    'staff.account_name',
                    'staff.basic_salary',
                    'staff.allowances',
                    'staff.deductions',
                    'staff.photo',
                    'staff.resume',
                    'staff.status',
                    'staff.deleted_at',
                    'staff.created_at',
                    'users.first_name',
                    'users.last_name',
                    'users.other_names',
                    'users.email',
                    'users.phone',
                    'users.password',
                    'users.remember_token',
                    'users.last_login_at',
                    'users.last_login_ip',
                    'users.avatar as user_avatar',
                ])
                ->get();

            foreach ($legacyStaffUsers as $staff) {
                $photo = $staff->photo ?: $staff->user_avatar;
                $match = [
                    'school_id' => $staff->school_id,
                    'staff_id_number' => $staff->staff_id_number ?: ('legacy-user-' . md5((string) $staff->email)),
                ];

                DB::table('staffs')->updateOrInsert(
                    $match,
                    [
                        'email' => $staff->email,
                        'first_name' => $staff->first_name,
                        'last_name' => $staff->last_name,
                        'other_names' => $staff->other_names,
                        'phone' => $staff->phone,
                        'employee_type' => $staff->employee_type ?: 'full_time',
                        'department' => $staff->department,
                        'designation' => $staff->designation,
                        'qualification' => $staff->qualification,
                        'date_of_employment' => $staff->date_of_employment,
                        'date_of_birth' => $staff->date_of_birth,
                        'gender' => $staff->gender,
                        'marital_status' => $staff->marital_status,
                        'nationality' => $staff->nationality ?: 'Nigerian',
                        'state_of_origin' => $staff->state_of_origin,
                        'address' => $staff->address,
                        'city' => $staff->city,
                        'state' => $staff->state,
                        'bank_name' => $staff->bank_name,
                        'account_number' => $staff->account_number,
                        'account_name' => $staff->account_name,
                        'basic_salary' => $staff->basic_salary ?? 0,
                        'allowances' => $staff->allowances,
                        'deductions' => $staff->deductions,
                        'photo' => $photo,
                        'resume' => $staff->resume,
                        'status' => $staff->status ?: 'active',
                        'password' => $staff->password,
                        'remember_token' => $staff->remember_token,
                        'created_at' => $staff->created_at ?? $now,
                        'updated_at' => $now,
                        'deleted_at' => $staff->deleted_at,
                    ]
                );
            }

            $migratedStaffEmails = DB::table('staffs')
                ->whereNotNull('email')
                ->pluck('email')
                ->all();

            $remainingStaffUsers = DB::table('users')
                ->whereIn('role', $staffRoles)
                ->whereNotIn('email', $migratedStaffEmails)
                ->get();

            foreach ($remainingStaffUsers as $user) {
                DB::table('staffs')->updateOrInsert(
                    [
                        'school_id' => $user->school_id,
                        'staff_id_number' => 'legacy-user-' . $user->id,
                    ],
                    [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'other_names' => $user->other_names,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'employee_type' => 'full_time',
                        'designation' => str_replace('_', ' ', (string) $user->role),
                        'nationality' => 'Nigerian',
                        'basic_salary' => 0,
                        'photo' => $user->avatar,
                        'status' => (bool) $user->is_active ? 'active' : 'inactive',
                        'password' => $user->password,
                        'remember_token' => $user->remember_token,
                        'created_at' => $user->created_at ?? $now,
                        'updated_at' => $now,
                        'deleted_at' => $user->deleted_at,
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        $adminRoles = ['super_admin', 'school_admin'];
        $staffRoles = ['teacher', 'principal', 'vice_principal', 'accountant', 'librarian', 'nurse', 'driver', 'staff'];

        $adminEmails = DB::table('users')
            ->whereIn('role', $adminRoles)
            ->pluck('email')
            ->all();

        if (!empty($adminEmails)) {
            DB::table('admins')->whereIn('email', $adminEmails)->delete();
        }

        $staffEmails = DB::table('users')
            ->whereIn('role', $staffRoles)
            ->pluck('email')
            ->all();

        if (!empty($staffEmails)) {
            DB::table('staffs')->whereIn('email', $staffEmails)->delete();
        }

        DB::table('staffs')
            ->where('staff_id_number', 'like', 'legacy-user-%')
            ->delete();
    }
};
