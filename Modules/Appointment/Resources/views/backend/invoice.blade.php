<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
        }

        .column {
            float: left;
            width: 30%;
            padding: 0 10px;
        }

        .row {
            margin: 0 -5px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .card {
            padding: 16px;
            text-align: center;
            background-color: #F6F7F9;
        }
        table tr td{
            font-size: 14px;
        }
        table thead th{
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div style="padding: 24px 0 0;">
        <div style="padding-bottom: 16px; margin-bottom: 16px; border-bottom:  1px solid #ccc;">
            <div style="overflow: hidden;">
                <div style="float:left; display: inline-block;">
                    <img src="https://apps.iqonic.design/kivilabs/img/logo/logo.png" height="30" width="30">
                </div>
                <div style="float:right; text-align:right;">
                    <span style="color: #828A90;">{{ __('messages.invoice_date') }}:</span><span style="color: #3F414D; padding-right: 60px;">
                        {{ App\Models\Setting::formatDate($data->appointment_date) ?? \Carbon\Carbon::parse($data->appointment_date)->format('Y-m-d') }}</span>
                    <span style="color: #828A90;">  {{ __('messages.invoice_id') }}-</span><span style="color: #5670CC;"> {{ '#' . $data->id ?? '-'}}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div>
            <p style="color: #828A90; margin-bottom: 16px;">{{__('messages.msg_thanks_payment')}}</p>
        </div>
        <div style="margin-bottom: 16px;">
            <div style="overflow: hidden;">
                <div style="float: left; width: 75%; display: inline-block;">
                    <h5 style="color: #3F414D; margin: 0;">{{__('messages.organization_information:')}}</h5>
                    <p style="color: #828A90;  margin-top: 12px; margin-bottom: 0;">{{__('messages.question_support_regarding_service')}}</p>
                </div>
                <div style="float:left; width: 25%; text-align:right;">
                    <span style="color: #3F414D; margin-bottom: 12px;">{{ App\Models\Setting::getSettings('inquriy_email') ?? '-'}}</span>
                    <p style="color: #3F414D;  margin-top: 12px; margin-bottom: 0;">{{ App\Models\Setting::getSettings('helpline_number') ?? '-'}}</p>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        {{-- PAYMENT INFORMATION --}}
        <div>
            <h5 style="color: #3F414D; margin-top: 0;">{{__('messages.payment_info')}} :</h5>
            <div style="background: #F6F7F9; padding:8px 24px;">
                <div style="display: inline-block;">
                    <span style="color: #3F414D;">{{__('messages.lbl_payment_method')}}::</span>
                    <span style="color: #828A90; margin-left: 16px;">{{ isset(optional($data->transactions)->payment_type) ? ucfirst(optional($data->transactions)->payment_type) : '-' }}</span>
                </div>
                <div style="display: inline-block; padding-left: 24px;">
                    <span style="color: #3F414D;">{{ __('messages.lbl_payment_status') }} ::</span>
                        @if(isset($data->transactions) && optional($data->transactions)->payment_status)
                            <span style="color: #219653; margin-left: 16px;" >
                                {{ str_replace('_', ' ', ucfirst(optional($data->transactions)->payment_status)) }}
                            </span>
                        @else
                            <span style="color: #FB2F2F; margin-left: 16px;">
                                {{ __('messages.pending') }}
                            </span>   
                        @endif
                </div>
            </div>
        </div>

        {{-- PERSON INFORMATION --}}

        <div style="padding: 16px 0;">
            <div class="row">
                @if($data->customer)

                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.lbl_customer')}}:</h5>
                    <div class="card" style="text-align: start;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($data->customer)->full_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($data->customer)->mobile ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D;">{{ __('messages.email') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($data->customer)->email ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if ($data->vendor_id)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.lbl_vendor')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($data->vendor)->full_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($data->vendor)->mobile ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D;">{{ __('messages.email') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($data->vendor)->email ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if($data->appointmentCollectorMapping && $data->appointmentCollectorMapping->collector)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.collector')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D; width:50%;">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional(optional($data->appointmentCollectorMapping)->collector)->full_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D; width:50%;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional(optional($data->appointmentCollectorMapping)->collector)->mobile ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #3F414D; width:50%;">{{ __('messages.email') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional(optional($data->appointmentCollectorMapping)->collector)->email ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- TABLE  --}}
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
           
            <thead style="background: #F6F7F9;">
                <th style="padding:12px 30px; text-align: center;">{{__('messages.service')}}</th>
                <th style="padding:12px 30px; text-align: center;">{{__('messages.lbl_price')}}</th>
                <th style="padding:12px 30px; text-align: center;">{{__('messages.lbl_discount')}}</th>
                <th style="padding:12px 30px; text-align: right;">{{__('messages.lbl_amount')}}</th>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"> {{$data->test_type == 'test_case' ? optional($data->catlog)->name :  optional($data->package)->name}}</td>
                    <td style="padding:12px 30px; text-align: start;">{{ isset($data->amount) ? Currency::format($data->amount) : 0 }}</td>
                    <td style="padding:12px 30px; text-align: start;">{{!empty($data->transaction->discount_value) ?  optional($data->transactions)->discount_type == 'percentage' 
                                ? optional($data->transactions)->discount_value . '%' 
                                : Currency::format(optional($data->transactions)->discount_value ?? 0) : 0}}</td>
                    <td style="padding:12px 30px; text-align: right;">{{!empty($data->test_discount_amount) ? $data->test_discount_amount : 0}}</td>
                </tr>
            </tbody>
        </table>

        {{-- BILLING TABLE --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
            <tbody style="background: #F6F7F9;">

                {{-- PRICE --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #828A90;">{{__('messages.lbl_amount')}}</td>
                    <td style="padding:12px 30px; text-align: end; color: #3F414D;">
                        {{ Currency::format($data->amount) ?? 0}} 
                    </td>
                </tr>
               
                {{-- DISCOUNT --}}
                @if(optional($data->transactions)->discount_amount > 0)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #828A90;">{{ __('messages.lbl_discount') }}
                            ({{ optional($data->transactions)->discount_type == 'percentage' 
                                ? optional($data->transactions)->discount_value . '%' 
                                : Currency::format(optional($data->transactions)->discount_value ?? 0) }})</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ \Currency::format(optional($data->transactions)->discount_amount) }}</td>
                </tr>
                @endif

                 {{-- COUPON --}}
                @if(optional($data->transactions)->coupon_id != null)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #828A90;">{{__('messages.coupons')}} ({{optional($data->transactions)->coupon_id}})</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ Currency::format(optional($data->transactions)->coupon_amount) }}</td>
                </tr>
                @endif
                
                 <!-- Extra Charges -->

                 
                {{--  Sub-Total --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #828A90;">{{ __('messages.sub_total') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #3F414D;">{{!empty($data->test_discount_amount) ? Currency::format($data->test_discount_amount) : 0}}</td>
                </tr>

                {{-- TAX  --}}
                @if(optional($data->transactions)->tax != "")
                    <tr>
                        <td style="padding:12px 30px; text-align: start;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: start; color: #828A90;">{{__('messages.tax')}} <br>
                                @foreach(json_decode(optional($data->transactions)->tax) as $key => $value)
                                    @if($value->type == 'percentage')
                                        <span>({{ $value->title }} {{ $value->value }}%)</span>
                                    @else
                                        <span>({{ $value->title }} {{ Currency::format($value->value) }})</span>
                                    @endif
                                @endforeach
                        </td>
                        <td style="padding:12px 30px; text-align: end; color: #FB2F2F;">{{!empty(optional($data->transactions)->total_tax_amount) ? Currency::format(optional($data->transactions)->total_tax_amount) : 0}}</td>
                    </tr>
                @endif

                {{-- GRAND TOTAL --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #3F414D; border-top:1px solid #ccc;">{{__('messages.grand_total')}}</td>
                    <td style="padding:12px 30px; text-align: end; color: #3F414D; border-top:1px solid #ccc;">{{ Currency::format($data->total_amount) ?? 0 }}</td>
                </tr>

                
             
            </tbody>
        </table>
       
        <footer style="margin-top: 24px;">
            <div style="display: inline; vertical-align: middle; margin-right: 10px;">
                <h5 style="display: inline;">{{__('messages.more_information')}}</h5>
                <a href="javascript:void(0)" style="color: #5F60B9;">{{ App\Models\Setting::getSettings('app_name') ?? '-'}}</a>
                <h5 style="display: block; margin: 8px 0 0;">{{__('messages.all_rights_reserved')}}</h5>
            </div>
        </footer>
    </div>
</body>

</html>