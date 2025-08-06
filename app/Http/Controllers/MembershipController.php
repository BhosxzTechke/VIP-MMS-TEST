<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Models\Membership;
use App\Models\Transaction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class MembershipController extends Controller
{
    //

       public function showForm()
    {
        $user = Auth::user();
        
        if ($user->isVip()) {
            return redirect()->route('dashboard')->with('info', 'You are already a VIP member.');
        }

        $membershipPricing = [
            'gold' => [
                'price' => config('paymongo.membership_prices.gold') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.gold') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support'
                ]
            ],
            'platinum' => [
                'price' => config('paymongo.membership_prices.platinum') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.platinum') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Platinum member events'
                ]
            ],
            'diamond' => [
                'price' => config('paymongo.membership_prices.diamond') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.diamond') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Diamond member events',
                    'Personal account manager',
                    'Highest commission rates'
                ]
            ],
        ];

        return view('payment.upgrade', compact('user', 'membershipPricing'));
    }


    public function subscribe(Request $request)
    {
        $request->validate([
            'tier' => 'required|in:gold,platinum,diamond',
            'payment_method' => 'required|in:card,gcash,grab_pay,paymaya'
        ]);

        $membership_prices = config('paymongo.membership_prices');

        $amount = $membership_prices[$request->tier];

        $localTunnel = env('NGROK_TUNNEL');  // ← your unique ngrok link

        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => $amount,
                    'description' => ucfirst($request->tier) . ' Membership',
                    'remarks' => 'Membership Subscription',
                    'payment_method_types' => [$request->payment_method],
                    'redirect' => [
                        'success' => $localTunnel . '/membership/success?tier=' . $request->tier,
                        'failed' => $localTunnel . '/membership/failed',
                    ],
                    'metadata' => [
                        'user_id' => Auth::id(),
                        'tier' => $request->tier,
                    ],
                ]
            ]
        ];

            // Make the API request to PayMongo


            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])


                ->post('https://api.paymongo.com/v1/links', $payload);

            $checkoutUrl = $response['data']['attributes']['checkout_url'];
            if (!$checkoutUrl) {
                return back()->withErrors(['error' => 'Failed to create checkout session.']);
            }
            session(['selected_tier' => $request->tier]);

            return redirect($checkoutUrl);
    }



        public function success(Request $request)
        {
            return view('payment.successful')->with('message', 'We received your payment! If it doesn’t show right away, it will be confirmed shortly.');
        }

            public function failed()
            {
                return view('payment.failed');
            }


}
