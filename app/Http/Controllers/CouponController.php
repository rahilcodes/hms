<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid promo code'
            ]);
        }

        if (!$coupon->isValidFor($request->amount)) {
            return response()->json([
                'valid' => false,
                'message' => 'This code is expired or requirements not met'
            ]);
        }

        return response()->json([
            'valid' => true,
            'discount' => $coupon->calculateDiscount($request->amount),
            'coupon_id' => $coupon->id
        ]);
    }
}
