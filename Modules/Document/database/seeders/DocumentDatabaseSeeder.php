<?php

namespace Modules\Document\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Document\Models\Document;

class DocumentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documents = [
            [
                'created_at' => '2023-10-12 08:07:13',
                'deleted_at' => null,
                'is_required' => 0,
                'name' => 'Business Registration Certificate',
                'user_type' => 'vendor',
                'status' => 1,
                'updated_at' => '2023-10-12 08:07:13',
                'slug' => 'business-registration-certificate',
            ],
            [
                'created_at' => '2023-10-12 08:07:32',
                'deleted_at' => null,
                'is_required' => 0,
                'name' => 'Tax Identification Number (TIN)',
                'user_type' => 'vendor',
                'status' => 1,
                'updated_at' => '2023-10-12 08:07:32',
                'slug' => 'tax-identification-number-tin',
            ],
            [
                'created_at' => '2023-10-12 08:07:43',
                'deleted_at' => null,
                'is_required' => 1,
                'name' => 'Laboratory Accreditation Certificate',
                'user_type' => 'vendor',
                'status' => 1,
                'updated_at' => '2023-10-12 08:07:43',
                'slug' => 'laboratory-accreditation-certificate',
            ],
            [
                'created_at' => '2023-10-12 08:07:57',
                'deleted_at' => null,
                'is_required' => 0,
                'name' => 'Owner/Manager ID Proof',
                'user_type' => 'vendor',
                'status' => 1,
                'updated_at' => '2023-10-12 08:08:21',
                'slug' => 'ownermanager-id-proof',
            ],
            [
                'created_at' => '2023-10-12 08:08:14',
                'deleted_at' => null,
                'is_required' => 1,
                'name' => "Government-issued ID Proof (Passport, Driver’s License, etc)",
                'user_type' => 'collector',
                'status' => 1,
                'updated_at' => '2023-10-12 08:08:14',
                'slug' => 'government-issued-id-proof-passport-drivers-license-etc',
            ],
            [
                'created_at' => '2023-10-12 08:08:18',
                'deleted_at' => null,
                'is_required' => 1,
                'name' => "Educational Certificates",
                'user_type' => 'collector',
                'status' => 1,
                'updated_at' => '2023-10-12 08:08:28',
                'slug' => 'educational-certificates',
            ],
            [
                'created_at' => '2023-10-12 08:08:40',
                'deleted_at' => null,
                'is_required' => 1,
                'name' => "Signed Contract/Agreement",
                'user_type' => 'collector',
                'status' => 1,
                'updated_at' => '2023-10-12 08:08:45',
                'slug' => 'signed-contractagreement',
            ],
        ];
        
        foreach ($documents as $document) {
            Document::create($document);
        }
    }
}
