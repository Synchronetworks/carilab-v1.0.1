<?php

namespace Modules\Collector\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Collector\Models\CollectorDocument;
use Modules\Document\Models\Document;

class CollectorDocumentSeeder extends Seeder
{
    public function run()
    {
        $collectorDocuments = [
            ['collector_id' => 24, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/felix_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 24, 'document_id' => 'educational-certificates', 'image' => public_path('dummy-images/collectordocument/felix_doc_2.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 24, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/felix_doc_3.png'), 'is_verified' => 0, 'status' => 1],
            ['collector_id' => 25, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/jorge_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 26, 'document_id' => 'educational-certificates', 'image' => public_path('dummy-images/collectordocument/erica_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 27, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/parsa_doc_1.png'), 'is_verified' => 0, 'status' => 1],
            ['collector_id' => 28, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/daniel_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 29, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/harvey_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 30, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/angela_doc_1.png'), 'is_verified' => 0, 'status' => 1],
            ['collector_id' => 31, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/amy_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 32, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/miles_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 33, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/karen_doc_1.png'), 'is_verified' => 0, 'status' => 1],
            ['collector_id' => 34, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/glen_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 35, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/jessica_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 36, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/julian_doc_1.png'), 'is_verified' => 0, 'status' => 1],
            ['collector_id' => 37, 'document_id' => 'government-issued-id-proof-passport-drivers-license-etc', 'image' => public_path('dummy-images/collectordocument/joey_doc_1.png'), 'is_verified' => 1, 'status' => 1],
            ['collector_id' => 38, 'document_id' => 'signed-contractagreement', 'image' => public_path('dummy-images/collectordocument/hanna_doc_1.png'), 'is_verified' => 1, 'status' => 1]
        
        ];

        foreach ($collectorDocuments as $data) {
       
            $document = Document::where('slug', $data['document_id'])->first();
            if (!$document) {
                continue; 
            }

            $data['document_id'] = $document->id;
            
            $collectorDocumentData = Arr::except($data, ['image']);

            $collectorDocument = CollectorDocument::create($collectorDocumentData);

            if (!empty($data['image']) && file_exists($data['image'])) {
                $this->attachFeatureImage($collectorDocument, $data['image']);
            }
        }
    }

    private function attachFeatureImage($model, $publicPath)
    {
        $file = new \Illuminate\Http\File($publicPath);
        return $model->addMedia($file)->preservingOriginal()->toMediaCollection('collector_document');
    }
}