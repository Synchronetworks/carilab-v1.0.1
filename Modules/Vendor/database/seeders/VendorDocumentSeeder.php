<?php

namespace Modules\Vendor\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Document\Models\Document;
use Modules\Vendor\Models\Vendor;
use Modules\MenuBuilder\Models\MenuBuilder;
use Modules\Vendor\Models\VendorDocument;

class VendorDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendorDocuments = [
            [
                'vendor_id' => 4, 
                'document_id' => 'business-registration-certificate',
                'image' => public_path('dummy-images/vendordocument/liam_doc_1.png'),
                'is_verified' => 1, 
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vendor_id'   => 4, 
                'document_id' => 'tax-identification-number-tin',
                'image'       => public_path('dummy-images/vendordocument/liam_doc_2.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 4, 
                'document_id' => 'laboratory-accreditation-certificate',
                'image'       => public_path('dummy-images/vendordocument/liam_doc_3.png'),
                'is_verified'   => 0, // Disable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 4, 
                'document_id' => 'ownermanager-id-proof',
                'image'       => public_path('dummy-images/vendordocument/liam_doc_4.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 5, 
                'document_id' => 'business-registration-certificate',
                'image'       => public_path('dummy-images/vendordocument/susan_doc_1.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 6, 
                'document_id' => 'tax_identification_number_tin',
                'image'       => public_path('dummy-images/vendordocument/roberto_doc_1.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 7, 
                'document_id' => 'laboratory-accreditation-certificate',
                'image'       => public_path('dummy-images/vendordocument/richard_doc_1.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 8, 
                'document_id' => 'ownermanager-id-proof',
                'image'       => public_path('dummy-images/vendordocument/ken_doc_1.png'),
                'is_verified'   => 1, // Enable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'vendor_id'   => 9, 
                'document_id' => 'tax-identification-number-tin',
                'image'       => public_path('dummy-images/vendordocument/deborah_doc_1.png'),
                'is_verified'   => 0, // Disable
                'status'      => 1, // Active
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]
        ];

        foreach ($vendorDocuments as $data) {

            $document = Document::where('slug', $data['document_id'])->first();
            if (!$document) {
                continue; 
            }

            $data['document_id'] = $document->id;

            $vendorDocumentData = Arr::except($data, ['image']);
            
            $vendorDocument = VendorDocument::create($vendorDocumentData);

            // Attach Image (If Exists)
            if (!empty($data['image']) && file_exists($data['image'])) {
                $this->attachFeatureImage($vendorDocument, $data['image']);
            }
        }
    }
    private function attachFeatureImage($model, $publicPath)
    {
        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('vendor_document');

        return $media;

    }
}
