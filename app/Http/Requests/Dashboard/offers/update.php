<?php
namespace App\Http\Requests\Dashboard\offers;

use App\Models\{Offer,Property};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'discount_retail' => ['nullable', 'numeric', 'min:0'],
            'discount_wholesale' => ['nullable', 'numeric', 'min:0'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $propertyId = $this->input('property_id');
            $newStart = $this->input('start');
            $newEnd = $this->input('end');
            
            // Exclude current offer from overlap checks
            // Handling both Model binding and raw ID string
            $offer = $this->route('offer') ?: $this->route('id');
            $offerId = is_object($offer) ? $offer->id : $offer;

            if ($propertyId && $newStart && $newEnd) {
                // 1. Check overlap excluding current offer
                $conflictingOffer = Offer::where('Property_id', $propertyId)
                    ->when($offerId, function($q) use ($offerId) {
                        return $q->where('id', '!=', $offerId);
                    })
                    ->where(function ($query) use ($newStart, $newEnd) {
                        $query->where('start', '<', $newEnd)
                              ->where('end', '>', $newStart);
                    })
                    ->exists();

                if ($conflictingOffer) {
                    $validator->errors()->add('start', __('Conflicting offer exists for this property in the selected time range.'));
                    $validator->errors()->add('end', __('Conflicting offer exists for this property in the selected time range.'));
                }

                // Get product type
                $property = Property::with('product')->find($propertyId);
                $type = $property->product->type ?? 'retail';

                // Check type-specific discount
                if ($type === 'retail' && empty($this->input('discount_retail'))) {
                    $validator->errors()->add('discount_retail', __('Retail discount is required for retail products.'));
                }
                if ($type === 'wholesale' && empty($this->input('discount_wholesale'))) {
                    $validator->errors()->add('discount_wholesale', __('Wholesale discount is required for wholesale products.'));
                }

                // 2. Same hour check excluding current offer
                $newStartDate = Carbon::parse($newStart);
                $startHour = $newStartDate->format('Y-m-d H');
                $sameHourOffer = Offer::where('Property_id', $propertyId)
                    ->when($offerId, function($q) use ($offerId) {
                        return $q->where('id', '!=', $offerId);
                    })
                    ->whereRaw("DATE_FORMAT(start, '%Y-%m-%d %H') = ?", [$startHour])
                    ->exists();

                if ($sameHourOffer) {
                    $validator->errors()->add('start', __('Cannot start an offer in the same hour as another offer for this property.'));
                }
            }

        });
    }

    public function messages(): array
    {
        return [
            'property_id.required' => __('Please select a product variant.'),
            'property_id.exists' => __('Selected product variant does not exist.'),
            'discount_retail.numeric' => __('Retail discount must be a number.'),
            'discount_retail.min' => __('Retail discount must be at least 0.'),
            'discount_wholesale.numeric' => __('Wholesale discount must be a number.'),
            'discount_wholesale.min' => __('Wholesale discount must be at least 0.'),
            'start.required' => __('Start date is required.'),
            'start.date' => __('Start date must be a valid date.'),
            'end.required' => __('End date is required.'),
            'end.date' => __('End date must be a valid date.'),
            'end.after_or_equal' => __('End date must be equal or after start date.'),
        ];
    }
}