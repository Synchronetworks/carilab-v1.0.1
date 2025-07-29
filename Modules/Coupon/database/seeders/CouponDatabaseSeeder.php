<?php

namespace Modules\Coupon\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Modules\Coupon\Models\Coupon;
use Modules\Lab\Models\Lab;
use Modules\Coupon\Models\CouponPackageMapping;
use Modules\Coupon\Models\CouponTestMapping;
use Modules\MenuBuilder\Models\MenuBuilder;
use Carbon\Carbon;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;

class CouponDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $coupons = [
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'XRT89U6P',
                'discount_type' => 'percentage', // or 'fixed'
                'discount_value' => 10,
                'applicability' => 'all', // Change as per your logic
                'start_at' => Carbon::parse('2025-02-15'),// Correct Date Format
                'end_at' => Carbon::parse('2025-02-25'),
                'total_usage_limit' => 15,
                'per_customer_usage_limit' => 2,
                'status' => 1,
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'PLM45ZQW',
                'discount_type' => 'percentage', // or 'fixed'
                'discount_value' => 20,
                'applicability' => 'specific_packages',
                'package_id' => 'essential-health-screening-package, advanced-cardiac-lipid-profile-package, comprehensive-liver-function-package', 
                'start_at' => Carbon::parse('2025-02-17'),
                'end_at' => Carbon::parse('2025-03-15'),
                'total_usage_limit' => 10,
                'per_customer_usage_limit' => 1,
                'status' => 1,
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'QWE78TYL',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'applicability' => 'All Packages',
                'start_at' => Carbon::parse('2025-02-19'),
                'end_at' => Carbon::parse('2025-04-05'),
                'total_usage_limit' => 12,
                'per_customer_usage_limit' => 3,
                'status' => 1,
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'JHK90PLN',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'applicability' => 'specific_tests',
                'start_at' => Carbon::parse('2025-02-21'),
                'end_at' => Carbon::parse('2025-05-20'),
                'total_usage_limit' => 10,
                'per_customer_usage_limit' => 1,
                'status' => 1,
                'test_id' => 'complete-blood-count-cbc, blood-glucose-test, lipid-profile, hemoglobin-a1c, serum-electrolytes-test, plasma-osmolality-test, urinalysis-routine, pregnancy-test, protein-in-urine-albumin-test, urine-culture-test, ph-level-of-urine-test, urine-glucose-test, total-cholesterol-test, hdl-cholesterol-test, ldl-cholesterol-test, cholesterolhdl-ratio-test, vldl-cholesterol-test',
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'ZXC23VBT',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'applicability' => 'specific_tests',
                'start_at' => Carbon::parse('2025-02-23'),
                'end_at' => Carbon::parse('2025-06-10'),
                'total_usage_limit' => 15,
                'per_customer_usage_limit' => 2,
                'status' => 1,
                'test_id' => 'liver-function-test, alt-alanine-aminotransferase-test, ast-aspartate-aminotransferase-test, bilirubin-test, alkaline-phosphatase-alp-test, thyroid-stimulating-hormone-tsh-test, free-t4-test, free-t3-test, thyroid-antibodies-test, thyroid-ultrasound, serum-calcitonin-test', // The array for specific_tests IDs
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'RTY34UIK',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'applicability' => 'All Packages',
                'start_at' => Carbon::parse('2025-02-25'),
                'end_at' => Carbon::parse('2025-07-30'),
                'total_usage_limit' => 10,
                'per_customer_usage_limit' => 1,
                'status' => 1,
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'FGH67JKM',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'applicability' => 'all',
                'start_at' => Carbon::parse('2025-02-27'),
                'end_at' => Carbon::parse('2025-08-18'),
                'total_usage_limit' => 15,
                'per_customer_usage_limit' => 2,
                'status' => 1,
            ],
            [
                'lab_id' => 'spectrum-health-diagnostics',
                'coupon_code' => 'ASD12GHX',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'applicability' => 'All Packages',
                'start_at' => Carbon::parse('2025-03-01'),
                'end_at' => Carbon::parse('2025-09-07'),
                'total_usage_limit' => 12,
                'per_customer_usage_limit' => 1,
                'status' => 1,
            ],
            [
                'lab_id' => 'advanced-diagnostics-lab',
                'coupon_code' => 'LMN56OPQ',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'applicability' => 'specific_tests',
                'start_at' => Carbon::parse('2025-03-03'),
                'end_at' => Carbon::parse('2025-10-25'),
                'total_usage_limit' => 10,
                'per_customer_usage_limit' => 2,
                'status' => 1,
                'test_id' => 'complete-blood-count-cbc, blood-glucose-test, serum-electrolytes-test, plasma-osmolality-test, urinalysis-routine, pregnancy-test, ph-level-of-urine-test, urine-glucose-test, total-cholesterol-test, ldl-cholesterol-test, liver-function-test, alt-alanine-aminotransferase-test, albumin-test, alkaline-phosphatase-alp-test, serum_calcitonin_test',
            ],
            [
                'lab_id' => 'precision-medical-lab',
                'coupon_code' => 'BNM89KLW',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'applicability' => 'specific_tests',
                'start_at' => Carbon::parse('2025-03-05'),
                'end_at' => Carbon::parse('2025-12-15'),
                'total_usage_limit' => 10,
                'per_customer_usage_limit' => 2,
                'status' => 1,
                'test_id' => 'complete-blood-count-cbc, blood-glucose-test, lipid-profile, serum-electrolytes-test, plasma-osmolality-test, urinalysis-routine, pregnancy-test, protein-in-urine-albumin-test, ph-level-of-urine-test, urine-glucose-test, total-cholesterol-test, hdl-cholesterol-test, ldl-cholesterol-test, triglycerides-test, vldl-cholesterol-tes',
            ]
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($coupons as $data) {
                $lab = Lab::where('slug', $data['lab_id'])->first();
                $data['lab_id'] = $lab->id;
                $couponsData = Arr::except($data, ['test_id','package_id']);
                $coupon = Coupon::create($couponsData);

                if (!empty($data['test_id'])) {
                    $catalogSlugs = explode(', ', $data['test_id']);
                    $catalogIds = CatlogManagement::whereIn('slug', $catalogSlugs)->pluck('id')->toArray();
                    foreach ($catalogIds as $testId) {
                        CouponTestMapping::create([
                            'coupon_id' => $coupon->id,
                            'test_id' => $testId,
                        ]);
                    }
                }
    
                // Insert into CouponPackageMapping if 'specific_packages' is selected
                if (!empty($data['package_id'])) {
                    $packageSlugs = explode(', ', $data['package_id']);
                    $packageIds = PackageManagement::whereIn('slug', $packageSlugs)->pluck('id')->toArray();                   
                    foreach ($packageIds as $packageId) {
                        CouponPackageMapping::create([
                            'coupon_id' => $coupon->id,
                            'package_id' => $packageId,
                        ]);
                    }
                }
                if(!empty($data['applicability']) && $data['applicability'] == 'All Packages'){
                    $packageIds = PackageManagement::where('lab_id', $data['lab_id'])->pluck('id')->toArray();                   
                    foreach ($packageIds as $packageId) {
                        CouponPackageMapping::create([
                            'coupon_id' => $coupon->id,
                            'package_id' => $packageId,
                        ]);
                    }
                    $data['applicability'] = 'specific_packages';
                    $coupon->update($data);
                }
                
                // if (!empty($data['package_id'])) {
                //     foreach ($data['package_id'] as $packageId) {
                //         CouponPackageMapping::create([
                //             'coupon_id' => $coupon->id,
                //             'package_id' => $packageId,
                //         ]);
                //     }
                // }

            }
         }

         Schema::enableForeignKeyConstraints();
}
    
}
