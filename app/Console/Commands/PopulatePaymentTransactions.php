<?php

namespace App\Console\Commands;

use App\Models\Payment\Payment;
use App\Models\Payment\PaymentTransaction;
use Illuminate\Console\Command;

class PopulatePaymentTransactions extends Command
{
    protected $signature = 'payments:populate-transactions';
    protected $description = 'Create payment transactions for existing payments to provide complete transaction history';

    public function handle()
    {
        $this->info('Populating payment transaction history...');

        $payments = Payment::whereDoesntHave('transactions')->get();

        if ($payments->isEmpty()) {
            $this->info('No payments found without transaction records.');
            return 0;
        }

        $this->info("Found {$payments->count()} payments without transaction records.");

        $bar = $this->output->createProgressBar($payments->count());
        $bar->start();

        foreach ($payments as $payment) {
            // Create initial payment transaction
            if ($payment->status !== 'pending') {
                // Map payment status to valid transaction status
                $transactionStatus = match($payment->status) {
                    'completed', 'refunded', 'partially_refunded' => 'completed',
                    'failed', 'cancelled' => $payment->status,
                    default => 'completed'
                };

                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'type' => 'payment',
                    'status' => $transactionStatus,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'gateway_transaction_id' => $payment->gateway_payment_id,
                    'description' => 'Payment processed via ' . $payment->method_display_name,
                    'gateway_response' => $payment->gateway_response,
                    'processed_at' => $payment->processed_at ?? $payment->created_at,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ]);
            }

            // Create refund transactions if there are any refunds
            if ($payment->refunded_amount > 0) {
                $refundedAmount = $payment->refunded_amount;

                // Create a single refund transaction with the total refunded amount
                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'type' => $refundedAmount >= $payment->amount ? 'refund' : 'partial_refund',
                    'status' => 'completed',
                    'amount' => $refundedAmount,
                    'currency' => $payment->currency,
                    'description' => 'Historical refund entry',
                    'reason' => 'Refund processed (historical data)',
                    'processed_at' => $payment->updated_at,
                    'created_at' => $payment->updated_at,
                    'updated_at' => $payment->updated_at
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('Payment transaction history populated successfully!');

        return 0;
    }
}