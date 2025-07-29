<?php

namespace Modules\Lab\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Lab\Models\Lab;
use Modules\Lab\Models\LabSession;
use Illuminate\Support\Arr;
class LabDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $labs = [
            [
                'name' => 'Spectrum Health Diagnostics',
                'slug' => 'spectrum-health-diagnostics',
                'lab_code' => 'SHDL004',
                'description' => 'Offering a wide range of reliable diagnostic services, ensuring precise results with expert analysis and advanced technology. 🏥🔬',
                'vendor_id' => multivendor() == 1 ? 4 : 1,
                'phone_number' => '1-78948793',
                'email' => 'diagnostics@spectrum.com',
                'address_line_1' => '101 Spectrum Plaza, New York, NY',
                'address_line_2' => 'Room 210',
                'city_id' => 1,
                'state_id' => 1,
                'country_id' => 1,
                'postal_code' => '10001',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-LAB-004326',
                'license_expiry_date' => null,
                'accreditation_type' => 'ISO',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '987-654321',
                'payment_modes' => ['manual', 'online'],
                'payment_gateways' => [],
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/spectrum_health_diagnostics.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            [
                'name' => 'Advanced Diagnostics Lab',
                'slug' => 'advanced-diagnostics-lab',
                'lab_code' => 'ADDL001',
                'description' => 'Specializing in accurate and timely diagnostic tests, providing reliable results for all your healthcare needs. 🧪📊',
                'vendor_id' => multivendor() == 1 ? 4 : 1,
                'phone_number' => '1-21548790',
                'email' => 'info@diagnostics.com',
                'address_line_1' => '123 Innovation Drive, Los Angeles, CA',
                'address_line_2' => 'Suite 400',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '90001',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-LAB-001234',
                'license_expiry_date' => null,
                'accreditation_type' => 'NABL',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '873-521430',
                'payment_modes' => ['online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/advanced_diagnostics_lab.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            [
                'name' => 'Precision Medical Lab',
                'slug' => 'precision-medical-lab',
                'lab_code' => 'PRML002',
                'description' => 'Offering comprehensive testing services, including routine blood work and advanced biomarker testing. 🩸',
                'vendor_id' => 4,
                'phone_number' => '1-21578791',
                'email' => 'support@medlab.com',
                'address_line_1' => '456 Wellness Avenue, Chicago, IL',
                'address_line_2' => 'Building B',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '60601',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-LAB-002567',
                'license_expiry_date' => null,
                'accreditation_type' => 'ISO',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '715-421309',
                'payment_modes' => ['manual'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/precision_medical_lab.png'),
                'taxes' =>'service-tax, state-health-tax',
            ],
            [
                'name' => 'Horizon Clinical Lab',
                'slug' => 'horizon-clinical-lab',
                'lab_code' => 'HOCL003',
                'description' => 'Known for accurate and timely clinical test results across all major diagnostic fields. ⏱️',
                'vendor_id' => 5,
                'phone_number' => '1-31548792',
                'email' => 'contact@horizonlab.com',
                'address_line_1' => '789 Health Street, Houston, TX',
                'address_line_2' => 'Floor 2',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '77001',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-LAB-003891',
                'license_expiry_date' => null,
                'accreditation_type' => 'ISO',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '604-329108',
                'payment_modes' => ['manual', 'online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/horizon_clinical_lab.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            [
                'name' => 'Apex Pathology Lab',
                'slug' => 'apex-pathology-lab',
                'lab_code' => 'APXL005',
                'description' => 'A trusted name in pathology services, offering reliable home sample collection and accurate testing. 🏡',
                'vendor_id' => 6,
                'phone_number' => '1-21896794',
                'email' => 'admin@apexpathlab.com',
                'address_line_1' => '202 Apex Lane, San Francisco, CA',
                'address_line_2' => 'Suite 105',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '94101',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-LAB-005789',
                'license_expiry_date' => null,
                'accreditation_type' => 'NABL',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '513-291087',
                'payment_modes' => ['manual', 'online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/apex_pathology_lab.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            
            [
                'name' => 'PrimeCare Diagnostics',
                'slug' => 'primecare-diagnostics',
                'lab_code' => 'PRCD006',
                'description' => 'Delivering state-of-the-art laboratory services with efficiency and accuracy for better healthcare outcomes. ✅',
                'vendor_id' => 6,
                'phone_number' => '1-21487795',
                'email' => 'contact@carediagnostics.com',
                'address_line_1' => '890 Willow Lane, Beverly Hills, CA',
                'address_line_2' => 'Suite 25A',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '10001',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-PCD-67891',
                'license_expiry_date' => null,
                'accreditation_type' => 'ISO',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '442-198076',
                'payment_modes' => ['manual', 'online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/primecare_diagnostics.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            [
                'name' => 'Infinity Diagnostics Center',
                'slug' => 'infinity-diagnostics-center',
                'lab_code' => 'INDC004',
                'description' => 'Comprehensive lab testing services to ensure accurate and timely results for all medical needs. 📋',
                'vendor_id' => 7,
                'phone_number' => '1-21896512',
                'email' => 'info@infinity.com',
                'address_line_1' => '456 Elm Street, New York, NY',
                'address_line_2' => 'Building C, Floor 3',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '10010',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-IDC-90234',
                'license_expiry_date' => null,
                'accreditation_type' => 'NABL',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '371-096587',
                'payment_modes' => ['online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/infinity_diagnostics_center.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            
            [
                'name' => 'Vital Path Labs',
                'slug' => 'vital-path-labs',
                'lab_code' => 'VIPL005',
                'description' => 'High-quality diagnostic services with a focus on patient-centric care and innovative testing. 💡',
                'vendor_id' => 9,
                'phone_number' => '1-87596797',
                'email' => 'support@pathlabs.com',
                'address_line_1' => '124 Lakeview Dr, Atlanta, GA',
                'address_line_2' => 'Suite 100',
                'city_id' => null,
                'state_id' => null,
                'country_id' => null,
                'postal_code' => '30301',
                'latitude' => null,
                'longitude' => null,
                'time_slot' => null,
                'license_number' => 'US-VPL-34567',
                'license_expiry_date' => null,
                'accreditation_type' => 'ISO',
                'accreditation_expiry_date' => null,
                'tax_identification_number' => '250-985476',
                'payment_modes' => ['manual', 'online'],
                'payment_gateways' => [],
                'status' => 1,
                'logo' => public_path('/dummy-images/lab/vital_path_labs.png'),
                'taxes' =>'service-tax, home-collection-fee, state-health-tax',
            ],
            
            
        ];
        foreach ($labs as $key => $val) {
            $logo = $val['logo'] ?? null;
            $labData = Arr::except($val, ['logo','taxes']);
            $lab = Lab::create($labData);
            if (isset($logo)) {
                $this->attachFeatureImage($lab, $logo);
            }
            $days = [
                ['day' => 'monday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'tuesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'wednesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'thursday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'friday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'saturday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'sunday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => true, 'breaks' => []],
            ];            
            foreach ($days as $key => $val) {

                $val['lab_id'] = $lab->id;
                LabSession::create($val);
            }
            if(!empty($labData['taxes']))
            {
                $taxes = explode(',', $labData['taxes']);
                foreach ($taxes as $tax) {
                    $taxId = trim($tax); // Assuming you have a way to get tax ID from tax name
                    LabTaxMapping::create([
                        'lab_id' => $lab->id,
                        'tax_id' => $taxId,
                    ]);
                }
            }
            if(!empty($labData['locations']))
            {
                foreach ($labData['locations'] as $locationId) {
                    LabLocationMapping::create([
                        'lab_id' => $lab->id,
                        'location_id' => $locationId,
                    ]);
                }
            }
        }
    }
    private function attachFeatureImage($model, $publicPath)
    {

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('logo');

        return $media;

    }
}
