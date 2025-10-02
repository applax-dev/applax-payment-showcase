<?php

namespace App\Helpers;

class GdprHelper
{
    /**
     * Mask email address for GDPR compliance
     * Example: john.doe@example.com -> jo***@ex***
     */
    public static function maskEmail(string $email): string
    {
        if (empty($email)) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        [$username, $domain] = $parts;

        // Mask username (show first 2 chars, rest as ***)
        $maskedUsername = strlen($username) <= 2
            ? str_repeat('*', strlen($username))
            : substr($username, 0, 2) . str_repeat('*', max(3, strlen($username) - 2));

        // Mask domain (show first 2 chars of domain name, rest as ***)
        $domainParts = explode('.', $domain);
        $maskedDomainName = strlen($domainParts[0]) <= 2
            ? str_repeat('*', strlen($domainParts[0]))
            : substr($domainParts[0], 0, 2) . str_repeat('*', max(3, strlen($domainParts[0]) - 2));

        // Keep the TLD visible
        $tld = count($domainParts) > 1 ? '.' . end($domainParts) : '';

        return $maskedUsername . '@' . $maskedDomainName . $tld;
    }

    /**
     * Mask phone number for GDPR compliance
     * Example: +1234567890 -> +12***90
     */
    public static function maskPhone(string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-digit characters except +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        if (strlen($cleanPhone) <= 4) {
            return str_repeat('*', strlen($cleanPhone));
        }

        // Show first 3 characters and last 2, mask the rest
        $start = substr($cleanPhone, 0, 3);
        $end = substr($cleanPhone, -2);
        $middle = str_repeat('*', max(3, strlen($cleanPhone) - 5));

        return $start . $middle . $end;
    }

    /**
     * Mask name for GDPR compliance (first name visible, last name first letter + ***)
     * Example: John Doe -> John D***
     */
    public static function maskName(string $firstName, string $lastName): string
    {
        $maskedLastName = empty($lastName) ? '' : substr($lastName, 0, 1) . str_repeat('*', max(3, strlen($lastName) - 1));
        return trim($firstName . ' ' . $maskedLastName);
    }

    /**
     * Check if current user should see unmasked data (admin override)
     * For now, always mask in demo - in real app you'd check user permissions
     */
    public static function shouldMaskData(): bool
    {
        // In a real application, you might check:
        // return !auth()->user()?->hasPermission('view_personal_data');

        // For demo purposes, always mask
        return true;
    }
}