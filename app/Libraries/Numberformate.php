<?php 
namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Numberformate{
    public function formatInIndianCurrency($amount) {
        // Round the amount to 2 decimal places
        $amount = round($amount, 2);
        
        // Split the amount into integer and decimal parts
        $integerPart = floor($amount);
        $decimalPart = round($amount - $integerPart, 2);
    
        // Format the integer part in Indian numbering system
        $formattedInteger = $this->formatIndianInteger($integerPart);
    
        // Format the decimal part (ensure it has exactly two digits)
        $formattedDecimal = str_pad($decimalPart * 100, 2, '0', STR_PAD_LEFT);
    
        // Combine integer and decimal parts
        return $formattedInteger . '.' . $formattedDecimal;
    }
    
    private function formatIndianInteger($integerPart) {
        // Convert the integer part to a string
        $integerPart = (string) $integerPart;
    
        // Apply Indian numbering system formatting
        $length = strlen($integerPart);
        if ($length > 3) {
            $lastThree = substr($integerPart, -3);
            $remaining = substr($integerPart, 0, -3);
            $remainingFormatted = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            return $remainingFormatted . ',' . $lastThree;
        }
    
        return $integerPart;
    }
    

}