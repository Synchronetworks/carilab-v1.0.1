<?php

namespace Modules\Subscriptions\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Subscriptions\Models\Subscription;

class SubscriptionsTransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('subscriptions_transactions')->delete();
        
        \DB::table('subscriptions_transactions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'subscriptions_id' => 1,
                'user_id' => 4,
                'amount' => 5.0,
                'payment_type' => 'stripe',
                'payment_status' => 'paid',
                'transaction_id' => 'pi_1R4JBYFTMa5P8ht0pZX0B3l3',
                'tax_data' => '[{"id":1,"title":"Service Tax","type":"Percentage","value":5,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":2,"title":"Home Collection Fee","type":"Fixed","value":10,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":3,"title":"State Health Tax","type":"Percentage","value":2,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]}]',
                'other_transactions_details' => NULL,
                'created_by' => 4,
                'updated_by' => 4,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:15:19',
                'updated_at' => '2025-03-19 15:15:19',
            ),
            1 => 
            array (
                'id' => 2,
                'subscriptions_id' => 2,
                'user_id' => 8,
                'amount' => 5.0,
                'payment_type' => 'stripe',
                'payment_status' => 'paid',
                'transaction_id' => 'pi_1R4JCqFTMa5P8ht0xkiHzc00',
                'tax_data' => '[{"id":1,"title":"Service Tax","type":"Percentage","value":5,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":2,"title":"Home Collection Fee","type":"Fixed","value":10,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":3,"title":"State Health Tax","type":"Percentage","value":2,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]}]',
                'other_transactions_details' => NULL,
                'created_by' => 8,
                'updated_by' => 8,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:16:39',
                'updated_at' => '2025-03-19 15:16:39',
            ),
            2 => 
            array (
                'id' => 3,
                'subscriptions_id' => 3,
                'user_id' => 6,
                'amount' => 5.0,
                'payment_type' => 'stripe',
                'payment_status' => 'paid',
                'transaction_id' => 'pi_1R4JDuFTMa5P8ht0Licn3byf',
                'tax_data' => '[{"id":1,"title":"Service Tax","type":"Percentage","value":5,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":2,"title":"Home Collection Fee","type":"Fixed","value":10,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},{"id":3,"title":"State Health Tax","type":"Percentage","value":2,"status":1,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:42:34.000000Z","updated_at":"2025-03-19T09:42:34.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]}]',
                'other_transactions_details' => NULL,
                'created_by' => 6,
                'updated_by' => 6,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:17:46',
                'updated_at' => '2025-03-19 15:17:46',
            ),
        ));
        
    }
}