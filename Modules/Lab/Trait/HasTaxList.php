<?php

namespace Modules\Lab\Trait;

use Modules\Tax\Models\Tax;
use Illuminate\Http\Request;

trait HasTaxList
{
    public function getTaxListAttribute()
    {
        if($this->labTaxMapping !== null && $this->labTaxMapping->count() > 0) {
            // Return lab-specific taxes
            return $this->labTaxMapping->map(function($taxMapping) {
                if($taxMapping->tax) {
                    return $this->formatTax($taxMapping->tax);
                }
            })->filter()->values();
        } else {
            if(multivendor() == 1) {
                // Return vendor taxes
                $vendorTaxes = optional($this->vendor)->userTaxMapping;

                if ($vendorTaxes && $vendorTaxes->count() > 0) {
                    return collect($vendorTaxes)
                        ->map(fn($taxMapping) => $taxMapping->tax ? $this->formatTax($taxMapping->tax) : null)
                        ->filter()
                        ->values();
                } else {
                    return Tax::where('status', 1)
                        ->get()
                        ->map(fn($tax) => $this->formatTax($tax));
                }
                
            } else {
                // Return all active taxes
                return Tax::where('status', 1)
                    ->get()
                    ->map(function($tax) {
                         $this->formatTax($tax);
                    });
            }
        }
    }

    protected function formatTax($tax)
    {
        return [
            'id' => $tax->id,
            'title' => $tax->title,
            'value' => $tax->value,
            'type' => $tax->type,
            'status' => $tax->status
        ];
    }
}