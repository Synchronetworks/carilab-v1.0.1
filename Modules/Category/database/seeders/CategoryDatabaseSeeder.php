<?php

namespace Modules\Category\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class CategoryDatabaseSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $categories = [
            [
                'name' => 'Blood Test',
                'slug' => 'blood-test',
                'file_url' => public_path('/dummy-images/test_category/blood_test.png'),
                'description' => 'Comprehensive blood analysis to check overall health and detect various conditions like anemia, infections, and more. 🩸',
                'status' => 1,
            ],
            [
                'name' => 'Urine Test',
                'slug' => 'urine-test',
                'file_url' => public_path('/dummy-images/test_category/urine_test.png'),
                'description' => 'Detects infections, kidney issues, and metabolic conditions through a detailed urine analysis. 🚰',
                'status' => 1,
            ],
            [
                'name' => 'Cholesterol Test',
                'slug' => 'cholesterol-test',
                'file_url' => public_path('/dummy-images/test_category/cholesterol_test.png'),
                'description' => 'Evaluates cholesterol levels to assess heart health and risk of cardiovascular diseases. 🥗',
                'status' => 1,
            ],
            [
                'name' => 'Liver Test',
                'slug' => 'liver-test',
                'file_url' => public_path('/dummy-images/test_category/liver_function_test.png'),
                'description' => 'Examines liver enzymes and proteins to ensure proper liver functionality. 🏥',
                'status' => 1,
            ],
            [
                'name' => 'Thyroid Test',
                'slug' => 'thyroid-test',
                'file_url' => public_path('/dummy-images/test_category/thyroid_test.png'),
                'description' => 'Checks for thyroid hormone levels to diagnose hyperthyroidism or hypothyroidism. 🦋',
                'status' => 1,
            ],
            [
                'name' => 'Vitamin Deficiency Test',
                'slug' => 'vitamin-deficiency-test',
                'file_url' => public_path('/dummy-images/test_category/vitamin_deficiency_test.png'),
                'description' => 'Identifies deficiencies in essential vitamins like Vitamin D, B12, and more. 🌞',
                'status' => 1,
            ],
            [
                'name' => 'Diabetes Test',
                'slug' => 'diabetes-test',
                'file_url' => public_path('/dummy-images/test_category/diabetes_test.png'),
                'description' => 'Monitors blood sugar levels to diagnose and manage diabetes effectively. 🍭',
                'status' => 1,
            ],
            [
                'name' => 'Allergy Test',
                'slug' => 'allergy-test',
                'file_url' => public_path('/dummy-images/test_category/allergy_test.png'),
                'description' => 'Identifies allergens causing reactions such as pollen, food, or dust mites. 🌾',
                'status' => 1,
            ],
            [
                'name' => 'Kidney Function Test',
                'slug' => 'kidney-function-test',
                'file_url' => public_path('/dummy-images/test_category/kidney_function_test.png'),
                'description' => 'Evaluates kidney performance by analyzing waste levels in blood and urine. 💧',
                'status' => 1,
            ],
            [
                'name' => 'Fertility Testing',
                'slug' => 'fertility-testing',
                'file_url' => public_path('/dummy-images/test_category/fertility_testing.png'),
                'description' => 'Tests to assess fertility levels, including sperm count for men and ovulation monitoring for women, which can be conducted with sample collection at home. 🤰',
                'status' => 1,
            ],
            [
                'name' => 'Hepatitis Test',
                'slug' => 'hepatitis-test',
                'file_url' => public_path('/dummy-images/test_category/hepatitis_test.png'),
                'description' => 'Detects hepatitis infections (A, B, or C) to assess liver health. 🦠',
                'status' => 1,
            ],
            [
                'name' => 'Prostate Test',
                'slug' => 'prostate-test',
                'file_url' => public_path('/dummy-images/test_category/prostate_test.png'),
                'description' => 'Tests for prostate-specific antigen (PSA) levels to assess prostate health. 🧑‍⚕️',
                'status' => 1,
            ],
            [
                'name' => 'Cancer Screening Test',
                'slug' => 'cancer-screening-test',
                'file_url' => public_path('/dummy-images/test_category/cancer_screening_test.png'),
                'description' => 'Detects early signs of various cancers, such as breast, lung, or colon cancer. 🎗️',
                'status' => 1,
            ]
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($categories as $categoryData) {
                $caetgory = Category::create($categoryData);
                if (isset($categoryData['file_url'])) {
                    $this->attachFeatureImage($caetgory, $categoryData['file_url']);
                }
            }
            
            Schema::enableForeignKeyConstraints();
        }
    }
    private function attachFeatureImage($model, $publicPath)
    {

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('category_image');

        return $media;

    }
}
