<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Shop\Order;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'first_name',
        'last_name',
        'country',
        'city',
        'address',
        'zip_code',
        'gateway_client_id',
        'gateway_data',
        'last_order_at'
    ];

    protected $casts = [
        'gateway_data' => 'array',
        'last_order_at' => 'datetime'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->email;
    }

    public function hasOrders(): bool
    {
        return $this->orders()->count() > 0;
    }

    public function isSyncedWithGateway(): bool
    {
        return !empty($this->gateway_client_id);
    }

    public function updateLastOrderTime(): void
    {
        $this->update(['last_order_at' => now()]);
    }

    public function toGatewayFormat(): array
    {
        // Format phone number to gateway format: country_code-phone_number
        $phone = $this->formatPhoneForGateway($this->phone);

        $data = [
            'email' => $this->email,
            'phone' => $phone,
            'first_name' => $this->first_name ?: null,
            'last_name' => $this->last_name ?: null,
        ];

        // If customer is already synced with Gateway, include original_client to link to existing client
        // This bypasses SDK validation while still linking to the existing client (OpenCart approach)
        if ($this->gateway_client_id) {
            $data['original_client'] = $this->gateway_client_id;
        }

        // Only add optional fields if they have values
        if ($this->city) {
            $data['city'] = $this->city;
        }

        if ($this->address) {
            $data['address'] = $this->address;
        }

        if ($this->zip_code) {
            $data['zip_code'] = $this->zip_code;
        }

        // Remove null values to avoid sending empty fields
        return array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Format phone number for gateway API
     * Expects format: country_code-phone_number (e.g., "40-749089083")
     */
    private function formatPhoneForGateway($phone)
    {
        if (!$phone) {
            return null;
        }

        // If phone already starts with +, it's in international format
        if (str_starts_with($phone, '+')) {
            // Remove the + and split country code from number
            $phone = substr($phone, 1);

            // Common country codes and their lengths
            $countryCodes = [
                '1' => 1,    // US/Canada
                '40' => 2,   // Romania
                '44' => 2,   // UK
                '49' => 2,   // Germany
                '33' => 2,   // France
                '39' => 2,   // Italy
            ];

            foreach ($countryCodes as $code => $length) {
                if (str_starts_with($phone, $code)) {
                    $countryCode = substr($phone, 0, $length);
                    $nationalNumber = substr($phone, $length);
                    return $countryCode . '-' . $nationalNumber;
                }
            }

            // Default: assume first 2 digits are country code
            if (strlen($phone) > 2) {
                return substr($phone, 0, 2) . '-' . substr($phone, 2);
            }
        }

        // If phone doesn't start with +, assume it's a Romanian number
        $phone = str_replace(['+', '-', ' ', '(', ')'], '', $phone);
        if (str_starts_with($phone, '07')) {
            return '40-' . $phone;
        } else if (str_starts_with($phone, '40')) {
            return '40-' . substr($phone, 2);
        } else {
            return '40-' . $phone;
        }
    }
}