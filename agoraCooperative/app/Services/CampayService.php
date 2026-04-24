<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CampayService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.campay.host', 'https://demo.campay.net/api'), '/');
        $this->token = trim(config('services.campay.token'));

        if (empty($this->token)) {
            Log::error('[CAMPAY] Token non configuré');
        }
    }

    /**
     * Initialiser un paiement (compatible avec l'interface Notchpay)
     * Utilise la méthode collect de Campay
     */
    public function initializePayment(array $data): array
    {
        $externalReference = $data['reference'] ?? $this->generateReference();
        $phoneNumber = $data['phone'] ?? null;

        if (!$phoneNumber) {
            return [
                'success' => false,
                'message' => 'Le numéro de téléphone est requis pour Campay',
            ];
        }

        $description = $data['description'] ?? 'Paiement Agora Coopérative';
        $amount = $data['amount'];

        Log::info('[CAMPAY] Initialisation paiement', [
            'reference' => $externalReference,
            'amount' => $amount,
            'phone' => $phoneNumber,
        ]);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Token {$this->token}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/collect/", [
                    'amount' => (string) $amount,
                    'currency' => 'XAF',
                    'from' => $phoneNumber,
                    'description' => $description,
                    'external_reference' => $externalReference,
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['reference'])) {
                Log::info('[CAMPAY] Paiement initié avec succès', ['reference' => $result['reference']]);
                
                return [
                    'success' => true,
                    'reference' => $externalReference,
                    'authorization_url' => null, // Campay n'a pas d'URL de redirection
                    'transaction' => [
                        'reference' => $result['reference'],
                        'status' => $result['status'] ?? 'PENDING',
                        'external_reference' => $externalReference,
                    ],
                ];
            }

            Log::error('[CAMPAY] Échec initialisation', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Erreur lors de l\'initialisation du paiement',
                'errors' => $result['errors'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('[CAMPAY] Exception lors de l\'initialisation', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erreur de connexion au service de paiement',
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Token {$this->token}",
                ])
                ->get("{$this->baseUrl}/transaction/{$reference}/");

            $result = $response->json();

            if ($response->successful() && isset($result['status'])) {
                return [
                    'success' => true,
                    'status' => $result['status'],
                    'transaction' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Transaction non trouvée',
            ];
        } catch (\Exception $e) {
            Log::error('[CAMPAY] Exception lors de la vérification', [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return [
                'success' => false,
                'message' => 'Erreur de vérification du paiement',
            ];
        }
    }

    /**
     * Valider la signature du webhook (Campay n'utilise pas de signature)
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        // Campay n'utilise pas de signature HMAC comme Notchpay
        // On peut valider par IP ou simplement accepter
        return true;
    }

    /**
     * Générer une référence unique
     */
    public function generateReference(string $prefix = 'PAY'): string
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }

    /**
     * Mapper le statut Campay vers notre statut interne
     */
    public function mapStatus(string $campayStatus): string
    {
        $mapping = [
            'SUCCESSFUL' => 'paye',
            'PENDING' => 'en_attente',
            'FAILED' => 'erreur',
            'CANCELLED' => 'annule',
            'EXPIRED' => 'annule',
        ];

        return $mapping[strtoupper($campayStatus)] ?? 'en_attente';
    }

    /**
     * Collecter de l'argent (Mobile Money -> Balance Campay)
     * Méthode originale conservée pour compatibilité
     */
    public function collect($amount, $phoneNumber, $description)
    {
        $externalReference = (string) Str::uuid();
        $url = "{$this->baseUrl}/collect/";

        Log::info('[CAMPAY-COLLECT] Nouvelle collecte', [
            'amount' => $amount,
            'phone' => $phoneNumber,
            'reference' => $externalReference,
        ]);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Token {$this->token}",
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'amount' => (string) $amount,
                    'currency' => 'XAF',
                    'from' => $phoneNumber,
                    'description' => $description,
                    'external_reference' => $externalReference,
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('[CAMPAY-COLLECT] Succès', $responseData);
            } else {
                Log::error('[CAMPAY-COLLECT] Erreur', [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::critical('[CAMPAY-COLLECT] Exception', [
                'message' => $e->getMessage(),
            ]);
            
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Retrait / Transfert (Payout)
     */
   /**
 * Retrait / Transfert (Payout)
 */
public function withdraw($amount, $description = "Transfert vers Admin")
{
    $adminPhone = config('services.campay.admin_phone');
    $externalReference = (string) Str::uuid();
    $url = "{$this->baseUrl}/withdraw/";

    Log::info('[CAMPAY-WITHDRAW] Tentative de retrait', [
        'amount' => $amount,
        'to' => $adminPhone,
    ]);

    try {
        // Suppression de connectTimeout() qui cause l'erreur
        $response = Http::withoutVerifying()
            ->timeout(60) 
            ->withHeaders([
                'Authorization' => "Token {$this->token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($url, [
                'amount' => (string) $amount,
                'to' => (string) $adminPhone,
                'currency' => 'XAF',
                'description' => $description,
                'external_reference' => $externalReference,
            ]);

        $data = $response->json();

        if ($response->successful()) {
            Log::info('[CAMPAY-WITHDRAW] Succès', [
                'reference' => $data['reference'] ?? 'N/A',
            ]);
            return $data;
        }

        Log::error('[CAMPAY-WITHDRAW] Échec', [
            'status' => $response->status(),
            'response' => $data,
        ]);
        
        return $data;
    } catch (\Exception $e) {
        Log::critical('[CAMPAY-WITHDRAW] Exception', [
            'message' => $e->getMessage(),
        ]);
        
        return [
            'success' => false,
            'message' => "Erreur de connexion au serveur Campay",
            'error_detail' => $e->getMessage(),
        ];
    }
}

    /**
     * Vérifier le statut d'une transaction
     */
    public function checkStatus($reference)
    {
        $url = "{$this->baseUrl}/transaction/{$reference}/";
        
        Log::info('[CAMPAY-STATUS] Vérification', ['reference' => $reference]);

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Token {$this->token}",
                ])
                ->get($url);

            $result = $response->json();
            
            Log::info('[CAMPAY-STATUS] Réponse', $result);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('[CAMPAY-STATUS] Exception', [
                'message' => $e->getMessage(),
            ]);
            
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}