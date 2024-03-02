<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission ;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    /**
     * List of applications to add.
     */
    private $permissions = [

        'الفواتير',
        'قائمة الفواتير',
        'الفواتير المدفوعة',
        'الفواتير المدفوعة جزئيا',
        'الفواتير الغير مدفوعة',
        'أرشيف الفواتير',
        'التقارير',
        'تقرير الفواتير',
        'تقرير العملاء',
        'المستخدمين',
        'قائمة المستخدمين',
        'صلاحيات المستخدمين',
        'الأعدادات',
        'المنتجات',
        'الأقسام',


        'أضافة فاتورة',
        'حذف الفاتورة',
        'تصدير Excel',
        'تغير حالة الدفع',
        'تعديل الفاتورة',
        'نقل الي الأرشيف',
        'طباعة الفاتورة',
        'أضافة مرفق',
        'حذف المرفق',

        'أضافة مستخدم',
        'تعديل مستخدم',
        'حذف مستخدم',

        'عرض صلاحية',
        'أضافة صلاحية',
        'تعديل صلاحية',
        'حذف صلاحية',

        'أضافة منتج',
        'تعديل منتج',
        'حذف منتج',

        'أضافة قسم',
        'تعديل قسم',
        'حذف قسم',
        'الاشعارات',

    ];



    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin User and assign the role to him.
        $user = User::create([
            'name' => 'Mohamed Essam',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456789'),
            'roles_name' => ["owner"],
            'status' => "مفعل" ,
        ]);

        $role = Role::create(['name' => 'owner']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
