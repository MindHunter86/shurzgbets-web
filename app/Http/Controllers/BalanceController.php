<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use Input;

class BalanceController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function  __destruct()
    {
    }

    private function convertBalance($balance, $valute, &$balanceSuffix, $revert = false) {
        if ($revert) {
            switch ($valute) {
                case 'points':
                    $balanceSuffix = 'points';
                    $balance = round($balance / config('webapi.pointsRate'), 2);
            }
        } else {
            switch ($valute) {
                case 'points':
                    $balanceSuffix = 'points';
                    $balance *= config('webapi.pointsRate');
            }
        }
        return $balance;
    }

    public function getBalance($steamid,$valute = NULL) {
        $user = DB::table('users')->where('steamid64',$steamid)->sharedLock()->first();
        if (is_null($user))
            return response()->json([
                'success' => false,
                'error_code' => 501,
                'error' => 'Unknown user'
            ]);
        $balance = $user->money;
        $balanceSuffix = 'р.';
        if (!is_null($valute)) {
            $balance = $this->convertBalance($balance, $valute, $balanceSuffix);
        }

        return response()->json([
            'success' => true,
            'balance' => $balance,
            'suffix' => $balanceSuffix
        ]);
    }
    public function updateBalance($steamid,$valute = NULL) {
        DB::beginTransaction();
        $balanceChange = Input::get('sum');
        $user = User::where('steamid64', $steamid)->lockForUpdate()->first();
        if (is_null($user))
            return response()->json([
                'success' => false,
                'error_code' => 501,
                'error' => 'Unknown user'
            ]);
        $balance = $user->money;
        $balanceSuffix = 'р.';
        if (!is_null($valute)) {
            $balanceChange = $this->convertBalance($balanceChange, $valute, $balanceSuffix, true);
        }
        $newBalance = $balance + $balanceChange;
        if ($newBalance < 0) {
            return response()->json([
                'success' => false,
                'error_code' => 502,
                'error' => 'Not enough money'
            ]);

        }

        $user->money = $newBalance;
        $user->save();

        if (!is_null($valute)) {
            $newBalance = $this->convertBalance($newBalance, $valute, $balanceSuffix);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'balance' => $newBalance,
            'suffix' => $balanceSuffix
        ]);
    }
}
