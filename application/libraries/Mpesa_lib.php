<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M-Pesa Payment Library for CodeIgniter
 * 
 * This library handles M-Pesa mobile money payments for Mozambique
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Commerce
 * @author      Your Name
 */

class Mpesa_lib {
    
    private $CI;
    private $apiKey;
    private $publicKey;
    private $serviceProviderCode;
    private $baseUrl;
    private $isLive;
    
    public function __construct($config = array()) {
        $this->CI =& get_instance();
        $this->CI->load->database();
        
        // Set default configuration
        $this->isLive = false;
        $this->baseUrl = "https://api.sandbox.vm.co.mz:18352/ipg/v1x/c2bPayment/singleStage/";
        
        // Load configuration if provided
        if (!empty($config)) {
            $this->initialize($config);
        }
    }
    
    /**
     * Initialize M-Pesa configuration
     */
    public function initialize($config) {
        if (isset($config['api_key'])) {
            $this->apiKey = $config['api_key'];
        }
        if (isset($config['public_key'])) {
            $this->publicKey = $config['public_key'];
        }
        if (isset($config['service_provider_code'])) {
            $this->serviceProviderCode = $config['service_provider_code'];
        }
        if (isset($config['is_live'])) {
            $this->isLive = $config['is_live'];
            if ($this->isLive) {
                $this->baseUrl = "https://api.vm.co.mz/ipg/v1x/c2bPayment/singleStage/";
            }
        }
    }
    
    /**
     * Generate Bearer Token for authentication
     */
    private function generateBearerToken() {
        // Clean the public key - remove any existing headers/footers and whitespace
        $cleanKey = preg_replace('/\s+/', '', $this->publicKey);
        $cleanKey = str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----'], '', $cleanKey);
        
        // Format the public key properly
        $publicKeyFormatted = "-----BEGIN PUBLIC KEY-----\n" . 
                              wordwrap($cleanKey, 64, "\n", true) . 
                              "\n-----END PUBLIC KEY-----";
        
        // Debug: Log the formatted key (remove in production)
        log_message('debug', 'M-Pesa Public Key Length: ' . strlen($cleanKey));
        log_message('debug', 'M-Pesa API Key: ' . substr($this->apiKey, 0, 10) . '...');
        
        // Try to encrypt
        $encrypted = '';
        $result = openssl_public_encrypt($this->apiKey, $encrypted, $publicKeyFormatted, OPENSSL_PKCS1_PADDING);
        
        if (!$result) {
            $error = openssl_error_string();
            log_message('error', 'M-Pesa RSA Encryption Error: ' . $error);
            throw new Exception('Failed to encrypt API key: ' . $error);
        }
        
        return base64_encode($encrypted);
    }
    
    /**
     * Process M-Pesa payment
     */
    public function processPayment($orderData) {
        try {
            // Generate unique references
            $transactionReference = 'T' . time() . rand(1000, 9999);
            $thirdPartyReference = strtoupper(substr(md5(uniqid()), 0, 6));
            
            // Prepare request data
            $requestData = array(
                "input_TransactionReference" => $transactionReference,
                "input_CustomerMSISDN" => $orderData['phone'],
                "input_Amount" => $orderData['amount'],
                "input_ThirdPartyReference" => $thirdPartyReference,
                "input_ServiceProviderCode" => $this->serviceProviderCode
            );
            
            // Generate bearer token
            $bearerToken = $this->generateBearerToken();
            
            // Send request to M-Pesa API
            $response = $this->sendRequest($requestData, $bearerToken);
            
            // Log transaction to database
            $logData = array(
                'order_id' => $orderData['order_id'],
                'customer_id' => $orderData['customer_id'],
                'phone_number' => $orderData['phone'],
                'amount' => $orderData['amount'],
                'user_reference' => $orderData['user_reference'] ?? null,
                'transaction_reference' => $transactionReference,
                'third_party_reference' => $thirdPartyReference,
                'mpesa_response_code' => $response['response_code'] ?? null,
                'mpesa_response_desc' => $response['response_desc'] ?? null,
                'mpesa_conversation_id' => $response['conversation_id'] ?? null,
                'mpesa_transaction_id' => $response['transaction_id'] ?? null,
                'status' => $response['success'] ? 'SUCCESS' : 'FAILED',
                'raw_response' => json_encode($response['raw_data']),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->CI->db->insert('mpesa_transactions', $logData);
            
            return array(
                'success' => $response['success'],
                'message' => $response['message'],
                'transaction_id' => $transactionReference,
                'mpesa_reference' => $thirdPartyReference,
                'data' => $response['raw_data']
            );
            
        } catch (Exception $e) {
            // Log error
            log_message('error', 'M-Pesa Payment Error: ' . $e->getMessage());
            
            return array(
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'transaction_id' => null,
                'mpesa_reference' => null,
                'data' => null
            );
        }
    }
    
    /**
     * Send HTTP request to M-Pesa API
     */
    private function sendRequest($requestData, $bearerToken) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->isLive);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $bearerToken,
            "Origin: developer.mpesa.vm.co.mz"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception('CURL Error: ' . $curlError);
        }
        
        $responseData = json_decode($response, true);
        
        return array(
            'success' => ($httpCode == 200 || $httpCode == 201),
            'message' => $responseData['output_ResponseDesc'] ?? 'Unknown response',
            'response_code' => $responseData['output_ResponseCode'] ?? $httpCode,
            'response_desc' => $responseData['output_ResponseDesc'] ?? 'No description',
            'conversation_id' => $responseData['output_ConversationID'] ?? null,
            'transaction_id' => $responseData['output_TransactionID'] ?? null,
            'raw_data' => $responseData,
            'http_code' => $httpCode
        );
    }
    
    /**
     * Check transaction status
     */
    public function checkTransactionStatus($transactionReference) {
        $query = $this->CI->db->select('*')
                           ->from('mpesa_transactions')
                           ->where('transaction_reference', $transactionReference)
                           ->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }
    
    /**
     * Update transaction status
     */
    public function updateTransactionStatus($transactionReference, $status, $additionalData = array()) {
        $updateData = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        if (!empty($additionalData)) {
            $updateData = array_merge($updateData, $additionalData);
        }
        
        return $this->CI->db->where('transaction_reference', $transactionReference)
                          ->update('mpesa_transactions', $updateData);
    }
    
    /**
     * Validate phone number format
     */
    public function validatePhoneNumber($phone) {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Mozambique phone number validation
        // Format: 258 + 9 digits (total 12 digits)
        // Mobile numbers in Mozambique: 258 + (82/83/84/85/86/87) + 7 digits
        
        if (strlen($phone) == 12 && substr($phone, 0, 3) == '258') {
            // Check if it's a valid Mozambique mobile number
            $operator_code = substr($phone, 3, 2);
            if (in_array($operator_code, ['82', '83', '84', '85', '86', '87'])) {
                return $phone;
            }
        } elseif (strlen($phone) == 9 && in_array(substr($phone, 0, 2), ['82', '83', '84', '85', '86', '87'])) {
            // If 9 digits starting with operator code, add country code
            return '258' . $phone;
        }
        
        return false;
    }
}