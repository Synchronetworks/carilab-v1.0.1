<?php

namespace Modules\PackageManagement\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\PackageManagement\Models\PackageCatlogMapping;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\MenuBuilder\Models\MenuBuilder;
use Modules\CatlogManagement\Models\CatlogManagement;

class PackageManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {

            $packages = [
                [
                    'image' => public_path('/dummy-images/packages/essential_health_screening_package.png'),
                    'slug' => 'essential-health-screening-package',
                    'vendor_id' => 4,
                    'lab_id' => 1,
                    'name' => 'Essential Health Screening Package',
                    'description' => 'Ideal for routine health check-ups to assess overall well-being, blood health, and glucose levels.',
                    'price' => 500.00,
                    'start_at' => Carbon::parse('2025-02-01'),
                    'end_at' => Carbon::parse('2025-04-11'),
                    'is_discount' => 1,
                    'discount_type' => 'fixed',
                    'discount_price' => 10,
                    'status' => 1,
                    'is_featured' => 0,
                    'parent_id' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'catalog_ids' => 'complete-blood-count-cbc, blood-glucose-test, lipid-profile, hemoglobin-a1c, serum-electrolytes-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/advanced_cardiac_lipid_profile_package.png'),
                    'slug' => 'advanced-cardiac-lipid-profile-package',
                    'vendor_id' => 4,
                    'lab_id' => 1,
                    'name' => 'Advanced Cardiac & Lipid Profile Package',
                    'description' => 'Helps evaluate heart health, cholesterol levels, and risk factors for cardiovascular diseases.',
                    'price' => 300.00,
                    'start_at' => Carbon::parse('2025-03-01'),
                    'end_at' => Carbon::parse('2025-04-19'),
                    'is_discount' => 1,
                    'discount_type' => 'percentage',
                    'discount_price' => 15,
                    'status' => 1,
                    'is_featured' => 0,
                    'parent_id' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'catalog_ids' => 'lipid-profile, cholesterolhdl-ratio-test, vldl-cholesterol-test, plasma-osmolality-test, serum-electrolytes-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/comprehensive_liver_function_package.png'),
                    'slug' => 'comprehensive-liver-function-package',
                    'vendor_id' => 4,
                    'lab_id' => 1,
                    'name' => 'Comprehensive Liver Function Package',
                    'description' => 'Designed to assess liver health, detect liver damage, and monitor liver function for individuals with liver-related conditions.',
                    'price' => 250.00,
                    'start_at' => Carbon::parse('2025-04-01'),
                    'end_at' => Carbon::parse('2025-04-30'),
                    'is_discount' => 1,
                    'discount_type' => 'fixed',
                    'discount_price' => 5,
                    'status' => 1,
                    'is_featured' => 0,
                    'parent_id' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'catalog_ids' => 'liver-function-test, alt-alanine-aminotransferase-test, ast-aspartate-aminotransferase-test, bilirubin-test, albumin-test, alkaline-phosphatase-alp-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/complete_thyroid_health_package.png'),
                    'slug' => 'complete-thyroid-health-package',
                    'vendor_id' => 4,
                    'lab_id' => 1,
                    'name' => 'Complete Thyroid Health Package',
                    'description' => 'A comprehensive package for diagnosing and monitoring thyroid disorders, including hypothyroidism and hyperthyroidism.',
                    'price' => 450.00,
                    'start_at' => Carbon::parse('2025-05-01'),
                    'end_at' => Carbon::parse('2025-05-31'),
                    'is_discount' => 1,
                    'discount_type' => 'percentage',
                    'discount_price' => 12,
                    'status' => 1,
                    'is_featured' => 0,
                    'parent_id' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'catalog_ids' => 'thyroid-stimulating-hormone-tsh-test, free-t4-test, free-t3-test, thyroid-antibodies-test, serum-calcitonin-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/full_body_wellness_package.png'),
                    'slug' => 'full-body-wellness-package',
                    'vendor_id' => 4,
                    'lab_id' => 1,
                    'name' => 'Full Body Wellness Package',
                    'description' => 'A holistic health check-up to monitor vital organs, blood health, and metabolic function.',
                    'price' => 400.00,
                    'start_at' => Carbon::parse('2025-06-01'),
                    'end_at' => Carbon::parse('2025-06-30'),
                    'is_discount' => 1,
                    'discount_type' => 'percentage',
                    'discount_price' => 10,
                    'status' => 1,
                    'is_featured' => 0,
                    'parent_id' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'catalog_ids' => "complete-blood-count-cbc, blood_glucose_test, lipid_profile, liver-function-test, thyroid-stimulating-hormone-tsh-test, serum-electrolytes-test, alt-alanine-aminotransferase-test, bilirubin-test",

                ],
                [
                    'image' => public_path('/dummy-images/packages/basic_health_screening_package.png'),
                    'slug' => 'basic-health-screening-package',
                    'vendor_id' => 4,  // Adjust based on actual vendor ID
                    'lab_id' => 2,     // Adjust based on actual lab ID
                    'name' => 'Basic Health Screening Package',
                    'description' => 'Tests for pregnancy, blood tests, and early-stage health monitoring. 🤰',
                    'price' => 350.00,
                    'start_at' => Carbon::parse('2025-07-01'),
                    'end_at' => Carbon::parse('2025-07-31'),
                    'is_discount' => 0,  // Set based on discount requirement
                    'discount_type' => 'fixed',
                    'discount_price' => 8,
                    'status' => 1,  // Active status
                    'is_featured' => 0,  // Featured or not
                    'parent_id' => null,
                    'created_by' => 1,  // Adjust based on actual user ID
                    'updated_by' => 1,  // Adjust based on actual user ID
                    'deleted_by' => null,
                    'catalog_ids' => 'complete-blood-count-cbc, blood-glucose-test, serum-electrolytes-test, plasma-osmolality-test, urinalysis-routine',
                ],
                [
                    'image' => public_path('/dummy-images/packages/liver_cholesterol_health_package.png'),
                    'slug' => 'liver-cholesterol-health-package',
                    'vendor_id' => 4,  // Adjust based on actual vendor ID
                    'lab_id' => 2,     // Adjust based on actual lab ID
                    'name' => 'Liver & Cholesterol Health Package',
                    'description' => 'Designed to evaluate liver function and cholesterol levels for better heart and liver health 🏥💖',
                    'price' => 200.00,
                    'start_at' => Carbon::parse('2025-08-01'),
                    'end_at' => Carbon::parse('2025-08-31'),
                    'is_discount' => 1,  // Assuming discount is applied
                    'discount_type' => 'percentage',
                    'discount_price' => 7,  // 7% discount
                    'status' => 1,  // Active status
                    'is_featured' => 0,  // Featured or not
                    'parent_id' => null,
                    'created_by' => 1,  // Adjust based on actual user ID
                    'updated_by' => 1,  // Adjust based on actual user ID
                    'deleted_by' => null,
                    'catalog_ids' => 'liver-function-test, alt-alanine-aminotransferase-test, albumin-test, alkaline-phosphatase-alp-test, total-cholesterol-test, ldl-cholesterol-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/comprehensive_wellness_pregnancy_package.png'),
                    'slug' => 'comprehensive-wellness-pregnancy-package',
                    'vendor_id' => 4,  // Adjust based on actual vendor ID
                    'lab_id' => 2,     // Adjust based on actual lab ID
                    'name' => 'Comprehensive Wellness & Pregnancy Package',
                    'description' => 'A specialized package for expecting mothers and individuals monitoring hormonal and metabolic health 🤰💡',
                    'price' => 150.00,
                    'start_at' => Carbon::parse('2025-09-01'),
                    'end_at' => Carbon::parse('2025-09-30'),
                    'is_discount' => 1,  // Assuming discount is applied
                    'discount_type' => 'percentage',
                    'discount_price' => 10,  // 10% discount
                    'status' => 1,  // Active status
                    'is_featured' => 0,  // Featured or not
                    'parent_id' => null,
                    'created_by' => 1,  // Adjust based on actual user ID
                    'updated_by' => 1,  // Adjust based on actual user ID
                    'deleted_by' => null,
                    'catalog_ids' => 'pregnancy-test,ph-level-of-urine-test,urine-glucose-test,serum_calcitonin_test,liver-function-test',
                ],
                [
                    'image' => public_path('/dummy-images/packages/essential_health_wellness_package.png'),
                    'slug' => 'essential-health-wellness-package',
                    'vendor_id' => 4,  // Adjust based on actual vendor ID
                    'lab_id' => 3,     // Adjust based on actual lab ID
                    'name' => 'Essential Health & Wellness Package',
                    'description' => 'A well-rounded package to assess overall health, detect imbalances, and monitor key health markers 🩺✅',
                    'price' => 400.00,
                    'start_at' => Carbon::parse('2025-10-01'),
                    'end_at' => Carbon::parse('2025-10-31'),
                    'is_discount' => 1,  // Assuming discount is applied
                    'discount_type' => 'percentage',
                    'discount_price' => 20,  // 20% discount
                    'status' => 1,  // Active status
                    'is_featured' => 0,  // Featured or not
                    'parent_id' => null,
                    'created_by' => 1,  // Adjust based on actual user ID
                    'updated_by' => 1,  // Adjust based on actual user ID
                    'deleted_by' => null,
                    'catalog_ids' => 'complete-blood-count-cbc, blood-glucose-test, lipid-profile, serum-electrolytes-test, plasma-osmolality-test, urinalysis-routine',
                ],
                [
                    'image' => public_path('/dummy-images/packages/advanced_metabolic_heart_health_package.png'),
                    'slug' => 'advanced-metabolic-heart-health-package',
                    'vendor_id' => 4,  // Adjust based on actual vendor ID
                    'lab_id' => 3,     // Adjust based on actual lab ID
                    'name' => 'Advanced Metabolic & Heart Health Package',
                    'description' => 'Designed to evaluate heart health, lipid profile, and metabolic function for early risk detection ❤️🔬',
                    'price' => 500.00,
                    'start_at' => Carbon::parse('2025-11-01'),
                    'end_at' => Carbon::parse('2025-11-30'),
                    'is_discount' => 1,  // Assuming discount is applied
                    'discount_type' => 'percentage',
                    'discount_price' => 10,  // 10% discount
                    'status' => 1,  // Active status
                    'is_featured' => 0,  // Featured or not
                    'parent_id' => null,
                    'created_by' => 1,  // Adjust based on actual user ID
                    'updated_by' => 1,  // Adjust based on actual user ID
                    'deleted_by' => null,
                    'catalog_ids' => 'total-cholesterol-test, hdl-cholesterol-test, ldl-cholesterol-test, triglycerides-test, vldl-cholesterol-test, pregnancy-test, protein-in-urine-albumin-test, ph-level-of-urine-test, urine-glucose-test',
                ]

            ];

            foreach ($packages as $data) {
                $image = $data['image'] ?? null;
                $packageData = Arr::except($data, ['image','catalog_ids']);

                $catalogSlugs = explode(', ', $data['catalog_ids']);
                $catalogIds = CatlogManagement::whereIn('slug', $catalogSlugs)->pluck('id')->toArray();

                // Create Package
                $package = PackageManagement::create($packageData);

                // Attach Image (If Exists)
                if ($image && file_exists($image)) {
                    $this->attachFeatureImage($package, $image);
                }

                $this->storeCatalogMappings($package,$catalogIds);
            }
        }
    }

    private function attachFeatureImage($model, $publicPath)
    {
        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('package_image');

        return $media;

    }

    private function storeCatalogMappings($package, $catalogIds)
    {
        // Prepare the catalog mapping data
        $catalogMappings = [];
        foreach ($catalogIds as $catalogId) {
            $catalogMappings[] = [
                'package_id' => $package->id,
                'catalog_id' => $catalogId,
            ];
        }

        // Insert the catalog mappings into the PackageCatlogMapping table
        PackageCatlogMapping::insert($catalogMappings);
    }
}
